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
        Schema::create('playlist_configuration_schedule', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->time('run_at_time', precision: 0)->nullable();
            $table->json('days');
            $table->enum('frequency', ['minute', 'hourly', 'daily']);
            $table->timestamp('activated')->nullable();
            $table->unsignedBigInteger('playlist_id');
            $table->foreign('playlist_id')
                ->references('id')->on('playlists');
            $table->timestamp('last_run')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_configuration_schedule');
    }
};
