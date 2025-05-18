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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // opsional nama alias device
            $table->string('session_id')->unique(); // ID unik untuk sesi WA
            $table->enum('status', ['pending', 'connected', 'disconnected'])->default('pending');
            $table->text('qr_code')->nullable(); // base64 QR code terakhir
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
