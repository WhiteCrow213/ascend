<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_student_studyload', function (Blueprint $table) {

            $table->unsignedBigInteger('student_id')->after('IDstudload');
            $table->unsignedBigInteger('offering_id')->after('student_id');
            $table->unsignedBigInteger('term_id')->after('offering_id');

            $table->timestamps();

            $table->index('student_id');
            $table->index('offering_id');
            $table->index('term_id');

        });
    }

    public function down(): void
    {
        Schema::table('tbl_student_studyload', function (Blueprint $table) {

            $table->dropColumn([
                'student_id',
                'offering_id',
                'term_id',
                'created_at',
                'updated_at'
            ]);

        });
    }
};