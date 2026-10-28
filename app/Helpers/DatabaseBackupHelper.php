<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * Pure-PHP full database dump (no shell_exec/mysqldump) — needed because
 * shared hosting commonly disables shell_exec. Streams output table-by-table
 * via a DB cursor (not ->get()) since some tables here (seeded address data)
 * have tens of thousands of rows and would otherwise risk exhausting the
 * PHP memory limit typical on shared hosting.
 */
final class DatabaseBackupHelper
{
    /**
     * Echoes the full SQL dump directly (for use inside streamDownload).
     */
    public function streamSqlDump(): void
    {
        $driver = DB::connection()->getDriverName();

        echo "-- Jivident database backup\n";
        echo '-- Generated at '.now()->toDateTimeString()."\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($this->getTableNames($driver) as $table) {
            $this->streamTable($table, $driver);
        }

        echo "SET FOREIGN_KEY_CHECKS=1;\n";
    }

    /**
     * Same as streamSqlDump() but returns a string — only meant for tests
     * against small datasets, not for real use (loads everything in memory).
     */
    public function generateSqlDump(): string
    {
        ob_start();
        $this->streamSqlDump();

        return ob_get_clean();
    }

    private function getTableNames(string $driver): array
    {
        if ($driver === 'sqlite') {
            return DB::table('sqlite_master')
                ->where('type', 'table')
                ->where('name', 'not like', 'sqlite_%')
                ->pluck('name')
                ->all();
        }

        return array_map(
            fn ($row) => array_values((array) $row)[0],
            DB::select('SHOW TABLES')
        );
    }

    private function streamTable(string $table, string $driver): void
    {
        echo "-- ----------------------------\n";
        echo "-- Table: {$table}\n";
        echo "-- ----------------------------\n";
        echo "DROP TABLE IF EXISTS `{$table}`;\n";
        echo $this->getCreateStatement($table, $driver).";\n\n";

        foreach (DB::table($table)->cursor() as $row) {
            echo $this->rowToInsert($table, (array) $row)."\n";
        }

        echo "\n";
    }

    private function getCreateStatement(string $table, string $driver): string
    {
        if ($driver === 'sqlite') {
            $row = DB::selectOne("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ?", [$table]);

            return $row->sql ?? '';
        }

        $row = (array) DB::selectOne("SHOW CREATE TABLE `{$table}`");

        return $row['Create Table'] ?? (string) reset($row);
    }

    private function rowToInsert(string $table, array $row): string
    {
        $columns = array_keys($row);
        $values = array_map(fn ($v) => $this->quoteValue($v), array_values($row));

        return "INSERT INTO `{$table}` (`".implode('`, `', $columns).'`) VALUES ('.implode(', ', $values).');';
    }

    private function quoteValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return "'".str_replace(['\\', "'"], ['\\\\', "\\'"], (string) $value)."'";
    }
}
