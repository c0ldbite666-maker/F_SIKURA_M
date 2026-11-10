<?php
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);
$data = body();
if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    json_out(['error' => 'Некорректный email'], 422);
}
try {
    db()->prepare('INSERT INTO subscriptions (email) VALUES (?)')->execute([$data['email']]);
} catch (PDOException $e) {
    if ($e->getCode() !== '23000') throw $e;
}
json_out(['ok' => true]);