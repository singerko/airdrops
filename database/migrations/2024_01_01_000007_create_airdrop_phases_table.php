<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/2024_01_01_000007_create_airdrop_phases_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('airdrop_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airdrop_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Season 0, Season 1, etc.
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->json('requirements')->nullable();
            $table->string('reward_amount')->nullable();
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->enum('status', ['upcoming', 'active', 'ended', 'cancelled'])->default('upcoming');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('airdrop_phases');
    }
};
