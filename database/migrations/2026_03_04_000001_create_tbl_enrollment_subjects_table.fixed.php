<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tbl_enrollment_subjects')) {
            return;
        }

        Schema::create('tbl_enrollment_subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedInteger('IDsubj');
            $table->string('source', 20)->default('manual'); // manual | auto (future)
            $table->timestamps();

            $table->unique(['enrollment_id', 'IDsubj'], 'uq_enrollment_subject');
            $table->index('enrollment_id', 'idx_enrollment_subject_enrollment');
            $table->index('IDsubj', 'idx_enrollment_subject_idsubj');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_enrollment_subjects');
    }
};
