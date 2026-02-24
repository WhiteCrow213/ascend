<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_student_info', function (Blueprint $table) {
            // Default Curriculum for the student (student truth)
            // Nullable for now to avoid breaking existing rows/workflows.
            $table->unsignedInteger('IDcurr')->nullable()->after('SecondProgramChoice');
            $table->index('IDcurr', 'idx_tbl_student_info_idcurr');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_student_info', function (Blueprint $table) {
            $table->dropIndex('idx_tbl_student_info_idcurr');
            $table->dropColumn('IDcurr');
        });
    }
};
