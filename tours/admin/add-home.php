<?php
if ($_POST) {
  $db = new PDO('sqlite:db/homes.db');
  $db->exec("CREATE TABLE IF NOT EXISTS homes (
    id INTEGER PRIMARY KEY,
    title TEXT,
    slug TEXT,
    thumbnail TEXT,
    matterport_url TEXT
  )");

  $stmt = $db->prepare("INSERT INTO homes (title, slug, thumbnail, matterport_url) VALUES (?, ?, ?, ?)");
  $stmt->execute([
    $_POST['title'],
    strtolower(preg_replace('/\s+/', '-', $_POST['title'])),
    $_POST['thumbnail'],
    $_POST['matterport_url']
  ]);
  echo "<div class='success'>✅ Home added! <a href='../index.html'>View</a></div>";
}
?>

<form method="POST">
  <input name="title" placeholder="Home Title" required>
  <input name="thumbnail" placeholder="Thumbnail URL (e.g., /assets/images/thumbnails/villa.jpg)">
  <input name="matterport_url" placeholder="Matterport Embed URL (m=... part)">
  <button type="submit">Add Home</button>
</form>