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
        Schema::table('payments', function (Blueprint $t) {
            $t->string('order_no')->unique()->after('id');      // custom order_id kamu
            $t->foreignId('device_id')->after('user_id')->constrained()->cascadeOnDelete();
            $t->string('snap_token')->nullable()->after('amount');
            $t->json('midtrans_payload')->nullable()->after('payment_type');
        });
    }
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $t) {
            $t->dropColumn(['order_no', 'device_id', 'snap_token', 'midtrans_payload']);
        });
    }
};
