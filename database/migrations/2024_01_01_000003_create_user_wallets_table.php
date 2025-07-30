<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/2024_01_01_000003_create_user_wallets_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('blockchain_id')->constrained();
            $table->string('address');
            $table->string('wallet_type'); // metamask, phantom, keplr, etc.
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['blockchain_id', 'address']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_wallets');
    }
};
