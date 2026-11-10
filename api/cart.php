<?php
require __DIR__ . '/../config/db.php';

$user = require_auth();
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $st = $pdo->prepare('
        SELECT c.id AS cart_id, c.quantity, m.id AS item_id, m.name, m.price, m.description
        FROM cart c
        JOIN menu_items m ON m.id = c.menu_item_id
        WHERE c.user_id = ?
    ');
    $st->execute([$user['id']]);
    $items = $st->fetchAll();
    
    // Считаем итоги на сервере
    $total = 0;
    foreach ($items as &$item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    json_out(['items' => $items, 'total' => $total]);
}

$data = body();

if ($method === 'POST') {
    $itemId = (int)($data['menu_item_id'] ?? 0);
    $action = $data['action'] ?? 'add';
    
    if (!$itemId) json_out(['error' => 'ID блюда обязателен'], 422);

    if ($action === 'add') {
        $pdo->prepare('
            INSERT INTO cart (user_id, menu_item_id, quantity) VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE quantity = quantity + 1
        ')->execute([$user['id'], $itemId]);
    } elseif ($action === 'update') {
        $qty = (int)$data['quantity'];
        if ($qty <= 0) {
            $pdo->prepare('DELETE FROM cart WHERE user_id = ? AND menu_item_id = ?')->execute([$user['id'], $itemId]);
        } else {
            $pdo->prepare('UPDATE cart SET quantity = ? WHERE user_id = ? AND menu_item_id = ?')->execute([$qty, $user['id'], $itemId]);
        }
    } elseif ($action === 'remove') {
        $pdo->prepare('DELETE FROM cart WHERE user_id = ? AND menu_item_id = ?')->execute([$user['id'], $itemId]);
    }
    json_out(['ok' => true]);
}