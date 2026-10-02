<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lhkpn_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('position_key')->unique();
            $table->string('person_name');
            $table->string('position');
            $table->string('group_key', 32)->default('pimpinan')->index();
            $table->unsignedSmallInteger('report_year')->nullable();
            $table->string('document_label')->nullable();
            $table->text('notes')->nullable();
            $table->string('source_type', 32)->nullable();
            $table->foreignId('media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->string('public_asset_path')->nullable();
            $table->string('external_url')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lhkpn_documents');
    }
};
