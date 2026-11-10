<?php
require __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = db();

if ($method !== 'GET') json_out(['error' => 'GET only'], 405);

if (!empty($_GET['id'])) {
    $st = $pdo->prepare('SELECT * FROM restaurants WHERE id = ? AND is_active = 1');
    $st->execute([(int)$_GET['id']]);
    $r = $st->fetch();
    if (!$r) json_out(['error' => 'Не найдено'], 404);

    $st = $pdo->prepare('SELECT m.name FROM restaurant_moods rm JOIN moods m ON m.id = rm.mood_id WHERE rm.restaurant_id = ?');
    $st->execute([$r['id']]);
    $r['moods'] = array_column($st->fetchAll(), 'name');

    $st = $pdo->prepare('SELECT d.name FROM restaurant_dishes rd JOIN dishes d ON d.id = rd.dish_id WHERE rd.restaurant_id = ?');
    $st->execute([$r['id']]);
    $r['dishes'] = array_column($st->fetchAll(), 'name');

    $st = $pdo->prepare('SELECT id,name,description,price FROM menu_items WHERE restaurant_id = ?');
    $st->execute([$r['id']]);
    $r['menu'] = $st->fetchAll();

    $st = $pdo->prepare('SELECT img FROM restaurant_images WHERE restaurant_id = ? ORDER BY sort_order');
    $st->execute([$r['id']]);
    $r['gallery'] = array_column($st->fetchAll(), 'img');
    if (!$r['gallery'] && $r['img']) $r['gallery'] = [$r['img']];

    $st = $pdo->prepare('SELECT id,author_name,rating,text,created_at FROM reviews WHERE restaurant_id = ? AND status = "approved" ORDER BY created_at DESC');
    $st->execute([$r['id']]);
    $r['reviews'] = $st->fetchAll();

    $r['price'] = str_repeat('₽', (int)$r['price_level']);
    $r['stars'] = str_repeat('★', (int)round((float)$r['rating'])) . str_repeat('☆', 5 - (int)round((float)$r['rating']));

    json_out($r);
}

$sql = 'SELECT DISTINCT r.* FROM restaurants r';
$joins = '';
$where = ['r.is_active = 1'];
$params = [];

if (!empty($_GET['moods'])) {
    $moods = array_filter(array_map('trim', explode(',', $_GET['moods'])));
    if ($moods) {
        $joins .= ' JOIN restaurant_moods rm ON rm.restaurant_id = r.id JOIN moods m ON m.id = rm.mood_id';
        $in = implode(',', array_fill(0, count($moods), '?'));
        $where[] = "m.name IN ($in)";
        $params = array_merge($params, $moods);
    }
}
if (!empty($_GET['dishes'])) {
    $dishes = array_filter(array_map('trim', explode(',', $_GET['dishes'])));
    if ($dishes) {
        $joins .= ' JOIN restaurant_dishes rd ON rd.restaurant_id = r.id JOIN dishes d ON d.id = rd.dish_id';
        $in = implode(',', array_fill(0, count($dishes), '?'));
        $where[] = "d.name IN ($in)";
        $params = array_merge($params, $dishes);
    }
}
if (!empty($_GET['cities'])) {
    $cities = array_filter(array_map('trim', explode(',', $_GET['cities'])));
    if ($cities) {
        $in = implode(',', array_fill(0, count($cities), '?'));
        $where[] = "r.city IN ($in)";
        $params = array_merge($params, $cities);
    }
}
if (!empty($_GET['budgets'])) {
    $budgets = array_filter(array_map('intval', explode(',', $_GET['budgets'])));
    if ($budgets) {
        $in = implode(',', array_fill(0, count($budgets), '?'));
        $where[] = "r.price_level IN ($in)";
        $params = array_merge($params, $budgets);
    }
}
if (!empty($_GET['q'])) {
    $where[] = '(r.name LIKE ? OR r.city LIKE ?)';
    $q = '%' . $_GET['q'] . '%';
    $params[] = $q;
    $params[] = $q;
}

$sql .= $joins . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY r.rating DESC';
$st = $pdo->prepare($sql);
$st->execute($params);
$list = $st->fetchAll();

$ids = array_column($list, 'id');
$moodsByR = [];
if ($ids) {
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare("SELECT rm.restaurant_id, m.name FROM restaurant_moods rm JOIN moods m ON m.id = rm.mood_id WHERE rm.restaurant_id IN ($in)");
    $st->execute($ids);
    foreach ($st->fetchAll() as $row) {
        $moodsByR[$row['restaurant_id']][] = $row['name'];
    }
}

foreach ($list as &$r) {
    $r['moods'] = $moodsByR[$r['id']] ?? [];
    $r['price'] = str_repeat('₽', (int)$r['price_level']);
    $r['stars'] = str_repeat('★', (int)round((float)$r['rating'])) . str_repeat('☆', 5 - (int)round((float)$r['rating']));
}
unset($r);

json_out($list);