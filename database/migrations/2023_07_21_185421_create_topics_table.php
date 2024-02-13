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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->integer('program_id')->unsigned();
            $table->string('name', 100);
            $table->text('description');
            $table->string('timeline', 10)->nullable();
            $table->json('reference_links')->nullable();
            $table->timestamps();
        });

        Schema::create('topic_technology', function (Blueprint $table) {
            $table->unsignedBigInteger('topic_id');
            $table->unsignedBigInteger('technology_id');

            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
            $table->foreign('technology_id')->references('id')->on('technologies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_technology');
        Schema::dropIfExists('topics');
    }
};
