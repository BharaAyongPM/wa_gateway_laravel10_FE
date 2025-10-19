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
        Schema::create('messages', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();      // pemilik permintaan (jika dari web form/user)
            $t->unsignedBigInteger('device_id')->nullable();    // device pengirim (session WA)
            $t->string('sender')->nullable();                   // nomor WA pengirim (opsional; bisa diisi saat eksekusi)
            $t->string('recipient');                            // nomor/ID grup tujuan (62xxx atau group id)
            $t->enum('recipient_type', ['private', 'group'])->default('private');

            $t->enum('message_type', ['text', 'image', 'pdf', 'document'])->default('text');
            $t->text('content')->nullable();                    // teks pesan (caption untuk media)
            $t->string('media_url')->nullable();                // kalau image/pdf/dokumen

            $t->enum('status', ['queued', 'sent', 'failed', 'pending'])->default('queued');
            $t->string('provider')->nullable();                 // 'api','web','autoreply','broadcast'
            $t->string('error_message', 255)->nullable();       // jika failed
            $t->unsignedInteger('retry_count')->default(0);

            $t->timestamp('scheduled_at')->nullable();          // jika dijadwalkan
            $t->timestamp('sent_at')->nullable();

            $t->json('meta')->nullable();                       // catatan bebas (response gateway, dsb)
            $t->timestamps();

            $t->index(['device_id', 'status', 'created_at']);
            $t->index(['user_id', 'created_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
