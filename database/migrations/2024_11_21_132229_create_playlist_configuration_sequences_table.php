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
        Schema::create('playlist_configuration_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('options_id');
            $table->foreign('options_id')
                ->references('id')->on('playlist_configuration_options')
                ->onDelete('cascade');
            $table->unsignedBigInteger('playlist_id');
            $table->foreign('playlist_id')
                ->references('id')->on('playlists')
                ->onDelete('cascade');
            $table->text('configuration_instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_configuration_sequences');
    }
};
