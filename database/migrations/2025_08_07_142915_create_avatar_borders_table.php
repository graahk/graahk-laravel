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
        Schema::dropIfExists('alternate_art_user');

        Schema::create('user_unlocks', function (Blueprint $table) {
            $table->id();
            $table->morphs('unlock');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('enabled')->default(false);
        });

        Schema::create('avatar_borders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('attachment_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avatar_borders');
    }
};
