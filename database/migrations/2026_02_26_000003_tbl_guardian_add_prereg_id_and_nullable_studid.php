<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_guardian', function (Blueprint $table) {
            // Allow guardians to be stored during pre-registration (no studID yet)
            $table->integer('studID')->nullable()->change();

            // Link guardians to prereg record first
            $table->unsignedBigInteger('prereg_id')->nullable()->after('studID')->index();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_guardian', function (Blueprint $table) {
            // Remove prereg link
            $table->dropIndex(['prereg_id']);
            $table->dropColumn('prereg_id');

            // Restore original constraint
            $table->integer('studID')->nullable(false)->change();
        });
    }
};
