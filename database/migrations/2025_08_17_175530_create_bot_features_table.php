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
        Schema::create('bot_features', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();   // ex: bantuan, domain_check, muslim_ai, pinterest, gpt, gpt_stop, ...
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('bot_features');
    }
};
