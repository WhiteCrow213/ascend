<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_sections', function (Blueprint $table) {
            $table->bigIncrements('section_id');

            // NOTE: We are NOT adding foreign key constraints yet to avoid assuming table names.
            // Add FK constraints later once your exact tables/PK columns are confirmed.
            $table->unsignedBigInteger('term_id');     // active term
            $table->unsignedBigInteger('program_id');  // academic program

            $table->unsignedTinyInteger('year_level'); // 1,2,3,4...
            $table->string('section_name', 100);       // e.g., Alpha, Beta

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Helpful indexes
            $table->index('term_id');
            $table->index('program_id');
            $table->index('year_level');

            // Prevent duplicates within the same term/program/year level
            $table->unique(['term_id', 'program_id', 'year_level', 'section_name'], 'uq_sections_term_program_year_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_sections');
    }
};
