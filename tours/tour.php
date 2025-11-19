<?php
$id = $_GET['id'] ?? 0;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    die("Invalid tour ID");
}

try {
    $db = new PDO("sqlite:" . __DIR__ . "/admin/db/homes.db");
    $stmt = $db->prepare("SELECT * FROM homes WHERE id = ?");
    $stmt->execute([$id]);
    $home = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$home) {
        http_response_code(404);
        die("Tour not found");
    }
} catch (Exception $e) {
    die("DB error");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($home['title']) ?> | HomeTour</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script defer src="assets/js/main.js"></script>
    <style>
        #tour-frame { width: 100%; height: 75vh; border: none; }
        .tour-info { text-align: center; margin: 1.5rem 0; }
        .tour-info h1 { margin-bottom: 0.3rem; }
        .tour-info .meta { color: #666; }
    </style>
</head>
<body>
    <header class="tour-header">
        <div class="container">
            <a href="index.php" class="back-link">← Back to Tours</a>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="tour-info">
                <h1><?= htmlspecialchars($home['title']) ?></h1>
                <p class="meta">
                    <?= htmlspecialchars($home['location']) ?> • <?= htmlspecialchars($home['specs']) ?>
                </p>
            </div>
        </div>

        <!-- Matterport Embed -->
        <?php if (!empty($home['matterport_url'])): ?>
            <iframe 
                id="tour-frame"
                src="<?= htmlspecialchars($home['matterport_url']) ?>&brand=0&help=0"
                allowfullscreen
                allow="xr-spatial-tracking; camera *; microphone *">
            </iframe>
        <?php else: ?>
            <div class="section empty-state">
                <p>No 360° tour embedded yet.</p>
                <p class="hint">Admin: Add a Matterport URL to enable virtual tour.</p>
            </div>
        <?php endif; ?>

        <div class="container" style="text-align:center; margin-top:2rem">
            <a href="contact.php?tour=<?= urlencode($home['title']) ?>" class="btn primary large">
                Schedule In-Person Visit
            </a>
        </div>
    </main>
</body>
</html>