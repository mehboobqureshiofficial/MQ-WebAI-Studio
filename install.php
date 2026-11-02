<?php
// One-time installer. Creates tables, sample content and the admin login, then writes config.php.
const CFG = __DIR__ . '/config.php';
date_default_timezone_set('Asia/Karachi');
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$done = false; $err = ''; $manual = '';
$v = ['host' => 'localhost', 'name' => '', 'user' => 'root', 'pass' => ''];

if (is_file(CFG)) {
    exit('<!DOCTYPE html><meta charset="utf-8"><body style="font-family:sans-serif;max-width:560px;margin:60px auto"><h2>Already installed ✅</h2>'
       . '<p>Security ke liye <b>install.php</b> file server se delete kar dein. Reinstall karna ho to pehle <code>config.php</code> delete karein.</p><p><a href="admin/login.php">Admin Login →</a></p>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    try {
        if ($v['name'] === '' || !preg_match('/^[A-Za-z0-9_]+$/', $v['name'])) throw new Exception('Database name sirf letters, numbers aur _ ho sakta hai.');
        $opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
        try {
            $pdo = new PDO("mysql:host={$v['host']};dbname={$v['name']};charset=utf8mb4", $v['user'], $v['pass'], $opt);
        } catch (PDOException $x) {                                   // database missing (e.g. XAMPP): try to create it
            $tmp = new PDO("mysql:host={$v['host']};charset=utf8mb4", $v['user'], $v['pass'], $opt);
            $tmp->exec("CREATE DATABASE IF NOT EXISTS `{$v['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = new PDO("mysql:host={$v['host']};dbname={$v['name']};charset=utf8mb4", $v['user'], $v['pass'], $opt);
        }
        $T = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
        foreach ([
            "CREATE TABLE IF NOT EXISTS admins(id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(60) NOT NULL UNIQUE, pass_hash VARCHAR(255) NOT NULL) $T",
            "CREATE TABLE IF NOT EXISTS settings(skey VARCHAR(60) PRIMARY KEY, sval MEDIUMTEXT) $T",
            "CREATE TABLE IF NOT EXISTS posts(id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, slug VARCHAR(190) NOT NULL UNIQUE, excerpt TEXT, content MEDIUMTEXT, cover VARCHAR(255) NOT NULL DEFAULT '', status ENUM('published','draft') NOT NULL DEFAULT 'draft', created_at INT NOT NULL, updated_at INT NOT NULL, KEY(status, created_at)) $T",
            "CREATE TABLE IF NOT EXISTS items(id INT AUTO_INCREMENT PRIMARY KEY, kind VARCHAR(20) NOT NULL, title VARCHAR(255) NOT NULL, sub VARCHAR(255) NOT NULL DEFAULT '', body TEXT, extra VARCHAR(255) NOT NULL DEFAULT '', sort_order INT NOT NULL DEFAULT 0, KEY(kind, sort_order)) $T",
            "CREATE TABLE IF NOT EXISTS messages(id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80), email VARCHAR(120), company VARCHAR(80), type VARCHAR(60), subject VARCHAR(120), budget VARCHAR(40), message TEXT, file VARCHAR(255) NOT NULL DEFAULT '', ip VARCHAR(45), is_read TINYINT NOT NULL DEFAULT 0, created_at INT NOT NULL) $T",
            "CREATE TABLE IF NOT EXISTS login_log(id INT AUTO_INCREMENT PRIMARY KEY, ip VARCHAR(45), ts INT NOT NULL, KEY(ip, ts)) $T",
        ] as $sql) $pdo->exec($sql);

        // Admin login (password is stored hashed, never in plain text)
        if (!(int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn())
            $pdo->prepare('INSERT INTO admins(username, pass_hash) VALUES(?, ?)')->execute(['WebAI', password_hash('Mehboob@1234', PASSWORD_DEFAULT)]);

        // Seed the content that was inside the original HTML file
        if (!(int)$pdo->query('SELECT COUNT(*) FROM settings')->fetchColumn()) {
            $s = json_decode(file_get_contents(__DIR__ . '/inc/seed.json'), true);
            $st = $pdo->prepare('INSERT INTO settings(skey, sval) VALUES(?, ?)');
            foreach (['name', 'email', 'uniEmail', 'wa', 'addr', 'bio'] as $k) $st->execute([$k, $s['DEF'][$k]]);
            foreach (['fb' => '', 'hero' => 'assets/hero.jpg', 'ceo_file' => 'assets/ceo-portfolio.html', 'mail_notify' => '1'] as $k => $val) $st->execute([$k, $val]);
            $it = $pdo->prepare('INSERT INTO items(kind, title, sub, body, extra, sort_order) VALUES(?,?,?,?,?,?)');
            foreach ($s['BUILD'] as $i => $r) $it->execute(['build', $r[0], '', $r[1], $r[2], $i + 1]);
            foreach ($s['SEC'] as $kind => $list) foreach ($list as $i => $r) $it->execute([$kind, $r[0], '', $r[1], '', $i + 1]);
            foreach ($s['PR'] as $i => $r) $it->execute(['project', $r[0], $r[1], $r[2], $r[3], $i + 1]);
            $now = time();
            $pdo->prepare("INSERT INTO posts(title, slug, excerpt, content, cover, status, created_at, updated_at) VALUES(?,?,?,?,?,'published',?,?)")
                ->execute(['Welcome to MQ WebAI Studio', 'welcome', 'Yeh pehli post hai. Admin panel se ise edit ya delete karein.',
                           '<p>Yeh pehli sample post hai. <strong>Admin panel</strong> se ise edit karein, delete karein ya nayi posts add karein.</p>', '', $now, $now]);
        }

        @mkdir(__DIR__ . '/uploads', 0755, true);
        $code = "<?php\nreturn " . var_export(['host' => $v['host'], 'name' => $v['name'], 'user' => $v['user'], 'pass' => $v['pass']], true) . ";\n";
        if (@file_put_contents(CFG, $code) === false) $manual = $code;
        $done = true;
    } catch (Exception $x) { $err = $x->getMessage(); }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install — MQ WebAI Studio</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light"><div class="container py-5" style="max-width:520px">
<h1 class="h4">MQ WebAI Studio — Installer</h1>
<?php if ($done): ?>
  <div class="alert alert-success">Install mukammal ✅<br>Admin username: <b>WebAI</b> · Password: <b>Mehboob@1234</b></div>
  <?php if ($manual): ?><div class="alert alert-warning">config.php likhi nahi ja saki. Yeh text <code>config.php</code> naam ki file mein paste karke site ke main folder mein save karein:<pre class="small bg-white p-2"><?= e($manual) ?></pre></div><?php endif; ?>
  <p class="small">Ab <b>install.php</b> delete kar dein, aur login ke baad Account page se password badal lein.</p><a class="btn btn-primary" href="admin/login.php">Admin Login →</a> <a class="btn btn-outline-secondary" href="index.php">Website</a>
<?php else: ?>
  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <form method="post" class="card p-3">
    <label class="small">MySQL Host</label><input class="form-control mb-2" name="host" value="<?= e($v['host']) ?>" required>
    <label class="small">Database name</label><input class="form-control mb-2" name="name" value="<?= e($v['name']) ?>" required>
    <label class="small">DB Username</label><input class="form-control mb-2" name="user" value="<?= e($v['user']) ?>" required>
    <label class="small">DB Password</label><input type="password" class="form-control mb-3" name="pass" value="<?= e($v['pass']) ?>">
    <button class="btn btn-primary">Install</button></form>
<?php endif; ?></div></body></html>
