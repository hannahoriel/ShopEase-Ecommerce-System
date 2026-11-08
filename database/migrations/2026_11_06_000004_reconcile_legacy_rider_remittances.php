<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $riderIds = DB::table('rider_remittances as rr')
                ->whereIn('rr.status', ['pending', 'discrepancy'])
                ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('rider_cod_remittance_items as i')
                    ->whereColumn('i.remittance_id', 'rr.id'))
                ->distinct()->pluck('rr.rider_id');

            foreach ($riderIds as $riderId) {
                $collections = DB::table('rider_cod_collections as c')
                    ->where('c.rider_id', $riderId)
                    ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('rider_cod_remittance_items as i')
                        ->whereColumn('i.cod_collection_id', 'c.id'))
                    ->orderBy('c.id')->get(['c.id', 'c.amount']);
                if ($collections->isEmpty()) continue;

                $due = round((float) $collections->sum('amount'), 2);
                $candidates = DB::table('rider_remittances')
                    ->where('rider_id', $riderId)->whereIn('status', ['pending', 'discrepancy'])
                    ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('rider_cod_remittance_items as i')
                        ->whereColumn('i.remittance_id', 'rider_remittances.id'))
                    ->orderBy('id')->lockForUpdate()->get();
                $matching = $candidates->filter(fn ($remittance) => abs(round((float) $remittance->amount, 2) - $due) < 0.009);
                $canonical = $matching->first();
                if (! $canonical) continue;

                $now = now();
                DB::table('rider_cod_remittance_items')->insert($collections->map(fn ($collection) => [
                    'remittance_id' => $canonical->id,
                    'cod_collection_id' => $collection->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all());

                $rider = DB::table('riders')->where('id', $riderId)->first(['logistics_branch_id', 'municipal_hub']);
                $hubId = $rider ? DB::table('logistics_delivery_hubs')
                    ->where('logistics_branch_id', $rider->logistics_branch_id)
                    ->where('hub_name', trim((string) $rider->municipal_hub))->where('status', 'active')->value('id') : null;
                DB::table('rider_remittances')->where('id', $canonical->id)->update([
                    'delivery_hub_id' => $hubId ?: $canonical->delivery_hub_id,
                    'updated_at' => $now,
                ]);

                foreach ($matching->skip(1) as $duplicate) {
                    DB::table('rider_remittances')->where('id', $duplicate->id)->update([
                        'status' => 'rejected',
                        'review_notes' => trim(($duplicate->review_notes ? $duplicate->review_notes.' ' : '')
                            .'Automatically rejected as a duplicate of remittance #'.$canonical->id.'; no COD collection was counted twice.'),
                        'reviewed_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Reconciliation changes historical records and is intentionally not reversed.
    }
};
