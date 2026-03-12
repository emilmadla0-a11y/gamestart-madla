<?php
// ============================================================
//  GAME START! — Member Page (member.php?id=1)
//  Shows member profile and their published articles
// ============================================================

require_once 'includes/db.php';

// ── Validate ID ──────────────────────────────────────────
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);                     // Validate id

if (!$id) {                                                                    // If no valid id
    header('Location: index.php');
    exit;
}

// ── Fetch member data ─────────────────────────────────────
$stmt = $pdo->prepare(
    "SELECT forename, surname, joined, picture, email FROM member WHERE id = :id;" // SQL
);
$stmt->execute([$id]);                                                         // Run with id
$member = $stmt->fetch();                                                      // Get member data

if (!$member) {                                                                // If array is empty
    http_response_code(404);
    $page_title = '404 — Member Not Found';
    $categories = $pdo->query("SELECT * FROM category ORDER BY id ASC")->fetchAll();
    require_once 'includes/header.php';
    echo '<div class="page-wrap"><div class="empty-state">
            <span class="big">404</span>
            Member not found.
            <br><br>
            <a href="index.php" class="back-btn">← Back Home</a>
          </div></div>';
    require_once 'includes/footer.php';
    exit;
}

// ── Fetch member's published articles ────────────────────
$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.summary, a.category_id, a.member_id,
           c.name        AS category,
           CONCAT(m.forename, ' ', m.surname) AS author,
           i.file        AS image_file,
           i.alt         AS image_alt
      FROM article    AS a
      JOIN category   AS c  ON a.category_id = c.id
      JOIN member     AS m  ON a.member_id   = m.id
      LEFT JOIN image AS i  ON a.image_id    = i.id
     WHERE a.member_id = :id AND a.published = 1
     ORDER BY a.id DESC;"                                                      // SQL
);
$stmt->execute([$id]);                                                         // Member's articles
$articles = $stmt->fetchAll();                                                 // Get articles

// ── Fetch all categories (for nav) ───────────────────────
$categories = $pdo->query("SELECT * FROM category ORDER BY id ASC")->fetchAll(); // Get categories

// ── Page meta ────────────────────────────────────────────
$page_title  = htmlspecialchars($member['forename'] . ' ' . $member['surname']) . ' — GAME START!'; // HTML <title> content
$av          = ($id % 3) + 1;                                                  // Avatar color index

// ── Category icon + colour maps ─────────────────────────
$cat_icons  = [1 => '⚔️', 2 => '🔫', 3 => '♟️', 4 => '🗺️'];
$cat_colors = [1 => 1,     2 => 2,    3 => 3,    4 => 4   ];

require_once 'includes/header.php';
?>

<div class="page-wrap">

  <!-- ── Member Profile Header ─────────────────────────── -->
  <section class="hero" style="padding-bottom: 32px;">
    <div class="hero-label">// Member Profile</div>

    <div style="display:flex; align-items:center; gap: 24px; flex-wrap: wrap; margin-top: 20px;">

      <!-- Avatar -->
      <div class="author-avatar-lg av-<?= $av ?>" style="width:80px;height:80px;font-size:28px;flex-shrink:0;">
        <?= strtoupper(substr($member['forename'], 0, 1)) ?>
      </div>

      <!-- Member info -->
      <div>
        <h1 style="
          font-family: 'Rajdhani', sans-serif;
          font-size: clamp(24px, 3vw, 38px);
          font-weight: 700; color: #fff;
          letter-spacing: 1px; margin-bottom: 6px;
        ">
          <?= htmlspecialchars($member['forename'] . ' ' . $member['surname']) ?>
        </h1>
        <div class="author-email" style="font-size:13px; margin-bottom: 4px;">
          <?= htmlspecialchars($member['email']) ?>
        </div>
        <div class="author-joined">
          // Joined <?= date('Y-m-d', strtotime($member['joined'])) ?>         
        </div>
        <div style="margin-top: 10px;">
          <span class="count-badge">
            <?= count($articles) ?> game<?= count($articles) !== 1 ? 's' : '' ?> published
          </span>
        </div>
      </div>

    </div>
  </section>

  <!-- ── Member's Articles ─────────────────────────────── -->
  <div class="section-header">
    <span class="section-title">// PUBLISHED GAMES</span>
    <span class="count-badge"><?= count($articles) ?> entries</span>
  </div>

  <?php if (empty($articles)): ?>
    <div class="empty-state">
      <span class="big">0</span>
      No published games yet.
      <br><br>
      <a href="index.php" class="back-btn">← Back to Archive</a>
    </div>

  <?php else: ?>
  <div class="articles-grid">
    <?php foreach ($articles as $i => $article):
        $ci   = $cat_colors[$article['category_id']] ?? 1;
        $icon = $cat_icons[$article['category_id']]  ?? '🎮';
        $mav  = ($article['member_id'] % 3) + 1;
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
            <?= htmlspecialchars($article['category']) ?>
          </span>
        </div>
        <h3 class="card-title"><?= htmlspecialchars($article['title']) ?></h3>
        <p class="card-summary"><?= htmlspecialchars($article['summary']) ?></p>
      </div>

      <div class="card-footer">
        <div class="card-author">
          <div class="author-avatar av-<?= $mav ?>">
            <?= strtoupper(substr($article['author'], 0, 1)) ?>
          </div>
          <?= htmlspecialchars($article['author']) ?>
        </div>
        <span class="read-btn">VIEW →</span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div style="margin-top: 40px;">
    <a href="index.php" class="back-btn">← Back to All Games</a>
  </div>

</div>

<?php require_once 'includes/footer.php'; ?>
