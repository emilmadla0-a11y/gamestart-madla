<?php
// ============================================================
//  GAME START! — Search Page (search.php?term=valorant)
//  Searches articles by title, summary, and content
// ============================================================

require_once 'includes/db.php';

// ── Get & sanitize inputs ────────────────────────────────
$term  = filter_input(INPUT_GET, 'term');                                      // Get search term
$show  = filter_input(INPUT_GET, 'show', FILTER_VALIDATE_INT) ?? 6;           // Results per page (default 6)
$from  = filter_input(INPUT_GET, 'from', FILTER_VALIDATE_INT) ?? 0;           // Offset for pagination
$count = 0;                                                                    // Set count to 0
$articles = [];                                                                // Set articles to empty array

if ($term) {                                                                   // If search term provided
    $arguments['term1'] = '%' . $term . '%';                                  // Store search term in array
    $arguments['term2'] = '%' . $term . '%';                                  // three times as placeholders
    $arguments['term3'] = '%' . $term . '%';                                  // cannot be repeated in SQL

    $sql = "SELECT COUNT(title) FROM article
             WHERE title   LIKE :term1
                OR summary LIKE :term2
                OR content LIKE :term3
               AND published = 1;";                                            // How many articles match term
    $count = $pdo->prepare($sql);                                              // Prepare count query
    $count->execute($arguments);                                               // Execute with arguments
    $count = $count->fetchColumn();                                            // Return count

    if ($count > 0) {                                                          // If articles match term
        $arguments['show'] = (int) $show;                                     // Add to array for pagination
        $arguments['from'] = (int) $from;                                     // Add to array for pagination

        $sql = "SELECT a.id, a.title, a.summary, a.category_id, a.member_id,
                       c.name        AS category_name,
                       CONCAT(m.forename, ' ', m.surname) AS author,
                       i.file        AS image_file,
                       i.alt         AS image_alt
                  FROM article     AS a
                  JOIN category    AS c  ON a.category_id = c.id
                  JOIN member      AS m  ON a.member_id   = m.id
                  LEFT JOIN image  AS i  ON a.image_id    = i.id
                 WHERE a.title   LIKE :term1
                    OR a.summary LIKE :term2
                    OR a.content LIKE :term3
                   AND a.published = 1
              ORDER BY a.id DESC
                 LIMIT :show
                OFFSET :from;";                                                // Find matching articles
        $stmt = $pdo->prepare($sql);                                           // Prepare query
        $stmt->bindValue(':term1', $arguments['term1']);                       // Bind term1
        $stmt->bindValue(':term2', $arguments['term2']);                       // Bind term2
        $stmt->bindValue(':term3', $arguments['term3']);                       // Bind term3
        $stmt->bindValue(':show',  $arguments['show'], PDO::PARAM_INT);       // Bind show as int
        $stmt->bindValue(':from',  $arguments['from'], PDO::PARAM_INT);       // Bind from as int
        $stmt->execute();                                                      // Run query
        $articles = $stmt->fetchAll();                                         // Get results
    }
}

// ── Pagination calculations ──────────────────────────────
$total_pages  = 1;                                                             // Default to 1 page
$current_page = 1;                                                             // Default to page 1
if ($count > $show) {                                                          // If matches is more than show
    $total_pages  = ceil($count / $show);                                      // Calculate total pages
    $current_page = ceil($from  / $show) + 1;                                 // Calculate current page
}

// ── Fetch all categories (for nav) ──────────────────────
$categories = $pdo->query("SELECT * FROM category ORDER BY id ASC")->fetchAll(); // Get categories

// ── Category icon + colour maps ─────────────────────────
$cat_icons  = [1 => '⚔️', 2 => '🔫', 3 => '♟️', 4 => '🗺️'];
$cat_colors = [1 => 1,     2 => 2,    3 => 3,    4 => 4   ];

$page_title = $term
    ? 'Search: ' . htmlspecialchars($term) . ' — GAME START!'
    : 'Search — GAME START!';                                                  // HTML <title> content

require_once 'includes/header.php';
?>

