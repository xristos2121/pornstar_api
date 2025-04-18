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
        Schema::create('thumbnails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pornstar_id')->constrained()->onDelete('cascade');
            $table->integer('height');
            $table->integer('width');
            $table->enum('type', ['pc', 'mobile', 'tablet']);
            $table->timestamps();
        });

        Schema::create('thumbnail_urls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thumbnail_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('cached_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thumbnails');
        Schema::dropIfExists('thumbnail_urls');
    }
};
