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
        Schema::create('search_resaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('search_id')->constrained()->cascadeOnDelete();
            $table->morphs("searchable");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_resaults');
    }
};
