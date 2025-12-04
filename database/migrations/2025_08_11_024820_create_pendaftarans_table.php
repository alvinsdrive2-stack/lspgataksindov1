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
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('wilayah');
            $table->string('pengusung');
            $table->string('provinsi')->nullable();
            $table->string('instansi')->nullable();
            $table->string('email_instansi')->nullable();
            $table->string('email');
            $table->string('referensi')->nullable();
            $table->string('tuk');
            $table->text('alamat');
            $table->string('pengaju');
            $table->string('telp_pengaju');
            $table->string('admin');
            $table->string('telp_admin');
            $table->date('tanggal_uji');
            $table->integer('jumlah_asesi');
            $table->enum('jenis_tuk', ['mandiri', 'sewaktu', 'ulang']);
            $table->string('dokumen_pengajuan');
            $table->string('dokumen_perjanjian')->nullable();
            $table->string('dokumentasi_foto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
