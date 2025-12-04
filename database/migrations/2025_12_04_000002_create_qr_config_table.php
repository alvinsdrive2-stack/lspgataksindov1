<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qr_config', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('base_url');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default configs
        DB::table('qr_config')->insert([
            [
                'name' => 'production',
                'base_url' => 'https://barcode.lspgatensi.id/',
                'description' => 'Production QR verification URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'development',
                'base_url' => 'http://localhost:8000/qr/',
                'description' => 'Local development URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'staging',
                'base_url' => 'https://staging-barcode.lspgatensi.id/',
                'description' => 'Staging environment URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('qr_config');
    }
};