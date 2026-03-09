<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix foreign key references for tbl_sections:
     * - term_id     -> tbl_terms.term_id
     * - program_id  -> tbl_program.IDProgram
     * - year_level  -> tbl_yearlevel.IDyearlvl
     *
     * This migration:
     * 1) Drops any existing FKs on those columns (name-agnostic)
     * 2) Aligns local column types to referenced PK types (prevents MySQL FK type mismatch)
     * 3) Adds correct FKs with explicit names
     */
    public function up(): void
    {
        if (!Schema::hasTable('tbl_sections')) {
            return;
        }

        // 1) Drop existing FK constraints on these columns (name-agnostic)
        $this->dropForeignKeysByColumns('tbl_sections', ['term_id', 'program_id', 'year_level']);

        // 2) Align column types to referenced PK column types (avoids type/sign mismatch FK errors)
        $this->alignColumnTypeToReferencedPk('tbl_sections', 'term_id',     'tbl_terms',     'term_id');
        $this->alignColumnTypeToReferencedPk('tbl_sections', 'program_id',  'tbl_program',   'IDProgram');
        $this->alignColumnTypeToReferencedPk('tbl_sections', 'year_level',  'tbl_yearlevel', 'IDyearlvl');

        // 3) Add correct foreign keys with explicit names
        Schema::table('tbl_sections', function (Blueprint $table) {
            $table->foreign('term_id', 'fk_tbl_sections_term_id')
                ->references('term_id')->on('tbl_terms')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('program_id', 'fk_tbl_sections_program_id')
                ->references('IDProgram')->on('tbl_program')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('year_level', 'fk_tbl_sections_year_level')
                ->references('IDyearlvl')->on('tbl_yearlevel')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tbl_sections')) {
            return;
        }

        // Drop the explicit FKs if they exist (then fallback to name-agnostic)
        $this->dropForeignKeysByNames('tbl_sections', [
            'fk_tbl_sections_term_id',
            'fk_tbl_sections_program_id',
            'fk_tbl_sections_year_level',
        ]);

        $this->dropForeignKeysByColumns('tbl_sections', ['term_id', 'program_id', 'year_level']);
    }

    /**
     * Drops foreign keys on a table by scanning information_schema for the given columns.
     * This avoids relying on Laravel's constraint-name guessing.
     */
    private function dropForeignKeysByColumns(string $table, array $columns): void
    {
        $dbName = DB::getDatabaseName();

        $placeholders = implode(',', array_fill(0, count($columns), '?'));

        $sql = "
            SELECT DISTINCT CONSTRAINT_NAME AS constraint_name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
              AND COLUMN_NAME IN ($placeholders)
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ";

        $rows = DB::select($sql, array_merge([$dbName, $table], $columns));

        foreach ($rows as $row) {
            $constraint = $row->constraint_name ?? null;
            if ($constraint) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
            }
        }
    }

    /**
     * Drops foreign keys by specific constraint names if present.
     */
    private function dropForeignKeysByNames(string $table, array $constraintNames): void
    {
        $dbName = DB::getDatabaseName();

        foreach ($constraintNames as $name) {
            $sql = "
                SELECT COUNT(*) AS c
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = ?
                  AND TABLE_NAME = ?
                  AND CONSTRAINT_NAME = ?
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ";

            $exists = DB::selectOne($sql, [$dbName, $table, $name]);

            if ($exists && (int)($exists->c ?? 0) > 0) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
            }
        }
    }

    /**
     * Ensures the local column type matches the referenced PK column type.
     * We copy COLUMN_TYPE from the referenced PK column (includes UNSIGNED if applicable)
     * and apply it to the local column as NOT NULL.
     */
    private function alignColumnTypeToReferencedPk(
        string $localTable,
        string $localColumn,
        string $refTable,
        string $refPkColumn
    ): void
    {
        if (!Schema::hasColumn($localTable, $localColumn) || !Schema::hasColumn($refTable, $refPkColumn)) {
            return;
        }

        $dbName = DB::getDatabaseName();

        $sql = "
            SELECT COLUMN_TYPE
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
        ";

        $ref = DB::selectOne($sql, [$dbName, $refTable, $refPkColumn]);

        if (!$ref || empty($ref->COLUMN_TYPE)) {
            return;
        }

        $columnType = $ref->COLUMN_TYPE; // e.g. 'int(11) unsigned', 'bigint(20) unsigned'

        DB::statement("ALTER TABLE `{$localTable}` MODIFY `{$localColumn}` {$columnType} NOT NULL");
    }
};
