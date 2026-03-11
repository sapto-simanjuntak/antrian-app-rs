<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('loket_id');                 // 1 = BPJS, 2 = Umum, 3 = Lansia
            $table->unsignedSmallInteger('nomor_antrian');
            $table->string('kode_antrian', 10);                      // e.g. "B001", "U002", "L003"
            $table->enum('status', [
                'waiting',   // menunggu dipanggil
                'calling',   // sedang dipanggil / dilayani
                'paused',    // ditunda
                'done',      // selesai dilayani
                'cancelled', // tidak hadir / dibatalkan
            ])->default('waiting');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->unsignedSmallInteger('service_duration')->nullable()->comment('seconds');
            $table->timestamps();

            // Prevent duplicate nomor antrian per loket per hari
            $table->index(['loket_id', 'nomor_antrian', 'created_at'], 'idx_loket_nomor_date');
            $table->index(['loket_id', 'status'], 'idx_loket_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
