<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            // Drop existing enum column
            $table->dropColumn('type');
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            // Add new enum with more comprehensive values
            $table->enum('type', [
                'verifikator1',
                'verifikator2',
                'verifikator_signature',
                'verifikator_paperless',
                'validator',
                'validator_signature',
                'validator_paperless',
                'ketua_tuk',
                'direktur',
                'asesi',
                'document'
            ])->after('verification_id');
        });
    }

    public function down()
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            // Restore original enum
            $table->enum('type', ['verifikator1', 'verifikator2', 'ketua_tuk', 'direktur', 'asesi', 'document'])->after('verification_id');
        });
    }
};