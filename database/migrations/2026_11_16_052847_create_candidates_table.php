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
        Schema::create('candidates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('election_id')->constrained()->cascadeOnDelete();
    $table->string('number', 8);
    $table->string('name');
    $table->string('running_mate')->nullable();
    $table->string('subtitle')->nullable();          // "XI MIPA 1 & XI IPS 2"
    $table->string('tagline')->nullable();
    $table->text('vision')->nullable();
    $table->json('missions')->nullable();
    $table->json('programs')->nullable();
    $table->string('photo_path')->nullable();
    $table->unsignedSmallInteger('sort_order')->default(0);
    $table->timestamps();

    $table->unique(['election_id', 'number']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
