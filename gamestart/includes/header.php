<?php
// ============================================================
//  GAME START! — Shared Header
//  Included at the top of every page
// ============================================================

// Active nav helper
function nav_active(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page ? 'active' : '';
}

// Build base URL once — works at any subfolder depth
if (!isset($base)) {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
}

// Fetch all categories for nav
if (!isset($categories) && isset($pdo)) {
    $categories = $pdo->query("SELECT * FROM category ORDER BY id ASC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'GAME START!') ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Rajdhani:wght@300;400;500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $base ?>css/style.css">
</head>
<body>

<header>
  <div class="header-inner">

    <a href="<?= $base ?>index.php" class="logo-link">
      <img class="logo-img" src="<?= $base ?>images/logo.png" alt="GAME START!">
    </a>

  <nav>
    <a href="<?= $base ?>index.php" class="nav-btn <?= nav_active('index') ?>">▍ Home</a>
    <?php foreach ($categories as $cat): ?>
      <a href="<?= $base ?>category.php?id=<?= $cat['id'] ?>"
        class="nav-btn <?= (isset($_GET['id']) && $_GET['id'] == $cat['id']) ? 'active' : '' ?>">
        <?= htmlspecialchars($cat['name']) ?>
      </a>
    <?php endforeach; ?>
    <a href="<?= $base ?>search.php" class="nav-btn <?= nav_active('search') ?>">🔍 Search</a>
  </nav>
    

    <div class="header-stats">
      <div class="stat-item">
        <span class="stat-label">Genre</span>
        <span class="stat-value"><?= str_pad(count($categories), 2, '0', STR_PAD_LEFT) ?></span>
      </div>
    </div>

  </div>
</header>
