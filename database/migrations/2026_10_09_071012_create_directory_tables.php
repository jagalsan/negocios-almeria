<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'phone')) {
            Schema::table('users', fn (Blueprint $table) => $table->string('phone', 20)->nullable());
        }
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', fn (Blueprint $table) => $table->string('role')->default('user')->index());
        }
        if (! Schema::hasColumn('users', 'status')) {
            Schema::table('users', fn (Blueprint $table) => $table->string('status')->default('active')->index());
        }
        if (! Schema::hasColumn('users', 'terms_accepted_at')) {
            Schema::table('users', fn (Blueprint $table) => $table->timestamp('terms_accepted_at')->nullable());
        }
        if (! Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', fn (Blueprint $table) => $table->timestamp('last_login_at')->nullable());
        }

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_plural');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('postal_code_prefix', 5)->nullable();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 120);
            $table->string('name_normalized', 120)->unique();
            $table->string('slug', 140)->unique();
            $table->string('website_url')->nullable();
            $table->string('website_domain')->nullable()->unique();
            $table->string('cif', 9)->nullable()->unique();
            $table->string('status')->default('draft')->index();
            $table->string('origin')->default('admin');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_verified')->default(false);
            $table->string('short_description', 160);
            $table->text('description');
            $table->json('services')->nullable();
            $table->json('sectors')->nullable();
            $table->foreignId('municipality_id')->constrained()->restrictOnDelete();
            $table->string('address')->nullable();
            $table->string('postal_code', 5)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('phone_public', 20)->nullable();
            $table->string('email_public')->nullable();
            $table->json('opening_hours')->nullable();
            $table->json('social')->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('team_size')->nullable();
            $table->decimal('google_rating', 2, 1)->nullable();
            $table->unsignedInteger('google_reviews_count')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->json('gallery')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 160)->nullable();
            $table->boolean('seo_noindex')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('category_company', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->primary(['company_id', 'category_id']);
        });

        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('municipality_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('h1');
            $table->text('answer');
            $table->string('primary_keyword')->unique();
            $table->json('secondary_keywords')->nullable();
            $table->longText('intro');
            $table->json('criteria');
            $table->json('faq');
            $table->text('closing_text');
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 160)->nullable();
            $table->string('og_image_path')->nullable();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['category_id', 'municipality_id']);
        });

        Schema::create('ranking_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ranking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('position');
            $table->text('reason');
            $table->timestamps();
            $table->unique(['ranking_id', 'company_id']);
            $table->unique(['ranking_id', 'position']);
        });

        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ranking_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('slot');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->unsignedInteger('amount_cents');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_link')->nullable();
            $table->string('status')->default('scheduled')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ownerships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role_in_company');
            $table->string('phone', 20);
            $table->text('message')->nullable();
            $table->string('domain_token')->nullable();
            $table->timestamp('domain_verified_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'user_id', 'status']);
        });

        Schema::create('company_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('before');
            $table->json('after');
            $table->string('status')->default('applied');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('company_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('website_clicks')->default(0);
            $table->unsignedInteger('phone_clicks')->default(0);
            $table->timestamps();
            $table->unique(['company_id', 'date']);
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('reason');
            $table->text('message');
            $table->string('status')->default('pending');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body');
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 160)->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();
            $table->string('to_path');
            $table->unsignedSmallInteger('code')->default(301);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('company_stats');
        Schema::dropIfExists('company_changes');
        Schema::dropIfExists('claims');
        Schema::dropIfExists('ownerships');
        Schema::dropIfExists('sponsorships');
        Schema::dropIfExists('ranking_entries');
        Schema::dropIfExists('rankings');
        Schema::dropIfExists('category_company');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('municipalities');
        Schema::dropIfExists('categories');
    }
};
