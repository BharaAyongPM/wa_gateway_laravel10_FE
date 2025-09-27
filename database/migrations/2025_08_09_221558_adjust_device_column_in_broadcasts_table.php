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
        Schema::table('broadcasts', function (Blueprint $t) {
            if (Schema::hasColumn('broadcasts', 'device')) {
                $t->renameColumn('device', 'device_old');
            }
            $t->unsignedBigInteger('device_id')->nullable()->after('id');
            $t->unsignedBigInteger('user_id')->nullable()->after('device_id');
        });
    }

    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $t) {
            $t->dropColumn(['device_id', 'user_id']);
            if (Schema::hasColumn('broadcasts', 'device_old')) {
                $t->renameColumn('device_old', 'device');
            }
        });
    }
};
