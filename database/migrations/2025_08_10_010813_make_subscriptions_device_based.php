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
        Schema::table('subscriptions', function (Blueprint $t) {
            $t->unsignedBigInteger('device_id')->nullable()->after('id');
            $t->boolean('is_trial')->default(false)->after('remaining_quota');
            // indeks
            $t->index('device_id');
        });
    }
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $t) {
            $t->dropColumn(['device_id', 'is_trial']);
        });
    }
};
