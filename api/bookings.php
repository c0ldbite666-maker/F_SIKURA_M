<?php
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);
$data = body();

foreach (['restaurant_id','name','phone','booking_at','guests'] as $f) {
    if (empty($data[$f])) json_out(['error' => "Поле $f обязательно"], 422);
}
$user = current_user();
$pdo = db();
$pdo->prepare('INSERT INTO bookings (restaurant_id,user_id,name,phone,booking_at,guests) VALUES (?,?,?,?,?,?)')
    ->execute([
        (int)$data['restaurant_id'],
        $user['id'] ?? null,
        $data['name'],
        $data['phone'],
        $data['booking_at'],
        $data['guests'],
    ]);
json_out(['ok' => true]);