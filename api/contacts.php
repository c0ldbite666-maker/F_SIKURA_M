<?php
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);
$data = body();
foreach (['name','email','message'] as $f) {
    if (empty($data[$f])) json_out(['error' => "Поле $f обязательно"], 422);
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) json_out(['error' => 'Некорректный email'], 422);
db()->prepare('INSERT INTO contact_messages (name,email,message) VALUES (?,?,?)')
    ->execute([$data['name'], $data['email'], $data['message']]);
json_out(['ok' => true]);