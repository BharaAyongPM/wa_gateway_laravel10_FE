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
        Schema::create('device_bot_features', function (Blueprint $table) {
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('feature_key'); // sama dengan bot_features.key
            $table->boolean('enabled')->default(false);
            $table->primary(['device_id', 'feature_key']);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('device_bot_features');
    }
};
