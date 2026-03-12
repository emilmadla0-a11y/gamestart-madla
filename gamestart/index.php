<?php
// ============================================================
//  GAME START! — Homepage (index.php)
//  Shows all categories + latest articles
// ============================================================

require_once 'includes/db.php';

$page_title = 'GAME START! — Game Archive';

// ── Fetch all categories with article counts ──────────────
$categories = $pdo->query("
    SELECT c.*,
           COUNT(a.id) AS article_count
    FROM   category c
    LEFT JOIN article a ON a.category_id = c.id AND a.published = 1
    GROUP  BY c.id
    ORDER  BY c.id ASC
")->fetchAll();

// ── Fetch latest 6 published articles with IMAGE DATA ──────
$latest = $pdo->query("
    SELECT a.*,
           c.name  AS category_name,
           m.forename, m.surname,
           i.file  AS image_file,
           i.alt   AS image_alt
    FROM   article a
    JOIN   category c ON c.id = a.category_id
    JOIN   member   m ON m.id = a.member_id
    LEFT JOIN image i ON i.id = a.image_id
    WHERE  a.published = 1
    ORDER  BY a.created DESC
    LIMIT  6
")->fetchAll();

// ── Total published games ────────────────────────────────
$total_games = $pdo->query("SELECT COUNT(*) FROM article WHERE published = 1")->fetchColumn();

// ── Category icon + colour maps ─────────────────────────
$cat_icons  = [1 => '⚔️', 2 => '🔫', 3 => '♟️', 4 => '🗺️'];
$cat_colors = [1 => 1,     2 => 2,    3 => 3,    4 => 4   ];

require_once 'includes/header.php';
?>

<div class="page-wrap">

  <section class="hero">
    <div class="hero-label">Game Content Archive</div>
    <h1 class="hero-title">
      THE ULTIMATE<br>
      <span class="highlight">GAME LIBRARY</span>
    </h1>
    <p class="hero-sub">
      Explore <?= $total_games ?> top titles across
      <?= count($categories) ?> genres &mdash; MOBA, FPS, Strategy &amp; Adventure.
    </p>
  </section>

  <div class="section-header">
    <span class="section-title">// GENRE INDEX</span>
    <span class="count-badge"><?= count($categories) ?> genres</span>
  </div>

  <div class="categories-grid">
    <?php
    $max_count = max(array_column($categories, 'article_count')) ?: 1;
    foreach ($categories as $i => $cat):
        $ci   = $cat_colors[$cat['id']] ?? 1;
        $pct  = round($cat['article_count'] / $max_count * 100);
        $icon = $cat_icons[$cat['id']] ?? '🎮';
    ?>
    <a class="cat-card hover-<?= $ci ?>"
       href="category.php?id=<?= $cat['id'] ?>"
       style="animation-delay: <?= $i * 80 ?>ms">
      <span class="cat-big-icon"><?= $icon ?></span>
      <h3 class="cat-name cn-<?= $ci ?>"><?= htmlspecialchars($cat['name']) ?></h3>
      <p class="cat-desc"><?= htmlspecialchars($cat['description']) ?></p>
      <div class="cat-count cn-<?= $ci ?>">
        <span><?= $cat['article_count'] ?> GAMES ARCHIVED</span>
      </div>
      <div class="cat-bar">
        <div class="cat-bar-fill fill-<?= $ci ?>" style="width: <?= $pct ?>%"></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="section-header" style="margin-top: 20px;">
    <span class="section-title">// LATEST GAMES</span>
    <span class="count-badge">showing 6 of <?= $total_games ?></span>
  </div>

  <?php if (empty($latest)): ?>
    <div class="empty-state">
      <span class="big">404</span>
      No published games yet. Add some via the database!
    </div>
  <?php else: ?>
  <div class="articles-grid">
    <?php foreach ($latest as $i => $article):
        $ci   = $cat_colors[$article['category_id']] ?? 1;
        $icon = $cat_icons[$article['category_id']] ?? '🎮';
        $av   = ($article['member_id'] % 3) + 1;
    ?>
    <a class="article-card"
       href="article.php?id=<?= $article['id'] ?>"
       style="animation-delay: <?= $i * 60 ?>ms">
      <div class="card-stripe stripe-<?= $ci ?>"></div>

<?php if (!empty($article['image_file'])): ?>
    <img class="card-img" 
         src="<?= $base ?>images/<?= htmlspecialchars($article['image_file']) ?>"
         alt="<?= htmlspecialchars($article['title']) ?>">
<?php else: ?>
    <div class="card-img-placeholder"><?= $icon ?></div>
<?php endif; ?>

      <div class="card-body">
        <div class="card-meta">
          <span class="card-tag tag-<?= $ci ?>">
            <?= htmlspecialchars($article['category_name']) ?>
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

</div><?php require_once 'includes/footer.php'; ?>