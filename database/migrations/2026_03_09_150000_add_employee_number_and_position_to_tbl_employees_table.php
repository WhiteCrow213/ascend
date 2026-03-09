<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_employees', function (Blueprint $table) {
            $table->string('employee_number', 50)->nullable()->after('IDemployees');
            $table->string('position', 100)->nullable()->after('collegeID');

            $table->index('employee_number');
            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_employees', function (Blueprint $table) {
            $table->dropIndex(['employee_number']);
            $table->dropIndex(['position']);

            $table->dropColumn('employee_number');
            $table->dropColumn('position');
        });
    }
};
