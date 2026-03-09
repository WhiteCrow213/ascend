<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_employees', function (Blueprint $table) {

            $table->unsignedBigInteger('collegeID')->after('employment_status');

            $table->index('collegeID');

        });
    }

    public function down(): void
    {
        Schema::table('tbl_employees', function (Blueprint $table) {

            $table->dropIndex(['collegeID']);
            $table->dropColumn('collegeID');

        });
    }
};