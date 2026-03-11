<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_sections', 'IDcurr')) {
                $table->unsignedBigInteger('IDcurr')->nullable()->after('program_id');
            }
        });

        // Backfill existing rows based on program_id → tbl_program.IDcurr
        if (
            Schema::hasTable('tbl_program') &&
            Schema::hasColumn('tbl_program', 'IDprogram') &&
            Schema::hasColumn('tbl_program', 'IDcurr')
        ) {
            DB::statement("
                UPDATE tbl_sections sec
                JOIN tbl_program prog
                    ON prog.IDprogram = sec.program_id
                SET sec.IDcurr = prog.IDcurr
                WHERE sec.IDcurr IS NULL
            ");
        }
    }

    public function down(): void
    {
        Schema::table('tbl_sections', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_sections', 'IDcurr')) {
                $table->dropColumn('IDcurr');
            }
        });
    }
};