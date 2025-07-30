<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallet_nonces', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('nonce');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['address', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_nonces');
    }
};
