<?php
$success = false;
$error = '';
$data = ['name' => '', 'email' => '', 'tour' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'tour' => trim($_POST['tour'] ?? ''),
        'message' => trim($_POST['message'] ?? '')
    ];

    if ($data['name'] && $data['email'] && filter_var($data['email'], FILTER_VALIDATE_EMAIL) && $data['message']) {
        // 🔔 Replace with your email or use Formspree/FormSubmit for static host
        $to = 'sana@example.com'; // ← CHANGE THIS
        $subject = "HomeAs: Inquiry" . ($data['tour'] ? " - " . $data['tour'] : '');
        $body = "Name: {$data['name']}\nEmail: {$data['email']}\nTour: {$data['tour']}\n\nMessage:\n{$data['message']}";
        $headers = "From: noreply@hometour.app\r\nReply-To: {$data['email']}";

        if (mail($to, $subject, $body, $headers)) {
            $success = true;
            $data = ['name' => '', 'email' => '', 'tour' => '', 'message' => '']; // reset
        } else {
            $error = "Failed to send. Try again or email directly.";
        }
    } else {
        $error = "Please fill all fields correctly.";
    }
} else {
    $data['tour'] = $_GET['tour'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | HomeTour</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script defer src="assets/js/main.js"></script>
</head>
<body>
    <header class="sub-header">
        <div class="container">
            <a href="index.php" class="logo">🏠 HomeTour</a>
        </div>
    </header>

    <section class="section contact-section">
        <div class="container">
            <h1>Schedule a Tour or Ask a Question</h1>
            
            <?php if ($success): ?>
                <div class="alert success">
                    <strong>✅ Success!</strong> Thank you! We’ll contact you within 24 hours.
                </div>
            <?php elseif ($error): ?>
                <div class="alert error">
                    <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form id="contact-form" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="tour">Interested Tour (optional)</label>
                    <input type="text" id="tour" name="tour" value="<?= htmlspecialchars($data['tour']) ?>">
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" rows="5" required><?= htmlspecialchars($data['message']) ?></textarea>
                </div>

                <button type="submit" class="btn primary large">Send Request</button>
            </form>
        </div>
    </section>
</body>
</html>