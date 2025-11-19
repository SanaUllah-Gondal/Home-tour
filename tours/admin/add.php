<?php
session_start();

// Optional: Simple login (username: admin, password: tour2025)
// Remove or change for production
$correctUser = 'admin';
$correctPass = 'tour2025';

if (!isset($_SESSION['admin_logged_in'])) {
    if ($_POST['username'] ?? '' === $correctUser && $_POST['password'] ?? '' === $correctPass) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $loginError = isset($_POST) ? "Invalid credentials." : "";
        include __DIR__ . '/login-form.html';
        exit;
    }
}

$message = '';
$home = ['title' => '', 'location' => '', 'specs' => '', 'thumbnail' => 'default.jpg', 'matterport_url' => ''];

if ($_POST && isset($_POST['title'])) {
    try {
        $dbPath = __DIR__ . '/db/homes.db';
        if (!is_dir(dirname($dbPath))) mkdir(dirname($dbPath), 0755, true);
        
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

        $stmt = $db->prepare("INSERT INTO homes (title, location, specs, thumbnail, matterport_url) 
                              VALUES (?, ?, ?, ?, ?)");
        $res = $stmt->execute([
            $_POST['title'],
            $_POST['location'],
            $_POST['specs'],
            $_POST['thumbnail'] ?: 'default.jpg',
            $_POST['matterport_url']
        ]);

        if ($res) {
            $message = "<div class='alert success'>✅ Home added successfully! <a href='../index.php'>View site</a></div>";
            $home = ['title' => '', 'location' => '', 'specs' => '', 'thumbnail' => 'default.jpg', 'matterport_url' => ''];
        }
    } catch (Exception $e) {
        $message = "<div class='alert error'>❌ DB Error: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>HomeAs Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container { max-width: 700px; margin: 2rem auto; padding: 0 1rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 4px; }
        .form-group textarea { height: 100px; }
        .btn-group { text-align: center; margin-top: 1rem; }
        .hint { font-size: 0.85rem; color: #666; margin-top: 0.3rem; display: block; }
        .alert { padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="admin-container">
        <header style="text-align:center; margin-bottom:2rem;">
            <h1>HomeAs Admin Panel</h1>
            <a href="../index.php" class="btn outline">← View Site</a>
        </header>

        <?= $message ?>

        <form method="POST">
            <div class="form-group">
                <label for="title">Home Title *</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($home['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($home['location']) ?>">
            </div>

            <div class="form-group">
                <label for="specs">Specs (e.g., 4 Beds, 3 Baths)</label>
                <input type="text" id="specs" name="specs" value="<?= htmlspecialchars($home['specs']) ?>">
            </div>

            <div class="form-group">
                <label for="thumbnail">Thumbnail (in <code>assets/images/</code>)</label>
                <input type="text" id="thumbnail" name="thumbnail" value="<?= htmlspecialchars($home['thumbnail']) ?>">
                <span class="hint">e.g., villa.jpg — must be uploaded manually to assets/images/</span>
            </div>

            <div class="form-group">
                <label for="matterport_url">Matterport Embed URL *</label>
                <input type="url" id="matterport_url" name="matterport_url" 
                       value="<?= htmlspecialchars($home['matterport_url']) ?>"
                       placeholder="https://my.matterport.com/show/?m=AbCdEfGhIjK" required>
                <span class="hint">Get from Matterport → Share → Embed → copy the <code>m=...</code> URL</span>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn primary large">✅ Add Home</button>
            </div>
        </form>

        <hr style="margin: 2rem 0;">
        <p><small>🔒 Logged in as admin. <a href="?logout=1" onclick="return confirm('Log out?')">Log out</a></small></p>
    </div>

    <?php if (isset($_GET['logout'])) { session_destroy(); header("Location: ."); exit; } ?>
</body>
</html>