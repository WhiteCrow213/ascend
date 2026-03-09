<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_employees', function (Blueprint $table) {

            // Personal Info
            $table->string('FacultyLastName', 50)->nullable();
            $table->string('FacultyFirstName', 50)->nullable();
            $table->string('FacultyMiddleName', 50)->nullable();
            $table->string('FacultySuffixName', 10)->nullable();

            $table->date('birthdate')->nullable();
            $table->string('Religion', 50)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('email', 100)->nullable();

            $table->string('home_address', 255)->nullable();

            $table->string('civil_status', 20)->nullable();

            // Government IDs
            $table->string('prc_license_number', 50)->nullable();
            $table->string('pagibig_number', 50)->nullable();
            $table->string('tin_number', 50)->nullable();
            $table->string('gsis_number', 50)->nullable();

            // Employment Info
            $table->string('employment_type', 30)->nullable(); // Job Order, Permanent, Contractual
            $table->string('employment_status', 30)->nullable(); // Full time, Part time

            // Education
            $table->string('undergraduate_degree', 150)->nullable();
            $table->string('masters_degree', 150)->nullable();
            $table->string('doctoral_degree', 150)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('tbl_employees', function (Blueprint $table) {

            $table->dropColumn([
                'FacultyLastName',
                'FacultyFirstName',
                'FacultyMiddleName',
                'FacultySuffixName',
                'birthdate',
                'Religion',
                'contact_number',
                'email',
                'home_address',
                'civil_status',
                'prc_license_number',
                'pagibig_number',
                'tin_number',
                'gsis_number',
                'employment_type',
                'employment_status',
                'undergraduate_degree',
                'masters_degree',
                'doctoral_degree',
            ]);
        });
    }
};