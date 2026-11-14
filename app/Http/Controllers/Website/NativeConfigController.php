<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;

class NativeConfigController extends Controller
{
    /**
     * Show the native app configuration page.
     * This page is accessible without database connection.
     */
    public function index()
    {
        $currentConfig = $this->getCurrentConfig();
        $dbStatus = $this->checkDatabaseStatus();
        
        return view('pages.native-config.index', [
            'config' => $currentConfig,
            'dbStatus' => $dbStatus,
        ]);
    }

    /**
     * Test database connection with given config.
     */
    public function testConnection(Request $request)
    {
        $connectionType = $request->db_connection ?? 'mysql';

        if ($connectionType === 'sqlite') {
            try {
                $dbPath = database_path('database.sqlite');
                if (!file_exists($dbPath)) {
                    touch($dbPath);
                }
                $pdo = new \PDO("sqlite:{$dbPath}", null, null, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi SQLite berhasil!',
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi SQLite gagal: ' . $e->getMessage(),
                ], 400);
            }
        }

        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $pdo = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_database}",
                $request->db_username,
                $request->db_password ?? '',
                [
                    \PDO::ATTR_TIMEOUT => 5,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Koneksi database berhasil!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi gagal: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Save configuration to .env and test.
     */
    public function saveConfig(Request $request)
    {
        $connectionType = $request->db_connection ?? 'mysql';

        if ($connectionType === 'sqlite') {
            $request->validate([
                'auth_server' => 'nullable|url',
                'type_local_server' => 'required',
            ]);
        } else {
            $request->validate([
                'db_host' => 'required|string',
                'db_port' => 'required|integer|min:1|max:65535',
                'db_database' => 'required|string',
                'db_username' => 'required|string',
                'db_password' => 'nullable|string',
                'auth_server' => 'nullable|url',
                'type_local_server' => 'required',
            ]);
        }

        try {
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = File::get($envPath);
                
                // Update ONLY DB settings in .env (never write TYPE_LOCAL_SERVER or AUTH_SERVER to .env)
                $replacements = [
                    'DB_CONNECTION' => $connectionType,
                    'DB_HOST' => $connectionType === 'sqlite' ? '' : $request->db_host,
                    'DB_PORT' => $connectionType === 'sqlite' ? '' : $request->db_port,
                    'DB_DATABASE' => $connectionType === 'sqlite' ? database_path('database.sqlite') : $request->db_database,
                    'DB_USERNAME' => $connectionType === 'sqlite' ? '' : $request->db_username,
                    'DB_PASSWORD' => $connectionType === 'sqlite' ? '' : ($request->db_password ?? ''),
                ];
                
                foreach ($replacements as $key => $value) {
                    $pattern = "/^{$key}=.*/m";
                    $replacement = "{$key}={$value}";
                    if (preg_match($pattern, $envContent)) {
                        $envContent = preg_replace($pattern, $replacement, $envContent);
                    } else {
                        $envContent .= "\n{$replacement}";
                    }
                }
                
                // Remove legacy TYPE_LOCAL_SERVER and AUTH_SERVER from .env
                $envContent = preg_replace('/^TYPE_LOCAL_SERVER=.*\n?/m', '', $envContent);
                $envContent = preg_replace('/^AUTH_SERVER=.*\n?/m', '', $envContent);

                File::put($envPath, $envContent);
            }
            
            // Save configuration to JSON file in storage ("file buatan": storage/app/native_config.json)
            $configData = [
                'db_connection' => $connectionType,
                'db_host' => $connectionType === 'sqlite' ? '' : $request->db_host,
                'db_port' => $connectionType === 'sqlite' ? '' : $request->db_port,
                'db_database' => $connectionType === 'sqlite' ? database_path('database.sqlite') : $request->db_database,
                'db_username' => $connectionType === 'sqlite' ? '' : $request->db_username,
                'db_password' => $connectionType === 'sqlite' ? '' : ($request->db_password ?? ''),
                'auth_server' => $request->auth_server ?? 'https://management.antariks.id/api/license/validate',
                'type_local_server' => (int) $request->type_local_server,
            ];
            
            $configDir = storage_path('app');
            if (!file_exists($configDir)) {
                mkdir($configDir, 0755, true);
            }
            File::put(storage_path('app/native_config.json'), json_encode($configData, JSON_PRETTY_PRINT));

            // Write values to .env and immediately clear config cache so runtime picks it up
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            // Reload config based on connection type
            Config::set('database.default', $connectionType);
            if ($connectionType === 'sqlite') {
                Config::set('database.connections.sqlite.database', database_path('database.sqlite'));
            } else {
                Config::set('database.connections.mysql.host', $request->db_host);
                Config::set('database.connections.mysql.port', $request->db_port);
                Config::set('database.connections.mysql.database', $request->db_database);
                Config::set('database.connections.mysql.username', $request->db_username);
                Config::set('database.connections.mysql.password', $request->db_password ?? '');
            }
            Config::set('license.auth_server', $configData['auth_server']);
            Config::set('license.type_local_server', $configData['type_local_server']);
            
            // Purge connections
            DB::purge('mysql');
            DB::purge('sqlite');

            // Test new connection
            $testResult = $this->testConnectionWithConfig($request->all());
            
            if ($testResult['success']) {
                // Check if database needs migration/seeding
                $dbStatus = $this->checkDatabaseStatus();
                
                if ($dbStatus['needsSetup']) {
                    $migrateResult = $this->runMigrationsAndSeed();
                }

                // Ensure type_local_server and auth_server are set in m_setting
                try {
                    \App\Models\Setting::updateOrCreate(
                        ['parameter' => 'type_local_server'],
                        ['name' => 'Type Local Server', 'value' => (string)$request->type_local_server, 'type' => 'number']
                    );
                    \App\Models\Setting::updateOrCreate(
                        ['parameter' => 'auth_server'],
                        ['name' => 'Auth Server URL', 'value' => (string)($request->auth_server ?? 'https://management.antariks.id/api/license/validate'), 'type' => 'text']
                    );
                } catch (\Throwable $e) {
                    // Ignore
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Konfigurasi disimpan dan database diinisialisasi!',
                    'migrated' => true,
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi disimpan tapi koneksi gagal: ' . $testResult['message'],
            ], 400);
            
        } catch (Exception $e) {
            Log::error('Native config save error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error menyimpan konfigurasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run migrations and seeders.
     */
    public function runMigrationsAndSeed()
    {
        try {
            $output = [];
            
            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            $output['migrate'] = Artisan::output();
            
            // Run master seeders
            Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
            $output['seed_master'] = Artisan::output();
            
            // Run module seeder to ensure default user/admin access
            Artisan::call('db:seed', ['--class' => 'ModuleSeeder', '--force' => true]);
            $output['seed_modules'] = Artisan::output();
            
            // Run transactional seeders
            Artisan::call('db:seed', ['--class' => 'TransactionalSeeder', '--force' => true]);
            $output['seed_transactional'] = Artisan::output();
            
            return [
                'success' => true,
                'output' => $output,
            ];
        } catch (Exception $e) {
            Log::error('Migration/seed error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get current configuration from native_config.json or database.
     */
    private function getCurrentConfig(): array
    {
        $configPath = storage_path('app/native_config.json');
        $savedConfig = [];
        if (file_exists($configPath)) {
            try {
                $savedConfig = json_decode(file_get_contents($configPath), true) ?? [];
            } catch (\Throwable $e) {}
        }

        $typeLocalServer = $savedConfig['type_local_server'] ?? 0;
        $authServer = $savedConfig['auth_server'] ?? 'https://management.antariks.id/api/license/validate';

        try {
            $settingType = \App\Models\Setting::where('parameter', 'type_local_server')->first();
            if ($settingType && $settingType->value !== null) {
                $typeLocalServer = $settingType->value;
            }
        } catch (\Throwable $e) {
            // Fallback if table not ready
        }

        try {
            $settingAuth = \App\Models\Setting::where('parameter', 'auth_server')->first();
            if ($settingAuth && $settingAuth->value !== null) {
                $authServer = $settingAuth->value;
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return [
            'db_connection' => $savedConfig['db_connection'] ?? env('DB_CONNECTION', 'mysql'),
            'db_host' => $savedConfig['db_host'] ?? env('DB_HOST', '127.0.0.1'),
            'db_port' => $savedConfig['db_port'] ?? env('DB_PORT', 3306),
            'db_database' => $savedConfig['db_database'] ?? env('DB_DATABASE', 'jivident'),
            'db_username' => $savedConfig['db_username'] ?? env('DB_USERNAME', 'root'),
            'db_password' => $savedConfig['db_password'] ?? env('DB_PASSWORD', ''),
            'auth_server' => $authServer,
            'type_local_server' => (int) $typeLocalServer,
        ];
    }

    /**
     * Check database status - whether it needs setup.
     */
    private function checkDatabaseStatus(): array
    {
        try {
            // Try to connect
            DB::connection()->getPdo();
            
            $driver = DB::connection()->getDriverName();
            if ($driver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
            } else {
                $tables = DB::select("SHOW TABLES LIKE 'migrations'");
            }
            
            if (empty($tables)) {
                return [
                    'connected' => true,
                    'needsSetup' => true,
                    'message' => 'Database kosong, perlu migrasi dan seeding',
                ];
            }
            
            // Check if there are any migrated migrations
            $migrations = DB::table('migrations')->count();
            
            if ($migrations === 0) {
                return [
                    'connected' => true,
                    'needsSetup' => true,
                    'message' => 'Tabel migrations ada tapi kosong, perlu migrasi',
                ];
            }
            
            return [
                'connected' => true,
                'needsSetup' => false,
                'message' => "Database sudah terkonfigurasi ({$migrations} migrasi)",
            ];
            
        } catch (Exception $e) {
            return [
                'connected' => false,
                'needsSetup' => true,
                'message' => 'Tidak bisa koneksi ke database: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test connection with specific config.
     */
    private function testConnectionWithConfig(array $config): array
    {
        $connectionType = $config['db_connection'] ?? 'mysql';
        if ($connectionType === 'sqlite') {
            return ['success' => true];
        }

        try {
            $pdo = new \PDO(
                "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_database']}",
                $config['db_username'],
                $config['db_password'] ?? '',
                [
                    \PDO::ATTR_TIMEOUT => 5,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]
            );
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}