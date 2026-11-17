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
        Schema::create('ballots', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignId('election_id')->constrained()->cascadeOnDelete();
    $table->foreignId('candidate_id')->nullable()->constrained()->restrictOnDelete(); // null = suara kosong
    $table->string('receipt_code', 20)->unique();

    $table->index(['election_id', 'candidate_id']);  // untuk menghitung suara
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ballots');
    }
};
