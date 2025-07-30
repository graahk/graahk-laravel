<?php

use App\Models\Artist;
use App\Models\Card;
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
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->foreignId('artist_id')
                ->after('attachment_id')
                ->nullable()
                ->constrained('artists')
                ->onDelete('set null');
        });

        $artist = Artist::firstOrCreate([
            'name' => 'Ediwen',
            'slug' => 'ediwen',
        ]);
    
        Card::withoutGlobalScopes()
            ->whereNull('artist_id')
            ->update(['artist_id' => $artist->id]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['artist_id']);
            $table->dropColumn('artist_id');
        });
        
        Schema::dropIfExists('artists');
    }
};
