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
        Schema::create('alternate_arts', function (Blueprint $table) {
            $table->id();
            $table->string('extended_name')->nullable();
            $table->foreignId('card_id')->nullable()->constrained('cards')->nullOnDelete();
            $table->foreignId('artist_id')->nullable()->constrained('artists')->nullOnDelete();
            $table->json('attachments')->default('[]');
            $table->timestamps();
        });

        Schema::create('alternate_art_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternate_art_id')->constrained('alternate_arts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('enabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternate_art_user');
        Schema::dropIfExists('alternate_arts');
    }
};
