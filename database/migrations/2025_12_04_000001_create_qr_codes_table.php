<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->unsignedBigInteger('verification_id');
            $table->enum('type', ['verifikator1', 'verifikator2', 'ketua_tuk', 'direktur', 'asesi', 'document']);
            $table->string('url');
            $table->enum('status', ['active', 'expired', 'used'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('verification_id')
                  ->references('id')
                  ->on('verifications')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Indexes
            $table->index('uuid');
            $table->index('verification_id');
            $table->index('status');
            $table->index('expires_at');
            $table->index(['type', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('qr_codes');
    }
};