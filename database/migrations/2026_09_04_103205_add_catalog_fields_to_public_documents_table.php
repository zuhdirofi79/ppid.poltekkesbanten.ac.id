<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_documents', function (Blueprint $table) {
            $table->string('section_title')->nullable()->after('public_information_category_id');
            $table->string('source_type', 32)->nullable()->after('year');
            $table->string('internal_url', 500)->nullable()->after('external_url');
            $table->unsignedInteger('sort_order')->default(0)->after('status');
            $table->index(['public_information_category_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('public_documents', function (Blueprint $table) {
            $table->dropIndex(['public_information_category_id', 'sort_order']);
            $table->dropColumn(['section_title', 'source_type', 'internal_url', 'sort_order']);
        });
    }
};
