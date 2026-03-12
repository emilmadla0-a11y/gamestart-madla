<?php
// ============================================================
//  GAME START! — Database Connection
//  XAMPP defaults: host=localhost, user=root, pass='', db name below
// ============================================================

$host   = 'localhost';
$dbname = 'gamestart';   // <-- change this to your DB name
$user   = 'root';
$pass   = '';            // <-- change if you set a root password

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Show a friendly error page instead of exposing credentials
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><title>DB Error</title>
    <style>body{font-family:monospace;background:#050810;color:#ff006e;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}
    .box{border:1px solid #ff006e;padding:40px;max-width:500px;text-align:center;}
    h2{color:#00f5ff;margin-bottom:16px;}p{color:#8892b0;line-height:1.6;}</style></head>
    <body><div class="box"><h2>// DB CONNECTION ERROR</h2>
    <p>Could not connect to the database.<br>Check your credentials in <code>includes/db.php</code></p>
    <p style="font-size:11px;margin-top:16px;color:#ff006e;">' . htmlspecialchars($e->getMessage()) . '</p>
    </div></body></html>';
    exit;
}
