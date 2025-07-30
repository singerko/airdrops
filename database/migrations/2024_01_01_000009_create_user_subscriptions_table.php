<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/2024_01_01_000009_create_user_subscriptions_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('airdrop_id')->constrained()->onDelete('cascade');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(false);
            $table->json('notification_types')->nullable(); // new_phase, deadline_reminder, etc.
            $table->timestamps();

            $table->unique(['user_id', 'airdrop_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
