<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_student_info', function (Blueprint $table) {

            $table->tinyInteger('is_4ps')->unsigned()->default(0);
            $table->tinyInteger('is_magna_carta_poor')->unsigned()->default(0);
            $table->tinyInteger('is_single_parent')->unsigned()->default(0);
            $table->tinyInteger('is_indigenous')->unsigned()->default(0);
            $table->tinyInteger('is_pwd')->unsigned()->default(0);
            $table->tinyInteger('is_senior_citizen')->unsigned()->default(0);

            $table->string('indigenous_group_name', 100)->nullable();
            $table->text('seg_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_student_info', function (Blueprint $table) {
            $table->dropColumn([
                'is_4ps',
                'is_magna_carta_poor',
                'is_single_parent',
                'is_indigenous',
                'is_pwd',
                'is_senior_citizen',
                'indigenous_group_name',
                'seg_notes',
            ]);
        });
    }
};
