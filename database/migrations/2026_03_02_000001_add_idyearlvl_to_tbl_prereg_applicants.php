<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_prereg_applicants', function (Blueprint $table) {
            // Year Level reference (tbl_yearlevel.IDyearlvl)
            // Nullable because Transferees should start blank.
            $table->unsignedInteger('IDyearlvl')->nullable()->after('applicant_type');
            $table->index('IDyearlvl', 'idx_prereg_idyearlvl');
        });

        // Backfill existing rows safely based on applicant_type
        DB::table('tbl_prereg_applicants')
            ->whereIn('applicant_type', ['Freshman', 'freshman'])
            ->update(['IDyearlvl' => 1]); // 1 = First Year in tbl_yearlevel

        DB::table('tbl_prereg_applicants')
            ->whereIn('applicant_type', ['Transferee', 'transferee'])
            ->update(['IDyearlvl' => null]);
    }

    public function down(): void
    {
        Schema::table('tbl_prereg_applicants', function (Blueprint $table) {
            $table->dropIndex('idx_prereg_idyearlvl');
            $table->dropColumn('IDyearlvl');
        });
    }
};
