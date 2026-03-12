<?php
// ============================================================
//  GAME START! — Article Detail Page (article.php?id=1)
//  Displays full content for a single published article
// ============================================================

require_once 'includes/db.php';

// ── Validate ID ──────────────────────────────────────────
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

// ── Fetch all categories (for nav) ───────────────────────
$categories = $pdo->query("SELECT * FROM category ORDER BY id ASC")->fetchAll();

// ── 1. Fetch the SINGLE article with IMAGE DATA ──────────
// NOTE: We use WHERE a.id = ? to get one specific game
$stmt = $pdo->prepare("
    SELECT a.*,
           c.name        AS category_name,
           c.id          AS category_id,
           c.description AS category_description,
           i.file        AS image_file,
           i.alt         AS image_alt,
           m.forename, m.surname, m.email, m.joined
    FROM   article a
    JOIN   category c ON c.id = a.category_id
    JOIN   member   m ON m.id = a.member_id
    LEFT JOIN image i ON i.id = a.image_id
    WHERE  a.id = ? AND a.published = 1
");
$stmt->execute([$id]);
$article = $stmt->fetch(); // This creates the $article variable

// ── 2. Check if it exists BEFORE using it ────────────────
if (!$article) {
    http_response_code(404);
    $page_title = '404 — Game Not Found';
    require_once 'includes/header.php';
    echo '<div class="page-wrap"><div class="empty-state">
            <span class="big">404</span>
            Game not found or not published yet.
            <br><br>
            <a href="index.php" class="back-btn">← Back Home</a>
          </div></div>';
    require_once 'includes/footer.php';
    exit;
}

// ── 3. Define variables NOW that $article is safe to use ──
$page_title = htmlspecialchars($article['title']) . ' — GAME START!';

$cat_icons  = [1 => '⚔️', 2 => '🔫', 3 => '♟️', 4 => '🗺️'];
$cat_colors = [1 => 1,     2 => 2,    3 => 3,    4 => 4   ];
$ci   = $cat_colors[$article['category_id']] ?? 1;
$icon = $cat_icons[$article['category_id']] ?? '🎮';
$av   = ($article['member_id'] % 3) + 1;

// ── 4. Fetch Related Articles ─────────────────────────────
$stmt = $pdo->prepare("
    SELECT a.*, m.forename, m.surname, m.id AS member_id, i.file AS image_file, i.alt AS image_alt
    FROM   article a
    JOIN   member  m ON m.id = a.member_id
    LEFT JOIN image i ON i.id = a.image_id
    WHERE  a.category_id = ? AND a.id != ? AND a.published = 1
    ORDER  BY a.created DESC
    LIMIT  3
");
$stmt->execute([$article['category_id'], $id]);
$related = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-wrap">

  <div class="article-hero">
    <div class="card-stripe stripe-<?= $ci ?>" style="margin-bottom:28px;height:4px;"></div>

    <?php if (!empty($article['image_file'])): ?>
      <img class="article-hero-img" 
           src="<?= $base ?>images/<?= htmlspecialchars($article['image_file']) ?>"
           alt="<?= htmlspecialchars($article['image_alt'] ?? $article['title']) ?>"
           style="width:100%; border-radius:8px; margin-bottom:20px; border: 1px solid var(--dark-border);">
    <?php else: ?>
      <div class="article-hero-placeholder"><?= $icon ?></div>
    <?php endif; ?>

    <div class="article-meta-row">
      <a href="category.php?id=<?= $article['category_id'] ?>" class="card-tag tag-<?= $ci ?>">
        <?= htmlspecialchars($article['category_name']) ?>
      </a>
      <span style="font-family:'Share Tech Mono',monospace;font-size:11px;color:rgba(136,146,176,.5);">
        📅 <?= date('F j, Y', strtotime($article['created'])) ?>
      </span>
    </div>

    <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
    <p class="article-summary"><?= htmlspecialchars($article['summary']) ?></p>

    <hr class="article-divider">

    <div class="article-content">
      <?php echo nl2br(htmlspecialchars($article['content'])); ?>
    </div>
  </div>

  <div class="article-footer">
    <div class="author-block">
      <div class="author-avatar-lg av-<?= $av ?>">
        <?= strtoupper(substr($article['forename'], 0, 1)) ?>
      </div>
      <div>
        <div class="author-name">
          <?= htmlspecialchars($article['forename'] . ' ' . $article['surname']) ?>
        </div>
        <div class="author-email"><?= htmlspecialchars($article['email']) ?></div>
        <div class="author-joined">// Joined <?= date('Y-m-d', strtotime($article['joined'])) ?></div>
      </div>
    </div>
    <a href="category.php?id=<?= $article['category_id'] ?>" class="back-btn">
      ← More <?= htmlspecialchars($article['category_name']) ?> Games
    </a>
  </div>

  <?php if (!empty($related)): ?>
  <div class="related-section">
    <div class="section-header">
      <span class="section-title">// MORE <?= strtoupper(htmlspecialchars($article['category_name'])) ?></span>
      <span class="count-badge"><?= count($related) ?> titles</span>
    </div>
    <div class="related-grid">
      <?php foreach ($related as $i => $rel): 
          $rav = ($rel['member_id'] % 3) + 1;
      ?>
      <a class="article-card" href="article.php?id=<?= $rel['id'] ?>" style="animation-delay: <?= $i * 80 ?>ms">
        <div class="card-stripe stripe-<?= $ci ?>"></div>
        
        <?php if (!empty($rel['image_file'])): ?>
          <img class="card-img" style="height:130px; object-fit:cover;"
               src="images/<?= htmlspecialchars($rel['image_file']) ?>" 
               alt="<?= htmlspecialchars($rel['image_alt'] ?? $rel['title']) ?>">
        <?php else: ?>
          <div class="card-img-placeholder" style="height:130px;font-size:40px;"><?= $icon ?></div>
        <?php endif; ?>

        <div class="card-body">
          <div class="card-meta">
            <span class="card-tag tag-<?= $ci ?>"><?= htmlspecialchars($article['category_name']) ?></span>
          </div>
          <h3 class="card-title"><?= htmlspecialchars($rel['title']) ?></h3>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</div>

<?php require_once 'includes/footer.php'; ?>