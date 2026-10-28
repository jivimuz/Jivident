<?php

namespace Tests\Unit;

use App\Helpers\DatabaseBackupHelper;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseBackupHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_dump_includes_create_table_and_data_for_a_known_table(): void
    {
        $dump = app(DatabaseBackupHelper::class)->generateSqlDump();

        $this->assertStringContainsString('m_setting', $dump);
        $this->assertStringContainsString('CREATE TABLE', $dump);
        // settings table is seeded by migration, so there should be INSERT rows for it
        $this->assertStringContainsString('INSERT INTO `m_setting`', $dump);
    }

    public function test_dump_correctly_escapes_quotes_and_backslashes_in_values(): void
    {
        Setting::create([
            'name' => "O'Brien's \"Setting\" \\ test",
            'parameter' => 'tricky_value_test',
            'value' => "line1\nline2 with 'quote' and \\backslash",
            'type' => 'text',
        ]);

        $dump = app(DatabaseBackupHelper::class)->generateSqlDump();

        $this->assertStringContainsString('tricky_value_test', $dump);
        // Dump must not contain an unescaped standalone quote that would break the SQL string.
        $this->assertStringContainsString("\\'quote\\'", $dump);
        $this->assertStringContainsString('\\\\backslash', $dump);
    }

    public function test_generated_dump_can_be_replayed_into_a_fresh_database(): void
    {
        Setting::create([
            'name' => 'Replay Test',
            'parameter' => 'replay_test_param',
            'value' => 'replay_value',
            'type' => 'text',
        ]);

        $dump = app(DatabaseBackupHelper::class)->generateSqlDump();

        // Simulasikan restore: drop tabel setting, lalu jalankan ulang statement dump-nya.
        DB::statement('DROP TABLE IF EXISTS m_setting');

        $statements = array_filter(
            array_map('trim', $this->extractStatementsForTable($dump, 'm_setting')),
            fn ($s) => $s !== ''
        );

        foreach ($statements as $statement) {
            DB::unprepared($statement);
        }

        $this->assertDatabaseHas('m_setting', [
            'parameter' => 'replay_test_param',
            'value' => 'replay_value',
        ]);
    }

    public function test_null_values_are_dumped_as_sql_null_not_empty_string(): void
    {
        Setting::create([
            'name' => 'Null Value Test',
            'parameter' => 'null_value_test',
            'value' => null,
            'type' => 'text',
        ]);

        $dump = app(DatabaseBackupHelper::class)->generateSqlDump();

        $this->assertMatchesRegularExpression(
            "/'null_value_test', NULL,/",
            $dump
        );
    }

    /**
     * Ambil semua statement SQL (CREATE/INSERT) milik satu tabel dari dump teks,
     * dipisah per baris karena helper memang menulis satu statement per baris.
     */
    private function extractStatementsForTable(string $dump, string $table): array
    {
        $lines = explode("\n", $dump);

        return array_filter($lines, function ($line) use ($table) {
            // CREATE TABLE identifier quoting is dialect-specific (backtick on MySQL,
            // double-quote on SQLite) — just check the table name appears at all.
            return str_contains($line, $table) &&
                (str_starts_with(trim($line), 'CREATE TABLE') || str_starts_with(trim($line), 'INSERT INTO'));
        });
    }
}
