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
        Schema::create('participations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('election_id')->constrained()->cascadeOnDelete();
    $table->foreignId('voter_id')->constrained()->cascadeOnDelete();
    $table->string('token_hash');
    $table->timestamp('token_issued_at')->nullable();
    $table->boolean('has_voted')->default(false);
    $table->timestamp('voted_at')->nullable();
    $table->timestamps();

    $table->unique(['election_id', 'voter_id']);     // 1 pemilih = 1 kali per pemilihan
    $table->index(['election_id', 'has_voted']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participations');
    }
};
