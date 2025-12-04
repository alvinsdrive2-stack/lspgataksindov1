<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Skip if table doesn't exist yet
        if (!Schema::hasTable('barcodes')) {
            return;
        }

        Schema::table('barcodes', function (Blueprint $table) {
            $table->string('qr_uuid', 36)->nullable()->after('url');
            $table->enum('qr_type', ['verifikator1', 'verifikator2', 'ketua_tuk', 'direktur', 'document'])->nullable()->after('qr_uuid');
            $table->enum('qr_status', ['active', 'expired', 'used'])->default('active')->after('qr_type');
            $table->unsignedBigInteger('user_id')->nullable()->after('qr_status');

            // Index
            $table->index('qr_uuid');

            // Foreign key (commented out if barcodes table doesn't have users foreign key support)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('barcodes', function (Blueprint $table) {
            $table->dropIndex(['qr_uuid']);
            $table->dropColumn(['qr_uuid', 'qr_type', 'qr_status', 'user_id']);
        });
    }
};