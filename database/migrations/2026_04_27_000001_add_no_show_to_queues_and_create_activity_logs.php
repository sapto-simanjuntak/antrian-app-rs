<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah nilai no_show ke enum status pada tabel queues (MySQL)
        DB::statement("ALTER TABLE queues MODIFY COLUMN status
            ENUM('waiting','calling','paused','done','cancelled','no_show')
            NOT NULL DEFAULT 'waiting'");

        // Tabel audit log aksi operator
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 100);
            $table->tinyInteger('loket_id');
            $table->string('action', 30);       // panggil, selesai, batal, dst.
            $table->string('kode_antrian', 10)->nullable();
            $table->string('notes', 200)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['loket_id', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');

        DB::statement("ALTER TABLE queues MODIFY COLUMN status
            ENUM('waiting','calling','paused','done','cancelled')
            NOT NULL DEFAULT 'waiting'");
    }
};
