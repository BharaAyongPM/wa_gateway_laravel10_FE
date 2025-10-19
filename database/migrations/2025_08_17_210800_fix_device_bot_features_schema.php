<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Normalisasi tipe kolom
        Schema::table('device_bot_features', function (Blueprint $table) {
            $table->boolean('enabled')->default(false)->nullable(false)->change();
        });

        // 2) Hapus index lama bila ada, lalu pasang unique baru
        // ganti nama index sesuai yang ada, contoh:
        // Schema::table('device_bot_features', fn(Blueprint $t) => $t->dropIndex('device_bot_features_device_id_feature_key_index'));
        // Atau aman: coba dropIfExists via DB::statement dalam try-catch

        // 3) Pasang unique
        Schema::table('device_bot_features', function (Blueprint $table) {
            $table->unique(['device_id', 'feature_key'], 'uniq_device_feature');
        });
    }

    public function down(): void
    {
        Schema::table('device_bot_features', function (Blueprint $table) {
            $table->dropUnique('uniq_device_feature');
        });
        // opsi: kembalikan tipe kolom, jika perlu
    }
};
