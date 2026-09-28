<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Simplificar users: quitar role y onboarding_completed si existen
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('users', 'role')) {
                $columnsToDrop[] = 'role';
            }
            if (Schema::hasColumn('users', 'onboarding_completed')) {
                $columnsToDrop[] = 'onboarding_completed';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        // 2) Simplificar boards: quitar team_id si existe
        if (Schema::hasColumn('boards', 'team_id')) {
            Schema::table('boards', function (Blueprint $table) {
                $this->dropForeignIfExists('boards', 'team_id');
                $table->dropColumn('team_id');
            });
        }

        // 3) Simplificar shifts: quitar team_id, quitar FK de user_id y hacerlo NOT NULL
        if (Schema::hasColumn('shifts', 'team_id')) {
            $this->dropForeignIfExists('shifts', 'team_id');
            Schema::table('shifts', function (Blueprint $table) {
                $table->dropColumn('team_id');
            });
        }

        // user_id: quitar FK si existe, asegurar NOT NULL, dejar FK a users
        if (Schema::hasColumn('shifts', 'user_id')) {
            $this->dropForeignIfExists('shifts', 'user_id');

            // Rellenar nulls para poder poner NOT NULL
            DB::table('shifts')->whereNull('user_id')->update(['user_id' => 1]);

            // Hacer NOT NULL vía SQL nativo (evita el "add column" de ->change())
            DB::statement('ALTER TABLE shifts ALTER COLUMN user_id SET NOT NULL');

            // Volver a añadir la FK si no existe
            if (!$this->foreignKeyExists('shifts', 'user_id')) {
                Schema::table('shifts', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                });
            }
        }

        // 4) Borrar tablas que ya no se usan
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
    }

    public function down(): void
    {
        // One-way migration
    }

    /**
     * Dropea una FK si existe, sin fallar si no existe.
     */
    private function dropForeignIfExists(string $table, string $column): void
    {
        $constraint = $this->findForeignKeyName($table, $column);
        if ($constraint) {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT \"{$constraint}\"");
        }
    }

    /**
     * Busca el nombre real de la FK que apunta a la columna.
     */
    private function findForeignKeyName(string $table, string $column): ?string
    {
        $result = DB::selectOne("
            SELECT tc.constraint_name
            FROM information_schema.table_constraints tc
            JOIN information_schema.key_column_usage kcu
              ON tc.constraint_name = kcu.constraint_name
             AND tc.table_schema = kcu.table_schema
            WHERE tc.constraint_type = 'FOREIGN KEY'
              AND tc.table_name = ?
              AND kcu.column_name = ?
              AND tc.table_schema = 'public'
            LIMIT 1
        ", [$table, $column]);

        return $result->constraint_name ?? null;
    }

    private function foreignKeyExists(string $table, string $column): bool
    {
        return $this->findForeignKeyName($table, $column) !== null;
    }
};