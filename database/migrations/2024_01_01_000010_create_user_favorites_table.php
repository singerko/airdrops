<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/2024_01_01_000010_create_user_favorites_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('airdrop_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'airdrop_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_favorites');
    }
};
