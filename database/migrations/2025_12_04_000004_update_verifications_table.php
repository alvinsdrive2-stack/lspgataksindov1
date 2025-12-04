<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->unsignedBigInteger('ketua_tuk_id')->nullable()->after('ketua_tuk');
            $table->unsignedBigInteger('direktur_id')->nullable()->after('approved');
            $table->string('direktur_name')->nullable()->after('direktur_id');

            // Indexes
            $table->index('ketua_tuk_id');
            $table->index('direktur_id');

            // Foreign keys (commented out if the users table doesn't exist or connection issues)
            // $table->foreign('ketua_tuk_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('direktur_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->dropIndex(['ketua_tuk_id']);
            $table->dropIndex(['direktur_id']);
            $table->dropColumn(['ketua_tuk_id', 'direktur_id', 'direktur_name']);
        });
    }
};