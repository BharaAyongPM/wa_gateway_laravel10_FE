<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // Status trial: 1 = trial aktif, 0 = tidak trial
            $table->boolean('is_trial')->default(true)->after('user_id');

            // Batas kuota pesan (opsional untuk tracking)
            $table->unsignedInteger('quota')->default(0)->after('is_trial');

            // Pesan terpakai dari kuota
            $table->unsignedInteger('messages_sent')->default(0)->after('quota');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['is_trial', 'quota', 'messages_sent']);
        });
    }
};
