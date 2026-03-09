<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_section_offerings', function (Blueprint $table) {
            $table->unsignedBigInteger('instructor_id')
                  ->nullable()
                  ->after('room');

            $table->index('instructor_id');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_section_offerings', function (Blueprint $table) {
            $table->dropIndex(['instructor_id']);
            $table->dropColumn('instructor_id');
        });
    }
};
