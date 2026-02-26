<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Student Number Sequences (per School Year prefix, e.g. 2526)
     *
     * Purpose:
     * - Guarantee unique, race-condition-safe incrementing numbers per AY prefix.
     * - No foreign keys (stability-first + avoids assuming school year table names).
     *
     * How it will be used later:
     * - Determine active AY prefix (e.g. 2526) from Utilities (active school year/term).
     * - Lock the sequence row for that prefix, increment last_number, then generate:
     *     stud_number = prefix + LPAD(last_number, 4, '0')
     *   Example: prefix=2526, last_number=1 => 25260001
     */
    public function up(): void
    {
        Schema::create('tbl_student_number_sequences', function (Blueprint $table) {
            $table->bigIncrements('seq_id');

            // AY prefix like "2526", stored as string to preserve format.
            $table->string('ay_prefix', 4)->unique();

            // Last issued running number for that AY (0001..9999).
            $table->unsignedInteger('last_number')->default(0);

            // Optional: if later you want to store a direct link to a school_year record.
            $table->unsignedBigInteger('school_year_id')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_student_number_sequences');
    }
};
