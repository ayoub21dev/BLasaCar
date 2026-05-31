<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('recipient_phone')->nullable()->after('message');
            $table->string('delivery_status')->nullable()->after('recipient_phone');
            $table->string('provider')->nullable()->after('delivery_status');
            $table->string('provider_message_id')->nullable()->after('provider');
            $table->timestamp('sent_at')->nullable()->after('provider_message_id');
            $table->timestamp('failed_at')->nullable()->after('sent_at');
            $table->text('delivery_error')->nullable()->after('failed_at');

            $table->index(['channel', 'delivery_status'], 'notifications_channel_delivery_idx');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_channel_delivery_idx');
            $table->dropColumn([
                'recipient_phone',
                'delivery_status',
                'provider',
                'provider_message_id',
                'sent_at',
                'failed_at',
                'delivery_error',
            ]);
        });
    }
};
