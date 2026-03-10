<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('channel');
            $table->string('title');
            $table->text('message');
            $table->string('related_entity_type')->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['related_entity_type', 'related_entity_id'], 'notifications_related_entity_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
