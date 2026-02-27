<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_program', function (Blueprint $table) {
            // Stability rule: do NOT drop/rename existing columns here.
            // New canonical relationship column:
            $table->unsignedBigInteger('collegeID')->nullable()->after('department');

            // Optional FK (enable later if you want strict FK constraints):
            // $table->foreign('collegeID')->references('collegeID')->on('tbl_colleges');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_program', function (Blueprint $table) {
            // If you later add FK, drop it first:
            // $table->dropForeign(['collegeID']);
            $table->dropColumn('collegeID');
        });
    }
};
