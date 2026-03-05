<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_section_offerings', function (Blueprint $table) {
            $table->bigIncrements('offering_id');

            // NOTE: We are NOT adding foreign key constraints yet to avoid assuming table names.
            // Add FK constraints later once your exact tables/PK columns are confirmed.
            $table->unsignedBigInteger('section_id');  // tbl_sections.section_id
            $table->unsignedBigInteger('subject_id');  // your subjects PK

            // Schedule fields (single meeting pattern per offering for now)
            $table->string('day_pattern', 20);         // e.g., MWF, TTH, SAT
            $table->time('time_start');
            $table->time('time_end');
            $table->string('room', 80)->nullable();

            // Seat limit (nullable for now; enforce required at UI/service layer later)
            $table->unsignedSmallInteger('student_limit')->nullable();

            // Instructor FK will be added later as requested
            // $table->unsignedBigInteger('instructor_id')->nullable();

            $table->timestamps();

            // Indexes + guardrails
            $table->index('section_id');
            $table->index('subject_id');

            // One subject should not appear twice in the same section offering list
            $table->unique(['section_id', 'subject_id'], 'uq_section_offerings_section_subject');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_section_offerings');
    }
};
