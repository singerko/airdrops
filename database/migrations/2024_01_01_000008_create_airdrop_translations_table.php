<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/2024_01_01_000008_create_airdrop_translations_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('airdrop_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airdrop_id')->constrained()->onDelete('cascade');
            $table->foreignId('language_id')->constrained();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->boolean('is_ai_generated')->default(false);
            $table->timestamp('translated_at')->nullable();
            $table->timestamps();

            $table->unique(['airdrop_id', 'language_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('airdrop_translations');
    }
};
