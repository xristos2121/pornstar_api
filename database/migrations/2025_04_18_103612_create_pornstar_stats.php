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
        Schema::create('pornstar_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pornstar_id')->constrained()->onDelete('cascade');
            $table->integer('subscriptions')->default(0);
            $table->integer('monthly_searches')->default(0);
            $table->integer('views')->default(0);
            $table->integer('videos_count')->default(0);
            $table->integer('premium_videos_count')->default(0);
            $table->integer('white_label_videos_count')->default(0);
            $table->integer('rank')->default(0);
            $table->integer('rank_premium')->default(0);
            $table->integer('rank_wl')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pornstar_stats');
    }
};
