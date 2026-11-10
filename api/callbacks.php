<?php
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);
$data = body();
if (empty($data['name']) || empty($data['phone'])) json_out(['error' => 'Поля обязательны'], 422);
db()->prepare('INSERT INTO callbacks (name,phone) VALUES (?,?)')->execute([$data['name'], $data['phone']]);
json_out(['ok' => true]);