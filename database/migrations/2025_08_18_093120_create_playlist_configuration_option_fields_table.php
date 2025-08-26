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
        Schema::create('playlist_configuration_option_fields', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('option_id');
            $table->foreign('option_id')
                ->references('id')->on('playlist_configuration_options')
                ->onDelete('cascade');
            $table->json('config_fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_configuration_option_fields');
    }
};
