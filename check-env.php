<?php
// Check environment variables
echo "<h2>Environment Variables Check</h2>";
echo "<pre>";

echo "=== Using getenv() ===\n";
echo "MYSQL_HOST: " . (getenv('MYSQL_HOST') ?: 'NOT SET') . "\n";
echo "MYSQL_PORT: " . (getenv('MYSQL_PORT') ?: 'NOT SET') . "\n";
echo "MYSQL_USER: " . (getenv('MYSQL_USER') ?: 'NOT SET') . "\n";
echo "MYSQL_DATABASE: " . (getenv('MYSQL_DATABASE') ?: 'NOT SET') . "\n";
echo "MYSQL_PASSWORD: " . (getenv('MYSQL_PASSWORD') ? '***SET***' : 'NOT SET') . "\n\n";

echo "=== Using \$_ENV ===\n";
echo "MYSQL_HOST: " . ($_ENV['MYSQL_HOST'] ?? 'NOT SET') . "\n";
echo "MYSQL_PORT: " . ($_ENV['MYSQL_PORT'] ?? 'NOT SET') . "\n";
echo "MYSQL_USER: " . ($_ENV['MYSQL_USER'] ?? 'NOT SET') . "\n";
echo "MYSQL_DATABASE: " . ($_ENV['MYSQL_DATABASE'] ?? 'NOT SET') . "\n";
echo "MYSQL_PASSWORD: " . (isset($_ENV['MYSQL_PASSWORD']) ? '***SET***' : 'NOT SET') . "\n\n";

echo "=== Using \$_SERVER ===\n";
echo "MYSQL_HOST: " . ($_SERVER['MYSQL_HOST'] ?? 'NOT SET') . "\n";
echo "MYSQL_PORT: " . ($_SERVER['MYSQL_PORT'] ?? 'NOT SET') . "\n";
echo "MYSQL_USER: " . ($_SERVER['MYSQL_USER'] ?? 'NOT SET') . "\n";
echo "MYSQL_DATABASE: " . ($_SERVER['MYSQL_DATABASE'] ?? 'NOT SET') . "\n";
echo "MYSQL_PASSWORD: " . (isset($_SERVER['MYSQL_PASSWORD']) ? '***SET***' : 'NOT SET') . "\n\n";

echo "=== All Environment Variables ===\n";
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'MYSQL') !== false || strpos($key, 'DB_') !== false || strpos($key, 'RAILWAY') !== false) {
        if (strpos($key, 'PASSWORD') !== false) {
            echo "$key: ***HIDDEN***\n";
        } else {
            echo "$key: $value\n";
        }
    }
}

echo "</pre>";
?>
