<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_status')->default('active')->after('phone_verified');
            $table->timestamp('suspended_at')->nullable()->after('account_status');
            $table->index('account_status');
        });

        Schema::table('rides', function (Blueprint $table) {
            $table->text('admin_note')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn('admin_note');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['account_status']);
            $table->dropColumn(['account_status', 'suspended_at']);
        });
    }
};
