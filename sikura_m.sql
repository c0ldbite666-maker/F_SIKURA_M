-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Ноя 10 2026 г., 08:56
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `sikura_m`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `booking_at` datetime NOT NULL,
  `guests` varchar(20) NOT NULL,
  `status` enum('new','confirmed','cancelled') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `callbacks`
--

CREATE TABLE `callbacks` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `status` enum('new','done') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `callbacks`
--

INSERT INTO `callbacks` (`id`, `name`, `phone`, `status`, `created_at`) VALUES
(1, 'андрей', '+79261234567', 'new', '2026-11-03 22:28:34');

-- --------------------------------------------------------

--
-- Структура таблицы `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `menu_item_id`, `quantity`, `created_at`) VALUES
(3, 2, 1, 1, '2026-11-08 22:56:12');

-- --------------------------------------------------------

--
-- Структура таблицы `collections`
--

CREATE TABLE `collections` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `collection_restaurants`
--

CREATE TABLE `collection_restaurants` (
  `collection_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `dishes`
--

CREATE TABLE `dishes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `dishes`
--

INSERT INTO `dishes` (`id`, `name`) VALUES
(1, 'BBQ'),
(5, 'Десерты'),
(4, 'Закуски'),
(3, 'Рис'),
(2, 'Супы');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `restaurant_id`, `created_at`) VALUES
(5, 2, 3, '2026-10-26 20:56:11'),
(7, 2, 1, '2026-11-03 08:08:17'),
(8, 3, 1, '2026-11-03 08:10:20'),
(9, 1, 1, '2026-11-10 07:52:34');

-- --------------------------------------------------------

--
-- Структура таблицы `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `menu_items`
--

INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`) VALUES
(1, 1, 'Кальби на гриле', 'Маринованные говяжьи ребра', 1450),
(2, 1, 'Пибимпап', 'Рис, овощи, яйцо', 780),
(3, 1, 'Кимчи-чиге', 'Острый суп с тофу', 650),
(4, 2, 'Пулькоги', 'Маринованная говядина', 1200),
(5, 3, 'Хе из осетра', 'Фирменная закуска', 890),
(6, 4, 'Ттокпокки', 'Рисовые клецки', 390),
(7, 5, 'Рамен', 'Острый суп с лапшой', 450),
(8, 6, 'Кимчи', 'Традиционная закуска', 250);

-- --------------------------------------------------------

--
-- Структура таблицы `moods`
--

CREATE TABLE `moods` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `moods`
--

INSERT INTO `moods` (`id`, `name`) VALUES
(2, 'Быстрый перекус'),
(5, 'Бюджетный вариант'),
(3, 'Деловой обед'),
(4, 'Произвести впечатление'),
(1, 'Уютный вечер');

-- --------------------------------------------------------

--
-- Структура таблицы `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `avg_check` int(11) DEFAULT 0,
  `rating` decimal(2,1) DEFAULT 0.0,
  `reviews_count` int(11) DEFAULT 0,
  `price_level` tinyint(4) DEFAULT 2,
  `img` varchar(255) DEFAULT NULL,
  `work_hours_week` text DEFAULT NULL,
  `work_hours_weekend` text DEFAULT NULL,
  `map_embed` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `city`, `address`, `phone`, `avg_check`, `rating`, `reviews_count`, `price_level`, `img`, `work_hours_week`, `work_hours_weekend`, `map_embed`, `is_active`, `created_at`) VALUES
(1, 'Seoul Garden', 'Аутентичная корейская кухня в центре Москвы.', 'Москва', 'ул. Тверская, 15', '+7 (495) 123-45-67', 1800, 5.0, 4, 3, 'img/rest1.jpg', 'Пн-Чт: 12:00 - 23:00', 'Пт-Вс: 12:00 - 01:00', NULL, 1, '2026-10-26 10:18:53'),
(2, 'Gangnam House', 'Деловые обеды и корейская классика.', 'СПб', 'Невский пр., 40', '+7 (812) 111-22-33', 2500, 4.5, 120, 4, 'img/rest2.jpg', 'Пн-Пт: 11:00 - 23:00', 'Сб-Вс: 12:00 - 00:00', NULL, 1, '2026-10-26 10:18:53'),
(3, 'Gangnam BBQ', 'Премиальный BBQ и авторские закуски.', 'Москва', 'ул. Арбат, 20', '+7 (495) 777-88-99', 3200, 4.9, 310, 4, 'img/rest3.jpg', 'Пн-Чт: 12:00 - 00:00', 'Пт-Вс: 12:00 - 02:00', NULL, 1, '2026-10-26 10:18:53'),
(4, 'K-Town', 'Быстрый перекус в корейском стиле.', 'Владивосток', 'ул. Светланская, 5', '+7 (423) 200-30-40', 900, 4.6, 98, 2, 'img/rest4.jpg', 'Пн-Вс: 10:00 - 22:00', 'Пн-Вс: 10:00 - 22:00', NULL, 1, '2026-10-26 10:18:53'),
(5, 'Busan Kitchen', 'Бюджетные супы и рис.', 'Казань', 'ул. Баумана, 10', '+7 (843) 500-60-70', 700, 4.7, 76, 1, 'img/rest5.jpg', 'Пн-Вс: 09:00 - 21:00', 'Пн-Вс: 09:00 - 21:00', NULL, 1, '2026-10-26 10:18:53'),
(6, 'Kimchi House', 'Уютное место для вечеров.', 'Екатеринбург', 'ул. Ленина, 25', '+7 (343) 300-40-50', 1500, 2.0, 1, 3, 'img/rest6.jpg', 'Пн-Чт: 12:00 - 23:00', 'Пт-Вс: 12:00 - 01:00', NULL, 1, '2026-10-26 10:18:53');

