<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_assessment_templates', function (Blueprint $table) {

            // Primary Key
            $table->bigIncrements('assessment_template_id');

            // Template Identity
            $table->string('template_name');

            // Scope
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('year_level_id')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();

            // Units
            $table->decimal('units',8,2)->default(0);

            // Tuition
            $table->decimal('tuition_fee',12,2)->default(0);
            $table->decimal('total_tuition',12,2)->default(0);

            // NSTP
            $table->decimal('nstp_units',8,2)->default(0);
            $table->decimal('nstp_fee',12,2)->default(0);

            // Misc Fees
            $table->decimal('athletic_fee',12,2)->default(0);
            $table->decimal('computer_fee',12,2)->default(0);
            $table->decimal('sociocultural_fee',12,2)->default(0);
            $table->decimal('guidance_fee',12,2)->default(0);
            $table->decimal('library_fee',12,2)->default(0);
            $table->decimal('medical_dental_fee',12,2)->default(0);
            $table->decimal('development_fee',12,2)->default(0);
            $table->decimal('registration_fee',12,2)->default(0);

            // Laboratory
            $table->decimal('laboratory_units',8,2)->default(0);
            $table->decimal('laboratory_fee',12,2)->default(0);

            // One-time Fees
            $table->decimal('entrance_exam_fee',12,2)->default(0);
            $table->decimal('admission_fee',12,2)->default(0);
            $table->decimal('handbook_fee',12,2)->default(0);
            $table->decimal('school_id_fee',12,2)->default(0);

            // Total
            $table->decimal('total_fees',12,2)->default(0);

            // Control
            $table->boolean('is_active')->default(true);
            $table->string('remarks')->nullable();

            $table->timestamps();

            // Performance indexes
            $table->index('program_id');
            $table->index('year_level_id');
            $table->index('term_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_assessment_templates');
    }
};