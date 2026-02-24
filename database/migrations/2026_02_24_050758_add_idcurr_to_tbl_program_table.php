<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_program', function (Blueprint $table) {
            // Default Curriculum for the program (used only as student default)
            $table->unsignedInteger('IDcurr')->nullable()->after('department');
            $table->index('IDcurr', 'idx_tbl_program_idcurr');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_program', function (Blueprint $table) {
            $table->dropIndex('idx_tbl_program_idcurr');
            $table->dropColumn('IDcurr');
        });
    }
};
