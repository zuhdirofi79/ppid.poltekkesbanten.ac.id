<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('information_service_summaries', function (Blueprint $table) {
            $table->unsignedInteger('provided_total')->nullable();
            $table->unsignedInteger('in_progress')->nullable();
            $table->json('period_breakdown')->nullable();
            $table->json('information_type_breakdown')->nullable();
            $table->string('source_document_path')->nullable();
            $table->json('analysis_paragraphs')->nullable();
            $table->json('improvement_points')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('information_service_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'provided_total',
                'in_progress',
                'period_breakdown',
                'information_type_breakdown',
                'source_document_path',
                'analysis_paragraphs',
                'improvement_points',
            ]);
        });
    }
};
