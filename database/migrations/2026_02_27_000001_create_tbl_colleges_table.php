<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_colleges', function (Blueprint $table) {
            $table->bigIncrements('collegeID');
            $table->string('college_code', 20)->unique();
            $table->string('college_name', 150);
            $table->timestamps();
        });

        // Seed default colleges (locked IDs as requested)
        DB::table('tbl_colleges')->insert([
            [
                'collegeID' => 1,
                'college_code' => 'CTE',
                'college_name' => 'College of Teacher Education',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collegeID' => 2,
                'college_code' => 'CCJE',
                'college_name' => 'College of Criminal Justice Education',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_colleges');
    }
};
