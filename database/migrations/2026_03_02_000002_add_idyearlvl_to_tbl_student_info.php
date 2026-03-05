<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_student_info', function (Blueprint $table) {
            // Year Level (official academic state)
            $table->unsignedInteger('IDyearlvl')->nullable()->after('IDcurr');
            $table->index('IDyearlvl', 'idx_student_idyearlvl');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_student_info', function (Blueprint $table) {
            $table->dropIndex('idx_student_idyearlvl');
            $table->dropColumn('IDyearlvl');
        });
    }
};
