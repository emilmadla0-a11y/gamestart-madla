<?php
// ============================================================
//  GAME START! — Shared Footer
// ============================================================
?>

<div class="status-bar">
  <div class="status-item"><div class="status-dot green"></div> DB CONNECTED</div>
  <div class="status-item"><div class="status-dot yellow"></div> <?= count($categories) ?> GENRES</div>
  <div class="status-item"><div class="status-dot pink"></div>
    <?php
      if (isset($pdo)) {
          $total = $pdo->query("SELECT COUNT(*) FROM article WHERE published = 1")->fetchColumn(); // Count published articles
          echo $total . ' GAMES';
      }
    ?>
  </div>
  <div class="status-item"><div class="status-dot green"></div>
    <?php
      if (isset($pdo)) {
          $members = $pdo->query("SELECT COUNT(*) FROM member")->fetchColumn(); // Count members
          echo $members . ' MEMBERS';
      }
    ?>
  </div>
  <div class="spacer"></div>
  <span>GAME START! CMS v2.0</span>
  <span>&copy; <?= date('Y') ?></span>
</div>

</body>
</html>