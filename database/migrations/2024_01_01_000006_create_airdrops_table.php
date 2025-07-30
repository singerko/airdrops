<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/2024_01_01_000006_create_airdrops_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('airdrops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('blockchain_id')->constrained();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('reward_amount')->nullable();
            $table->string('reward_token')->nullable();
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->enum('status', ['draft', 'upcoming', 'active', 'ended', 'cancelled'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->integer('priority')->default(0);
            $table->json('social_links')->nullable();
            $table->json('video_links')->nullable();
            $table->string('featured_image')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('participants_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('airdrops');
    }
};
