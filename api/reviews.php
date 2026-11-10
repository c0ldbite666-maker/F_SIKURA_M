<?php
require __DIR__ . '/../config/db.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rid = (int)($_GET['restaurant_id'] ?? 0);
    if (!$rid) json_out(['error' => 'restaurant_id обязателен'], 422);
    $st = $pdo->prepare('SELECT id,author_name,rating,text,created_at FROM reviews WHERE restaurant_id = ? AND status = "approved" ORDER BY created_at DESC');
    $st->execute([$rid]);
    json_out($st->fetchAll());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);

$data = body();
$rid = (int)($data['restaurant_id'] ?? 0);
$rating = (int)($data['rating'] ?? 0);
$text = trim($data['text'] ?? '');
$name = trim($data['author_name'] ?? '');
$user = current_user();

if (!$rid || $rating < 1 || $rating > 5 || !$text) json_out(['error' => 'Проверьте поля'], 422);
if (!$name && $user) $name = $user['first_name'];
if (!$name) json_out(['error' => 'Укажите имя'], 422);

$st = $pdo->prepare('INSERT INTO reviews (restaurant_id,user_id,author_name,rating,text) VALUES (?,?,?,?,?)');
$st->execute([$rid, $user['id'] ?? null, $name, $rating, $text]);

$st = $pdo->prepare('SELECT AVG(rating) a, COUNT(*) c FROM reviews WHERE restaurant_id = ? AND status = "approved"');
$st->execute([$rid]);
$agg = $st->fetch();
$pdo->prepare('UPDATE restaurants SET rating = ?, reviews_count = ? WHERE id = ?')
    ->execute([round((float)$agg['a'], 1), (int)$agg['c'], $rid]);

json_out(['ok' => true]);