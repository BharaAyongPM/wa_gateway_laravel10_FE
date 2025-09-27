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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price')->default(0); // dalam rupiah
            $table->integer('duration')->default(30); // dalam hari
            $table->integer('max_devices')->default(1);
            $table->integer('quota_limit')->nullable(); // untuk paket OTP
            $table->boolean('can_image')->default(false);
            $table->boolean('can_pdf')->default(false);
            $table->boolean('can_autoreply')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
