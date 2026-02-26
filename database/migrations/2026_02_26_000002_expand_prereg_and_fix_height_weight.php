<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * 1) Add prereg columns that currently live in tbl_student_info
         */
        Schema::table('tbl_prereg_applicants', function (Blueprint $table) {

            // Personal Information
            $table->string('FirstName', 50)->nullable();
            $table->string('MidName', 50)->nullable();
            $table->string('LastName', 50)->nullable();
            $table->string('Suffix', 10)->nullable();
            $table->string('ContactNo', 50)->nullable();
            $table->date('Birthdate')->nullable();
            $table->string('place_of_birth', 255)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('Gender', 10)->nullable();
            $table->string('Citizenship', 20)->nullable();
            $table->string('CivilStatus', 20)->nullable();
            $table->string('Religion', 50)->nullable();

            // Height & Weight (proper numeric types)
            $table->decimal('Height', 5, 2)->nullable();
            $table->decimal('Weight', 5, 2)->nullable();

            $table->string('Bloodtype', 10)->nullable();

            // Academic Background
            $table->string('PrimarySchool', 100)->nullable();
            $table->string('PrimarySchool_Address', 100)->nullable();
            $table->string('YearGradPrimary', 50)->nullable();
            $table->string('SecondarySchool', 100)->nullable();
            $table->string('SecondarySchool_Address', 100)->nullable();
            $table->string('YearGradSecondary', 50)->nullable();
            $table->string('LastSchoolAttended', 100)->nullable();

            // Location Codes
            $table->char('region_psgc', 10)->nullable();
            $table->char('province_psgc', 10)->nullable();
            $table->char('citymun_psgc', 10)->nullable();
            $table->char('brgy_psgc', 10)->nullable();

            // Media
            $table->string('profile_photo_path', 255)->nullable();
        });

        /**
         * 2) Modify Height & Weight in tbl_student_info
         *    (from varchar → decimal)
         */
        Schema::table('tbl_student_info', function (Blueprint $table) {
            $table->decimal('Height', 5, 2)->nullable()->change();
            $table->decimal('Weight', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_student_info', function (Blueprint $table) {
            $table->string('Height', 50)->nullable()->change();
            $table->string('Weight', 50)->nullable()->change();
        });

        Schema::table('tbl_prereg_applicants', function (Blueprint $table) {

            $table->dropColumn([
                'FirstName','MidName','LastName','Suffix','ContactNo',
                'Birthdate','place_of_birth','email','Gender',
                'Citizenship','CivilStatus','Religion',
                'Height','Weight','Bloodtype',
                'PrimarySchool','PrimarySchool_Address','YearGradPrimary',
                'SecondarySchool','SecondarySchool_Address','YearGradSecondary',
                'LastSchoolAttended',
                'region_psgc','province_psgc','citymun_psgc','brgy_psgc',
                'profile_photo_path'
            ]);
        });
    }
};