<div class="page-wrap">

  <!-- ── Search Header ─────────────────────────────────── -->
  <section class="hero" style="padding-bottom: 28px;">
    <div class="hero-label">Search the Archive</div>
    <h1 class="hero-title" style="font-size: clamp(12px, 1.6vw, 20px);">
      FIND YOUR<br>
      <span class="highlight">NEXT GAME</span>
    </h1>

    <!-- Search form -->
    <form action="search.php" method="get" style="display:flex; gap: 0; margin-top: 22px; max-width: 560px;">
      <label for="search" style="
        font-family: 'Share Tech Mono', monospace;
        font-size: 11px; color: var(--mid-gray);
        display: flex; align-items: center;
        background: var(--dark-card);
        border: 1px solid var(--dark-border);
        border-right: none;
        padding: 0 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        white-space: nowrap;
      ">// Search:</label>
      <input
        type="text"
        name="term"
        id="search"
        value="<?= htmlspecialchars($term ?? '') ?>"
        placeholder="e.g. valorant, dota, FPS..."
        style="
          flex: 1;
          background: var(--dark-card);
          border: 1px solid var(--dark-border);
          border-right: none;
          color: var(--light-text);
          font-family: 'Share Tech Mono', monospace;
          font-size: 13px;
          padding: 12px 16px;
          outline: none;
        "
      />
      <input
        type="submit"
        value="SEARCH"
        style="
          background: var(--neon-cyan);
          color: var(--dark-bg);
          border: none;
          font-family: 'Press Start 2P', monospace;
          font-size: 9px;
          padding: 12px 20px;
          cursor: pointer;
          letter-spacing: 1px;
          transition: all .2s;
        "
      />
    </form>

    <!-- Match count -->
    <?php if ($term): ?>
      <p style="
        font-family: 'Share Tech Mono', monospace;
        font-size: 12px; margin-top: 16px;
        color: var(--mid-gray);
      ">
        <?php if ($count > 0): ?>
          <span style="color: var(--neon-cyan);"><?= $count ?></span>
          match<?= $count !== 1 ? 'es' : '' ?> found for
          <span style="color: var(--neon-yellow);">"<?= htmlspecialchars($term) ?>"</span>
        <?php else: ?>
          <span style="color: var(--neon-pink);">0 matches</span> found for
          <span style="color: var(--neon-yellow);">"<?= htmlspecialchars($term) ?>"</span>
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </section>

  <!-- ── Results Grid ──────────────────────────────────── -->
  <?php if (!empty($articles)): ?>

    <div class="section-header">
      <span class="section-title">// SEARCH RESULTS</span>
      <span class="count-badge"><?= count($articles) ?> of <?= $count ?> titles</span>
    </div>

    <div class="articles-grid">
      <?php foreach ($articles as $i => $article):
          $ci   = $cat_colors[$article['category_id']] ?? 1;
          $icon = $cat_icons[$article['category_id']]  ?? '🎮';
          $av   = ($article['member_id'] % 3) + 1;
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
              <?= htmlspecialchars($article['category_name']) ?>
            </span>
          </div>
          <h3 class="card-title"><?= htmlspecialchars($article['title']) ?></h3>
          <p class="card-summary"><?= htmlspecialchars($article['summary']) ?></p>
        </div>

        <div class="card-footer">
          <div class="card-author">
            <div class="author-avatar av-<?= $av ?>">
              <?= strtoupper(substr($article['author'], 0, 1)) ?>
            </div>
            <?= htmlspecialchars($article['author']) ?>
          </div>
          <span class="read-btn">VIEW →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

  <?php elseif ($term && $count === 0): ?>

    <!-- ── No Results State ──────────────────────────────── -->
    <div class="empty-state">
      <span class="big">0</span>
      No games matched <strong style="color: var(--neon-yellow);">"<?= htmlspecialchars($term) ?>"</strong>
      <br><br>
      <a href="index.php" class="back-btn">← Back to Archive</a>
    </div>

  <?php endif; ?>

  <!-- ── Pagination ────────────────────────────────────── -->
  <?php if ($count > $show): ?>
  <nav style="margin-top: 36px; display:flex; gap: 8px; flex-wrap: wrap;" aria-label="Pagination Navigation">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?term=<?= urlencode($term) ?>&show=<?= $show ?>&from=<?= (($i - 1) * $show) ?>"
         style="
           font-family: 'Share Tech Mono', monospace;
           font-size: 12px;
           padding: 8px 16px;
           border: 1px solid <?= $i === $current_page ? 'var(--neon-cyan)' : 'var(--dark-border)' ?>;
           background: <?= $i === $current_page ? 'rgba(0,245,255,0.1)' : 'var(--dark-card)' ?>;
           color: <?= $i === $current_page ? 'var(--neon-cyan)' : 'var(--mid-gray)' ?>;
           <?= $i === $current_page ? 'text-shadow: 0 0 8px var(--neon-cyan);' : '' ?>
         "
         <?= $i === $current_page ? 'aria-current="true"' : '' ?>>
        <?= $i ?>
      </a>
    <?php endfor; ?>
  </nav>
  <?php endif; ?>

  <div style="margin-top: 40px;">
    <a href="index.php" class="back-btn">← Back to All Games</a>
  </div>

</div>

<?php require_once 'includes/footer.php'; ?>
