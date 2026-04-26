<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'operator'])->default('operator')->after('email');
            $table->unsignedTinyInteger('loket_id')->nullable()->after('role')
                ->comment('1=BPJS, 2=Umum, 3=Lansia — null jika admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'loket_id']);
        });
    }
};
