<?php
declare(strict_types=1);


require_once __DIR__ . '/../src/validate.php';

$name = '';
$email = '';
$message = '';
$errors = [];

// Show success message after redirect
if ($_SERVER['REQUEST_METHOD'] === 'GET'
    && isset($_GET['success'])
    && $_GET['success'] === '1') {
    $success = true;
} else {
    $success = false;
}

// Handle submitted form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = is_string($_POST['name'] ?? null)
        ? $_POST['name'] : '';

    $email = is_string($_POST['email'] ?? null)
        ? $_POST['email'] : '';

    $message = is_string($_POST['message'] ?? null)
        ? $_POST['message'] : '';

    // Validate name
    if (!validate_required($name)) {
        $errors['name'] = 'Name is required.';
    } elseif (!validate_length(trim($name), 2, 60)) {
     $errors['name'] = 'Invalid name: must be 2 to 60 characters.';

    }

    // Validate email
    if (!validate_email($email)) {
        $errors['email'] = 'Please enter a valid email.';
    }

    // Validate message
    if (!validate_required($message)) {
        $errors['message'] = 'Message is required.';
    } elseif (!validate_length(trim($message), 10, 1000)) {
        $errors['message'] = 'Message must be 10 to 1000 characters.';
    }

    // PRG: Redirect after successful POST
    if (empty($errors)) {
        header('Location: index.php?success=1', true, 303);
        exit;
    }
}

// Escape output to prevent XSS
function e(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contact Us</title>
</head>
<body>

<h1>Contact Us</h1>

<?php if ($success): ?>

    <p>Thank you! Your message was sent successfully.</p>

<?php else: ?>

    <form method="post" action="index.php">

        <div>
            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= e($name) ?>"
            >

            <?php if (isset($errors['name'])): ?>
                <span><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <br>

        <div>
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= e($email) ?>"
            >

            <?php if (isset($errors['email'])): ?>
                <span><?= e($errors['email']) ?></span>
            <?php endif; ?>
        </div>

        <br>

        <div>
            <label for="message">Message</label>
            <textarea
                id="message"
                name="message"
            ><?= e($message) ?></textarea>

            <?php if (isset($errors['message'])): ?>
                <span><?= e($errors['message']) ?></span>
            <?php endif; ?>
        </div>

        <br>

        <button type="submit">Send</button>

    </form>

<?php endif; ?>

</body>
</html>

