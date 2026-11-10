<?php
require __DIR__ . '/../config/db.php';

require_admin();
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'stats';

if ($method === 'POST' || $method === 'DELETE') {
    $data = body();
    
    if ($action === 'delete_restaurant') {
        $id = (int)($data['id'] ?? 0);
        $pdo->prepare('DELETE FROM restaurants WHERE id = ?')->execute([$id]);
        json_out(['ok' => true]);
    }
    
    if ($action === 'save_restaurant') {
        $id = (int)($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $city = trim($data['city'] ?? '');
        $address = trim($data['address'] ?? '');
        $avg_check = (int)($data['avg_check'] ?? 0);
        $price_level = (int)($data['price_level'] ?? 2);
        $desc = trim($data['description'] ?? '');

        if (!$name || !$city) json_out(['error' => 'Название и город обязательны'], 422);

        if ($id) {
            // Обновление
            $sql = 'UPDATE restaurants SET name=?, city=?, address=?, avg_check=?, price_level=?, description=? WHERE id=?';
            $pdo->prepare($sql)->execute([$name, $city, $address, $avg_check, $price_level, $desc, $id]);
        } else {
            // Создание
            $sql = 'INSERT INTO restaurants (name, city, address, avg_check, price_level, description, is_active) VALUES (?,?,?,?,?,?,1)';
            $pdo->prepare($sql)->execute([$name, $city, $address, $avg_check, $price_level, $desc]);
        }
        json_out(['ok' => true]);
    }
    json_out(['error' => 'Unknown action'], 404);
}

switch ($action) {
    case 'stats':
        json_out([
            'users' => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'restaurants' => (int)$pdo->query('SELECT COUNT(*) FROM restaurants')->fetchColumn(),
            'bookings' => (int)$pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
            'reviews' => (int)$pdo->query('SELECT COUNT(*) FROM reviews')->fetchColumn(),
        ]);

    case 'restaurants':
        $list = $pdo->query('SELECT * FROM restaurants ORDER BY created_at DESC')->fetchAll();
        json_out($list);

    case 'users':
        json_out($pdo->query('SELECT id,first_name,last_name,email,city,role,created_at FROM users ORDER BY created_at DESC')->fetchAll());

    case 'reviews':
        json_out($pdo->query('SELECT rv.*, r.name AS restaurant FROM reviews rv JOIN restaurants r ON r.id = rv.restaurant_id ORDER BY rv.created_at DESC LIMIT 100')->fetchAll());

    default:
        json_out(['error' => 'Unknown'], 404);
}