-- --------------------------------------------------------

--
-- Структура таблицы `restaurant_dishes`
--

CREATE TABLE `restaurant_dishes` (
  `restaurant_id` int(11) NOT NULL,
  `dish_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `restaurant_dishes`
--

INSERT INTO `restaurant_dishes` (`restaurant_id`, `dish_id`) VALUES
(1, 1),
(1, 3),
(2, 2),
(2, 3),
(3, 1),
(3, 4),
(4, 3),
(4, 4),
(5, 2),
(5, 3),
(6, 2),
(6, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `restaurant_images`
--

CREATE TABLE `restaurant_images` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `restaurant_images`
--

INSERT INTO `restaurant_images` (`id`, `restaurant_id`, `img`, `sort_order`) VALUES
(1, 1, 'img/rest1-1.jpg', 1),
(2, 1, 'img/rest1-2.jpg', 2),
(3, 1, 'img/rest1-3.jpg', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `restaurant_moods`
--

CREATE TABLE `restaurant_moods` (
  `restaurant_id` int(11) NOT NULL,
  `mood_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `restaurant_moods`
--

INSERT INTO `restaurant_moods` (`restaurant_id`, `mood_id`) VALUES
(1, 1),
(2, 3),
(3, 4),
(4, 2),
(5, 5),
(6, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `author_name` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `text` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `restaurant_id`, `user_id`, `author_name`, `rating`, `text`, `status`, `created_at`) VALUES
(1, 1, NULL, 'Анна К.', 5, 'Отличный корейский BBQ, вернемся!', 'approved', '2026-10-26 10:18:53'),
(2, 1, NULL, 'Дмитрий В.', 5, 'Пибимпап на высшем уровне.', 'approved', '2026-10-26 10:18:53'),
(3, 2, NULL, 'Ольга С.', 4, 'Хороший деловой обед.', 'approved', '2026-10-26 10:18:53'),
(4, 3, NULL, 'Иван П.', 5, 'Лучший BBQ в Москве!', 'approved', '2026-10-26 10:18:53'),
(5, 1, 3, 'Андрей', 5, 'очень вкусно', 'approved', '2026-11-03 08:12:22'),
(6, 6, 2, 'admin', 2, 'фууу', 'approved', '2026-11-03 22:28:04'),
(7, 1, 2, 'ляляляляляяляляялляля', 5, 'все шикарно', 'approved', '2026-11-08 22:56:31');

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `city`, `role`, `created_at`) VALUES
(1, 'Админ', 'Сикура', 'admin@sikura.ru', '$2y$10$72wXwWbmlLGRlEzMJbfR0e06H1VmbJvmFTPcz7bM2WQxh3SwxqtUO', 'Москва', 'admin', '2026-10-26 10:18:53'),
(2, 'admin', 'Иванова', 'admin@gmail.com', '$2y$10$JKAu7vvNqk8Rc2FSdWAtROj2J//cupYcu/mooJhSTIWFRPrwSOZme', 'астрахань', 'user', '2026-10-26 20:20:25'),
(3, 'Андрей', 'Шкачихин', 'andrei.shkachikhin@mail.ru', '$2y$10$Z79gxeW2L.6OXubab/9BDea4u9ccZn2kXuDp5F0OLji5XqN/b22Yy', 'астрахань', 'user', '2026-11-03 08:10:10');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Индексы таблицы `callbacks`
--
ALTER TABLE `callbacks`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart` (`user_id`,`menu_item_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Индексы таблицы `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `collection_restaurants`
--
ALTER TABLE `collection_restaurants`
  ADD PRIMARY KEY (`collection_id`,`restaurant_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Индексы таблицы `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fav` (`user_id`,`restaurant_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Индексы таблицы `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Индексы таблицы `moods`
--
ALTER TABLE `moods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `restaurant_dishes`
--
ALTER TABLE `restaurant_dishes`
  ADD PRIMARY KEY (`restaurant_id`,`dish_id`),
  ADD KEY `dish_id` (`dish_id`);

--
-- Индексы таблицы `restaurant_images`
--
ALTER TABLE `restaurant_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Индексы таблицы `restaurant_moods`
--
ALTER TABLE `restaurant_moods`
  ADD PRIMARY KEY (`restaurant_id`,`mood_id`),
  ADD KEY `mood_id` (`mood_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `callbacks`
--
ALTER TABLE `callbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `collections`
--
ALTER TABLE `collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dishes`
--
ALTER TABLE `dishes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `moods`
--
ALTER TABLE `moods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `restaurant_images`
--
ALTER TABLE `restaurant_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `collection_restaurants`
--
ALTER TABLE `collection_restaurants`
  ADD CONSTRAINT `collection_restaurants_ibfk_1` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collection_restaurants_ibfk_2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `restaurant_dishes`
--
ALTER TABLE `restaurant_dishes`
  ADD CONSTRAINT `restaurant_dishes_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_dishes_ibfk_2` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `restaurant_images`
--
ALTER TABLE `restaurant_images`
  ADD CONSTRAINT `restaurant_images_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `restaurant_moods`
--
ALTER TABLE `restaurant_moods`
  ADD CONSTRAINT `restaurant_moods_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_moods_ibfk_2` FOREIGN KEY (`mood_id`) REFERENCES `moods` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
