<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_prereg_applicants', function (Blueprint $table) {
            $table->bigIncrements('prereg_id');

            // Applicant Identity
            $table->string('ApplicantNum', 50)->unique();

            // Application Workflow
            $table->string('application_status', 20)->default('pending'); // pending / approved / rejected
            $table->string('applicant_type', 50)->nullable(); // Freshman / Transferee / etc.

            // Program Choices
            $table->string('FirstProgramChoice', 100)->nullable();
            $table->string('SecondProgramChoice', 100)->nullable();

            // Link to student once promoted
            $table->unsignedBigInteger('studID')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_prereg_applicants');
    }
};