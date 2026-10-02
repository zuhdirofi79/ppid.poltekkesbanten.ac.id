<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('academic_calendars')) {
            if (! Schema::hasColumn('academic_calendars', 'mime_type')) {
                Schema::table('academic_calendars', function (Blueprint $table) {
                    $table->string('mime_type')->nullable();
                });
            }

            if (! Schema::hasColumn('academic_calendars', 'extension')) {
                Schema::table('academic_calendars', function (Blueprint $table) {
                    $table->string('extension', 10)->nullable();
                });
            }

            return;
        }

        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedSmallInteger('year')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('source_type')->nullable()->comment('uploaded_file, external_url, public_asset');
            $table->foreignId('media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('file_path')->nullable()->comment('For uploaded files via storage');
            $table->string('public_asset_path')->nullable()->comment('For files already in public folder e.g. docs/file.pdf');
            $table->string('external_url')->nullable();
            $table->string('mime_type')->nullable()->comment('application/pdf, image/png, image/jpeg');
            $table->string('extension', 10)->nullable()->comment('pdf, png, jpg, jpeg');
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_published', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_calendars');
    }
};
