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
        Schema::table('alternate_arts', function (Blueprint $table) {
            $table->boolean('in_packs')->default(false)->after('artist_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alternate_arts', function (Blueprint $table) {
            $table->dropColumn('in_packs');
        });
    }
};
