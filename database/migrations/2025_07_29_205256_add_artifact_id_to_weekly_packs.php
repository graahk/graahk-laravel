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
        Schema::table('weekly_packs', function (Blueprint $table) {
            $table->foreignId('artifact_id')
                ->nullable()
                ->after('attachment_id')
                ->constrained('cards')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_packs', function (Blueprint $table) {
            $table->dropForeign(['artifact_id']);
            $table->dropColumn('artifact_id');
        });
    }
};
