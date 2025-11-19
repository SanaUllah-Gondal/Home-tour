<?php
// Initialize DB
try {
    $dbPath = __DIR__ . '/admin/db/homes.db';
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $db->exec("CREATE TABLE IF NOT EXISTS homes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        location TEXT,
        specs TEXT,
        thumbnail TEXT DEFAULT 'default.jpg',
        matterport_url TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $homes = $db->query("SELECT * FROM homes ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $homes = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeTour | Virtual Property Walkthroughs</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script defer src="assets/js/main.js"></script>
</head>
<body>
    <!-- Mobile Nav Toggle -->
    <button id="mobile-menu-toggle" class="mobile-toggle" aria-label="Toggle menu">☰</button>

    <header id="main-header">
        <div class="container">
            <a href="index.php" class="logo">🏠 HomeTour</a>
            <nav id="main-nav">
                <a href="#hero">Home</a>
                <a href="#listings">Tours</a>
                <a href="contact.php">Contact</a>
                <a href="admin/add.php" class="admin-link">Admin</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="hero" class="hero">
        <div class="container hero-content">
            <h1>Step Inside. Anytime.</h1>
            <p>Experience our latest homes in immersive 360° — no appointment needed.</p>
            <a href="#listings" class="btn primary">Explore Tours</a>
        </div>
    </section>

    <!-- Listings -->
    <section id="listings" class="section">
        <div class="container">
            <h2>Featured Home Tours</h2>
            <?php if (empty($homes)): ?>
                <p class="empty-state">No homes added yet. <a href="admin/add.php">Add one in admin</a>.</p>
            <?php else: ?>
                <div class="grid" id="homes-grid">
                    <?php foreach ($homes as $home): ?>
                    <div class="card">
                        <div class="card-image">
                            <img src="assets/images/<?= htmlspecialchars($home['thumbnail']) ?>" 
                                 alt="<?= htmlspecialchars($home['title']) ?>"
                                 loading="lazy"
                                 onerror="this.src='assets/images/default.jpg'">
                        </div>
                        <div class="card-body">
                            <h3><?= htmlspecialchars($home['title']) ?></h3>
                            <p class="meta"><?= htmlspecialchars($home['location']) ?></p>
                            <p class="specs"><?= htmlspecialchars($home['specs']) ?></p>
                            <a href="tour.php?id=<?= $home['id'] ?>" class="btn outline">Start Virtual Tour</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> HomeTour by <a href="https://github.com/SanaUllah-Gondal" target="_blank">Sana Ullah Gondal</a></p>
        </div>
    </footer>
</body>
</html>