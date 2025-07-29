<?php
// database/migrations/2024_01_01_000000_create_languages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('name');
            $table->string('native_name');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('languages');
    }
};

// database/migrations/2024_01_01_000001_create_blockchains_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('blockchains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->string('explorer_url')->nullable();
            $table->string('token_standard')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blockchains');
    }
};

// database/migrations/2024_01_01_000002_create_users_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('avatar')->nullable();
            $table->string('country')->nullable();
            $table->enum('theme', ['light', 'dark', 'auto'])->default('auto');
            $table->string('accent_color', 7)->default('#3B82F6');
            $table->json('notification_settings')->nullable();
            $table->json('preferred_blockchains')->nullable();
            $table->boolean('force_password_change')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};

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

// database/migrations/2024_01_01_000004_create_airdrop_categories_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('airdrop_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('airdrop_categories');
    }
};

// database/migrations/2024_01_01_000005_create_projects_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->string('twitter')->nullable();
            $table->string('discord')->nullable();
            $table->string('telegram')->nullable();
            $table->string('github')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('airdrop_categories');
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};

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

// database/migrations/2024_01_01_000011_create_notifications_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};

// database/migrations/2024_01_01_000012_create_social_logins_table.php

return new class extends Migration
{
    public function up()
    {
        Schema::create('social_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // google, facebook, twitter
            $table->string('provider_id');
            $table->string('provider_email')->nullable();
            $table->json('provider_data')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('social_logins');
    }
};
