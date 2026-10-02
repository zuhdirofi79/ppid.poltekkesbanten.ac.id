<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('disk', 32);
            $table->string('path');
            $table->string('mime_type', 127);
            $table->string('extension', 16);
            $table->unsignedBigInteger('size');
            $table->char('checksum', 64)->index();
            $table->string('title')->nullable();
            $table->string('caption', 500)->nullable();
            $table->string('alt_text')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visibility', 16)->default('internal')->index();
            $table->timestamps();
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_asset_id')->constrained('media_assets')->cascadeOnDelete();
            $table->morphs('attachable');
            $table->string('display_title')->nullable();
            $table->string('description', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('visibility', 16)->default('public')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('saq_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->unsignedSmallInteger('year')->default(2026)->index();
            $table->string('section');
            $table->string('subsection')->nullable();
            $table->string('question');
            $table->text('evidence_requirement')->nullable();
            $table->string('recommended_content_type', 32)->nullable();
            $table->string('recommended_public_route')->nullable();
            $table->string('answer', 16)->default('not_set');
            $table->string('status', 32)->default('missing')->index();
            $table->text('notes')->nullable();
            $table->boolean('requires_public')->default(true);
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('saq_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saq_item_id')->constrained('saq_items')->cascadeOnDelete();
            $table->string('evidence_type', 32);
            $table->foreignId('media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->foreignId('public_document_id')->nullable()->constrained('public_documents')->nullOnDelete();
            $table->string('external_url', 500)->nullable();
            $table->string('title')->nullable();
            $table->string('description', 500)->nullable();
            $table->string('visibility', 16)->default('public')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('information_service_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->unsignedInteger('total_received')->nullable();
            $table->unsignedInteger('fulfilled_full')->nullable();
            $table->unsignedInteger('fulfilled_partial')->nullable();
            $table->unsignedInteger('rejected')->nullable();
            $table->decimal('average_response_days', 8, 2)->nullable();
            $table->text('response_time_description')->nullable();
            $table->text('rejection_reasons')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('information_service_summaries');
        Schema::dropIfExists('saq_evidences');
        Schema::dropIfExists('saq_items');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('media_assets');
    }
};
