<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('seller_messages')
            ->where('seed_key', 'like', 'seller-messages-%')
            ->orWhereIn('seed_key', ['demo-marketplace-message-1', 'demo-marketplace-message-2'])
            ->delete();

        DB::table('seller_conversations')
            ->where('seed_key', 'like', 'seller-messages-%')
            ->orWhere('seed_key', 'demo-marketplace-buyer-conversation')
            ->delete();

        DB::table('complaints')
            ->where('subject', 'like', 'SE-DEMO-MESSAGE-COMPLAINT-%')
            ->delete();
    }

    public function down(): void
    {
    }
};
