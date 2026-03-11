<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_enrollment_subjects', function (Blueprint $table) {

            $table->unsignedBigInteger('offering_id')
                  ->after('enrollment_id');

            $table->index('offering_id');

        });
    }

    public function down(): void
    {
        Schema::table('tbl_enrollment_subjects', function (Blueprint $table) {

            $table->dropColumn('offering_id');

        });
    }
};
