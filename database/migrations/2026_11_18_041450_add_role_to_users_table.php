<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            // Kolom sudah ada (dari Modul 3): ubah agar mendukung 'admin' dan 'mahasiswa'
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'mahasiswa'])->default('mahasiswa')->change();
            });
        } else {
            // Kolom belum ada: buat baru
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'mahasiswa'])->default('mahasiswa')->after('password');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};