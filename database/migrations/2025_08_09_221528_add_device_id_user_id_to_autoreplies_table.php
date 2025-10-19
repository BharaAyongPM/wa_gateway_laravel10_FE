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
        Schema::table('auto_replies', function (Blueprint $t) {
            $t->unsignedBigInteger('device_id')->nullable()->after('id');
            $t->unsignedBigInteger('user_id')->nullable()->after('device_id');
        });
    }

    public function down(): void
    {
        Schema::table('autoreplies', function (Blueprint $t) {
            $t->dropColumn(['device_id', 'user_id']);
        });
    }
};
