<?php
require __DIR__ . '/../config/db.php';

$user = require_auth();
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $st = $pdo->prepare(
        'SELECT r.* FROM favorites f JOIN restaurants r ON r.id = f.restaurant_id WHERE f.user_id = ? ORDER BY f.created_at DESC'
    );
    $st->execute([$user['id']]);
    $list = $st->fetchAll();
    foreach ($list as &$r) {
        $r['price'] = str_repeat('₽', (int)$r['price_level']);
        $r['stars'] = str_repeat('★', (int)round((float)$r['rating'])) . str_repeat('☆', 5 - (int)round((float)$r['rating']));
    }
    unset($r);
    json_out($list);
}

if ($method === 'POST') {
    $data = body();
    $rid = (int)($data['restaurant_id'] ?? 0);
    if (!$rid) json_out(['error' => 'restaurant_id обязателен'], 422);

    $st = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND restaurant_id = ?');
    $st->execute([$user['id'], $rid]);
    if ($st->fetch()) {
        $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND restaurant_id = ?')->execute([$user['id'], $rid]);
        json_out(['ok' => true, 'favorited' => false]);
    }
    $pdo->prepare('INSERT INTO favorites (user_id,restaurant_id) VALUES (?,?)')->execute([$user['id'], $rid]);
    json_out(['ok' => true, 'favorited' => true]);
}

json_out(['error' => 'Метод не поддерживается'], 405);