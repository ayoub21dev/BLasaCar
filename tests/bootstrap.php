<?php

require __DIR__.'/../vendor/autoload.php';

$connection = $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: null;
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$port = (int) ($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306);

if ($connection !== 'mysql' || $host !== '127.0.0.1' || $port !== 3307 || PHP_OS_FAMILY !== 'Windows') {
    return;
}

$database = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'blasacar_test';
$username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'blassacar_app';
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';

if (! preg_match('/^[A-Za-z0-9_]+$/', $database) || ! preg_match('/^[A-Za-z0-9_]+$/', $username)) {
    throw new RuntimeException('Invalid test database or username for MySQL bootstrap.');
}

$mysqld = $_ENV['MYSQLD_PATH']
    ?? getenv('MYSQLD_PATH')
    ?: 'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqld.exe';

$dataDir = $_ENV['MYSQL_TEST_DATA_DIR']
    ?? getenv('MYSQL_TEST_DATA_DIR')
    ?: 'C:\\tmp\\blasacar-test-mysql-data';

$pidFile = $dataDir.'\\mysqld.pid';
$errorLog = $dataDir.'\\mysqld.err';

$quote = static fn (string $value): string => '"'.str_replace('"', '\\"', $value).'"';

$connect = static function (?string $db, string $user, string $pass) use ($host, $port): PDO {
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4".($db ? ";dbname={$db}" : '');

    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 1,
    ]);
};

$canUseConfiguredDatabase = static function () use ($connect, $database, $username, $password): bool {
    try {
        $connect($database, $username, $password);

        return true;
    } catch (Throwable) {
        return false;
    }
};

if (! $canUseConfiguredDatabase()) {
    if (! is_file($mysqld)) {
        throw new RuntimeException("mysqld.exe was not found at {$mysqld}.");
    }

    if (! is_dir($dataDir.'\\mysql')) {
        if (! is_dir($dataDir) && ! mkdir($dataDir, 0777, true) && ! is_dir($dataDir)) {
            throw new RuntimeException("Unable to create MySQL test data directory: {$dataDir}");
        }

        $output = [];
        $exitCode = 0;
        exec(
            $quote($mysqld).' --no-defaults --initialize-insecure --datadir='.$quote($dataDir).' --console 2>&1',
            $output,
            $exitCode,
        );

        if ($exitCode !== 0) {
            throw new RuntimeException("Unable to initialize MySQL test data directory:\n".implode("\n", $output));
        }
    }

    $serverReady = false;

    for ($attempt = 0; $attempt < 2; $attempt++) {
        try {
            $connect(null, 'root', '');
            $serverReady = true;
            break;
        } catch (Throwable) {
            pclose(popen(
                'cmd /C start "" /B '.$quote($mysqld)
                .' --no-defaults'
                .' --datadir='.$quote($dataDir)
                ." --port={$port}"
                ." --bind-address={$host}"
                .' --pid-file='.$quote($pidFile)
                .' --log-error='.$quote($errorLog)
                .' --console',
                'r',
            ));
        }

        for ($i = 0; $i < 30; $i++) {
            try {
                $connect(null, 'root', '');
                $serverReady = true;
                break 2;
            } catch (Throwable) {
                sleep(1);
            }
        }
    }

    if (! $serverReady) {
        $log = is_file($errorLog) ? implode('', array_slice(file($errorLog), -40)) : 'No MySQL error log found.';

        throw new RuntimeException("MySQL test server did not become ready.\n{$log}");
    }

    $root = $connect(null, 'root', '');
    $quotedPassword = $root->quote($password);

    $root->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $root->exec("CREATE USER IF NOT EXISTS '{$username}'@'127.0.0.1' IDENTIFIED BY {$quotedPassword}");
    $root->exec("GRANT ALL PRIVILEGES ON `{$database}`.* TO '{$username}'@'127.0.0.1'");
    $root->exec('FLUSH PRIVILEGES');
}
