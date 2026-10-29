<?php
declare(strict_types=1);

$directAccess = $_SERVER['REQUEST_METHOD'] !== 'POST';

function postString(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

$name = postString('name');
$email = postString('email');
$deviceType = postString('device_type');
$deliverySpeed = postString('delivery_speed');
$priceQuality = postString('price_quality');
$comments = postString('comments');

$allowedDeviceTypes = ['Смартфони', 'Ноутбуки', 'Аксесуари', 'Побутова техніка'];
$allowedDeliverySpeeds = ['Швидко (1-2 дні)', 'Середньо (3-5 днів)', 'Повільно'];
$allowedPriceQuality = ['Відмінне', 'Добре', 'Задовільне', 'Незадовільне'];

$errors = [];
if ($name === '') {
    $errors[] = 'Вкажіть ваше ім’я.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Вкажіть коректну електронну адресу.';
}
if (!in_array($deviceType, $allowedDeviceTypes, true)) {
    $errors[] = 'Оберіть категорію техніки.';
}
if (!in_array($deliverySpeed, $allowedDeliverySpeeds, true)) {
    $errors[] = 'Оберіть оцінку швидкості доставки.';
}
if (!in_array($priceQuality, $allowedPriceQuality, true)) {
    $errors[] = 'Оберіть оцінку співвідношення ціни та якості.';
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$submissionTime = date('Y-m-d H:i:s');
$pageTitle = 'Результат опитування';
$message = '';
$isSuccess = false;

if ($directAccess) {
    $pageTitle = 'Надсилання відгуку';
    $message = '<p>Ця сторінка обробляє відгуки. Щоб надіслати відповідь, заповніть форму опитування.</p>';
} elseif ($errors !== []) {
    http_response_code(400);
    $message = '<p>Перевірте, будь ласка, заповнені поля:</p><ul>';
    foreach ($errors as $error) {
        $message .= '<li>' . escape($error) . '</li>';
    }
    $message .= '</ul>';
} else {
    $responseDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'responses';
    if (!is_dir($responseDirectory) && !mkdir($responseDirectory, 0775, true) && !is_dir($responseDirectory)) {
        http_response_code(500);
        $message = '<p>Не вдалося створити папку для збереження відповідей.</p>';
    } else {
        $filename = $responseDirectory . DIRECTORY_SEPARATOR
            . 'survey_' . date('Y-m-d_H-i-s') . '_' . bin2hex(random_bytes(4)) . '.txt';
        $text = "Відгук DigitalSmart\n"
            . "Дата та час: {$submissionTime}\n"
            . "Ім’я: {$name}\n"
            . "Email: {$email}\n"
            . "Категорія техніки: {$deviceType}\n"
            . "Швидкість доставки: {$deliverySpeed}\n"
            . "Співвідношення ціни та якості: {$priceQuality}\n"
            . "Побажання та зауваження:\n{$comments}\n";

        if (file_put_contents($filename, $text, LOCK_EX) === false) {
            http_response_code(500);
            $message = '<p>Не вдалося зберегти вашу відповідь. Спробуйте ще раз пізніше.</p>';
        } else {
            $isSuccess = true;
            $message = '<p>Анкету успішно надіслано.</p>'
                . '<p><strong>Час та дата заповнення:</strong> ' . escape($submissionTime) . '</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?> — DigitalSmart</title>
    <link rel="stylesheet" href="../style.css">
    <script src="../script.js" defer></script>
</head>
<body>
    <header>
        <h1>DigitalSmart</h1>
        <p>Магазин сучасної техніки</p>
    </header>
    <main>
        <section class="survey-success">
            <h2><?= $directAccess ? 'Обробка відгуку' : ($isSuccess ? 'Дякуємо за ваш відгук!' : 'Не вдалося надіслати анкету') ?></h2>
            <?= $message ?>
            <a class="back-link" href="survey.html">Повернутися до опитування</a>
        </section>
    </main>
    <a href="../index.html" class="home-button">🏠 На головну</a>
</body>
</html>
