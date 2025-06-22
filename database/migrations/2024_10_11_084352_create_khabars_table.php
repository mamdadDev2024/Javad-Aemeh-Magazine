<?php

use App\Models\Scope;
use App\Models\User;
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
        Schema::create('khabars', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Scope::class)->nullable()->constrained("scopes")->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');
            $table->string("pdf")->nullable();
            $table->string("image");
            $table->foreignIdFor(User::class)->constrained("users")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khabars');
    }
};
