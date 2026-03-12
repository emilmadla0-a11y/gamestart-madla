<?php
// ============================================================
//  GAME START! — Category Page (category.php?id=1)
//  Shows all published articles for a single category
// ============================================================

require_once 'includes/db.php';

// ── Validate the ID ──────────────────────────────────────
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

// ── Fetch the category ────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM category WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    http_response_code(404);
    $page_title = '404 — Category Not Found';
    $categories = $pdo->query("SELECT * FROM category ORDER BY name ASC")->fetchAll();
    require_once 'includes/header.php';
    echo '<div class="page-wrap"><div class="empty-state">
            <span class="big">404</span>
            Genre not found. <a href="index.php" style="color:var(--neon-cyan)">← Back home</a>
          </div></div>';
    require_once 'includes/footer.php';
    exit;
}

// ── Fetch all categories (for nav) ───────────────────────
$categories = $pdo->query("SELECT * FROM category ORDER BY id ASC")->fetchAll();

// ── Fetch published articles with IMAGE DATA ────────────
$stmt = $pdo->prepare("
    SELECT a.*,
           m.forename, m.surname, m.id AS member_id,
           i.file AS image_file,
           i.alt  AS image_alt
    FROM   article a
    JOIN   member  m ON m.id = a.member_id
    LEFT JOIN image i ON i.id = a.image_id
    WHERE  a.category_id = ? AND a.published = 1
    ORDER  BY a.created DESC
");
$stmt->execute([$id]);
$articles = $stmt->fetchAll();

$page_title = htmlspecialchars($category['name']) . ' Games — GAME START!';

// ── Maps ─────────────────────────────────────────────────
$cat_icons  = [1 => '⚔️', 2 => '🔫', 3 => '♟️', 4 => '🗺️'];
$cat_colors = [1 => 1,     2 => 2,    3 => 3,    4 => 4   ];
$ci   = $cat_colors[$category['id']] ?? 1;
$icon = $cat_icons[$category['id']] ?? '🎮';

require_once 'includes/header.php';
?>

<div class="page-wrap">

  <div class="cat-banner">
    <div class="cat-banner-icon"><?= $icon ?></div>
    <div class="cat-banner-text">
      <div class="cat-banner-label">// Genre</div>
      <h1 class="cat-banner-title cn-<?= $ci ?>">
        <?= htmlspecialchars($category['name']) ?>
      </h1>
      <p class="cat-banner-desc">
        <?= htmlspecialchars($category['description']) ?>
      </p>
    </div>
  </div>

  <div class="section-header">
    <span class="section-title">// GAME ARCHIVE</span>
    <span class="count-badge"><?= count($articles) ?> entries</span>
  </div>

  <?php if (empty($articles)): ?>
    <div class="empty-state">
      <span class="big">0</span>
      No published games in this genre yet.
      <br><br>
      <a href="index.php" class="back-btn">← Back to All Genres</a>
    </div>

  <?php else: ?>
  <div class="articles-grid">
    <?php foreach ($articles as $i => $article):
        $av = ($article['member_id'] % 3) + 1;
    ?>
    <a class="article-card"
       href="article.php?id=<?= $article['id'] ?>"
       style="animation-delay: <?= $i * 60 ?>ms">

      <div class="card-stripe stripe-<?= $ci ?>"></div>

      <?php if (!empty($article['image_file'])): ?>
        <img class="card-img" 
             src="<?= $base ?>images/<?= htmlspecialchars($article['image_file']) ?>"
             alt="<?= htmlspecialchars($article['image_alt'] ?? $article['title']) ?>">
      <?php else: ?>
        <div class="card-img-placeholder"><?= $icon ?></div>
      <?php endif; ?>

      <div class="card-body">
        <div class="card-meta">
          <span class="card-tag tag-<?= $ci ?>">
            <?= htmlspecialchars($category['name']) ?>
          </span>
          <span class="card-date">
            <?= date('Y-m-d', strtotime($article['created'])) ?>
          </span>
        </div>
        <h3 class="card-title"><?= htmlspecialchars($article['title']) ?></h3>
        <p class="card-summary"><?= htmlspecialchars($article['summary']) ?></p>
      </div>

      <div class="card-footer">
        <div class="card-author">
          <div class="author-avatar av-<?= $av ?>">
            <?= strtoupper(substr($article['forename'], 0, 1)) ?>
          </div>
          <?= htmlspecialchars($article['forename'] . ' ' . $article['surname']) ?>
        </div>
        <span class="read-btn">VIEW →</span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div style="margin-top: 40px;">
    <a href="index.php" class="back-btn">← Back to All Genres</a>
  </div>

</div><?php require_once 'includes/footer.php'; ?>