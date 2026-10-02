<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leadership_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('position_key')->unique();
            $table->string('name');
            $table->string('credentials')->nullable();
            $table->string('position');
            $table->text('short_summary')->nullable();
            $table->text('detail_body')->nullable();
            $table->text('education')->nullable();
            $table->text('work_history')->nullable();
            $table->foreignId('photo_media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leadership_profiles');
    }
};
