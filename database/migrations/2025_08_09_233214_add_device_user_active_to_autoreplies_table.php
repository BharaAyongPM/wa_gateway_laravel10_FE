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

            $t->boolean('active')->default(true)->after('type');
        });
    }
    public function down(): void
    {
        Schema::table('auto_replies', function (Blueprint $t) {
            $t->dropColumn(['active']);
        });
    }
};
