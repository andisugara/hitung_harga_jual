<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('platform_variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->string('variable');
            $table->enum('type', ['percent', 'amount']);
            $table->decimal('value', 14, 2)->default(0);
            $table->timestamps();
        });

        DB::table('platforms')->insert([
            [
                'name' => 'Shopee',
                'slug' => 'shopee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TikTok',
                'slug' => 'tiktok',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pribadi',
                'slug' => 'pribadi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $platformIds = DB::table('platforms')->pluck('id', 'slug');

        DB::table('platform_variables')->insert([
            [
                'platform_id' => $platformIds['shopee'],
                'variable' => 'Biaya Admin',
                'type' => 'percent',
                'value' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform_id' => $platformIds['shopee'],
                'variable' => 'Pajak',
                'type' => 'percent',
                'value' => 0.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform_id' => $platformIds['tiktok'],
                'variable' => 'Biaya Admin',
                'type' => 'percent',
                'value' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform_id' => $platformIds['tiktok'],
                'variable' => 'Biaya Ads',
                'type' => 'percent',
                'value' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform_id' => $platformIds['pribadi'],
                'variable' => 'PPN',
                'type' => 'percent',
                'value' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_variables');
        Schema::dropIfExists('platforms');
    }
};
