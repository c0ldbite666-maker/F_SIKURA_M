<?php
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);

$data = body();
$mood = $data['mood'] ?? null;
$budget = (int)($data['budget'] ?? 0);

$pdo = db();
$sql = 'SELECT DISTINCT r.* FROM restaurants r
        JOIN restaurant_moods rm ON rm.restaurant_id = r.id
        JOIN moods m ON m.id = rm.mood_id
        WHERE r.is_active = 1';
$params = [];
if ($mood) { $sql .= ' AND m.name = ?'; $params[] = $mood; }
if ($budget) { $sql .= ' AND r.price_level = ?'; $params[] = $budget; }
$sql .= ' ORDER BY RAND() LIMIT 1';

$st = $pdo->prepare($sql);
$st->execute($params);
$r = $st->fetch();

if (!$r && $mood) {
    $st = $pdo->prepare('SELECT DISTINCT r.* FROM restaurants r JOIN restaurant_moods rm ON rm.restaurant_id = r.id JOIN moods m ON m.id = rm.mood_id WHERE r.is_active = 1 AND m.name = ? ORDER BY RAND() LIMIT 1');
    $st->execute([$mood]);
    $r = $st->fetch();
}
if (!$r) {
    $r = $pdo->query('SELECT * FROM restaurants WHERE is_active=1 ORDER BY RAND() LIMIT 1')->fetch();
}
if (!$r) json_out(['error' => 'Нет вариантов'], 404);

$r['price'] = str_repeat('₽', (int)$r['price_level']);
$r['stars'] = str_repeat('★', (int)round((float)$r['rating'])) . str_repeat('☆', 5 - (int)round((float)$r['rating']));

$st = $pdo->prepare('SELECT m.name FROM restaurant_moods rm JOIN moods m ON m.id = rm.mood_id WHERE rm.restaurant_id = ?');
$st->execute([$r['id']]);
$r['moods'] = array_column($st->fetchAll(), 'name');

json_out($r);