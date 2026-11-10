<?php
require __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? '';
$pdo = db();
$data = body();

switch ($action) {
    case 'register':
        foreach (['first_name','last_name','email','password','password2','city'] as $f) {
            if (empty($data[$f])) json_out(['error' => "Поле $f обязательно"], 422);
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) json_out(['error' => 'Некорректный email'], 422);
        if (strlen($data['password']) < 8) json_out(['error' => 'Пароль минимум 8 символов'], 422);
        if ($data['password'] !== $data['password2']) json_out(['error' => 'Пароли не совпадают'], 422);

        $st = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $st->execute([$data['email']]);
        if ($st->fetch()) json_out(['error' => 'Email уже занят'], 409);

        $st = $pdo->prepare('INSERT INTO users (first_name,last_name,email,password_hash,city) VALUES (?,?,?,?,?)');
        $st->execute([
            $data['first_name'], $data['last_name'], $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT), $data['city']
        ]);
        $_SESSION['user_id'] = (int)$pdo->lastInsertId();
        json_out(['ok' => true, 'user' => current_user()]);

    case 'login':
        $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $st->execute([$data['email'] ?? '']);
        $u = $st->fetch();
        if (!$u || !password_verify($data['password'] ?? '', $u['password_hash'])) {
            json_out(['error' => 'Неверный email или пароль'], 401);
        }
        $_SESSION['user_id'] = (int)$u['id'];
        unset($u['password_hash']);
        json_out(['ok' => true, 'user' => $u]);

    case 'logout':
        $_SESSION = [];
        session_destroy();
        json_out(['ok' => true]);

    case 'me':
        json_out(current_user() ?: ['user' => null]);

    default:
        json_out(['error' => 'Unknown action'], 404);
}