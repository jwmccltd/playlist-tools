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
        Schema::create('playlist_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('option_id');
            $table->foreign('option_id')
                ->references('id')->on('playlist_configuration_options')
                ->onDelete('cascade');
            $table->unsignedBigInteger('playlist_id');
            $table->foreign('playlist_id')
                ->references('id')->on('playlists')
                ->onDelete('cascade');
            $table->text('config');
            $table->boolean('active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_configurations');
    }
};
