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
        Schema::create('pornstar_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pornstar_id')->constrained()->onDelete('cascade');
            $table->boolean('tattoos')->default(false);
            $table->boolean('piercings')->default(false);
            $table->boolean('breast_size')->default(false);
            $table->boolean('breast_type')->default(false);
            $table->string('orientation');
            $table->string('gender');
            $table->integer('age');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pornstar_attributes');
    }
};
