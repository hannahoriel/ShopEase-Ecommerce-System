<?php

namespace App\Models\Rider;

use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsBranch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'logistics_branch_id',
        'last_name',
        'first_name',
        'middle_initial',
        'sex',
        'contact_no',
        'birthday',
        'age',
        'province',
        'municipality',
        'barangay',
        'street',
        'house_number',
        'vehicle',
        'plate_number',
        'courier_company',
        'logistics_operation',
        'municipal_hub',
        'upload_or_cr',
        'upload_id_license',
        'registration_status',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'birthday' => 'date',
        'age' => 'integer',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logisticsBranch(): BelongsTo
    {
        return $this->belongsTo(LogisticsBranch::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function deliveryProofs(): HasMany
    {
        return $this->hasMany(DeliveryProof::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class);
    }

    public function codCollections(): HasMany
    {
        return $this->hasMany(CodCollection::class);
    }

    public function remittances(): HasMany
    {
        return $this->hasMany(Remittance::class);
    }

    public function earningEntries(): HasMany
    {
        return $this->hasMany(EarningEntry::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function calculations(): HasMany
    {
        return $this->hasMany(Calculation::class);
    }

    public function orderArchives(): HasMany
    {
        return $this->hasMany(OrderArchive::class);
    }

    public function deliveryActions(): HasMany
    {
        return $this->hasMany(DeliveryAction::class);
    }
}
