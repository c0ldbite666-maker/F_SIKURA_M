CREATE DATABASE IF NOT EXISTS sikura_m CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sikura_m;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  city VARCHAR(100),
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE restaurants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  city VARCHAR(100) NOT NULL,
  address VARCHAR(255),
  phone VARCHAR(30),
  avg_check INT DEFAULT 0,
  rating DECIMAL(2,1) DEFAULT 0,
  reviews_count INT DEFAULT 0,
  price_level TINYINT DEFAULT 2,
  img VARCHAR(255),
  work_hours_week TEXT,
  work_hours_weekend TEXT,
  map_embed TEXT,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE moods (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE restaurant_moods (
  restaurant_id INT NOT NULL,
  mood_id INT NOT NULL,
  PRIMARY KEY (restaurant_id, mood_id),
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
  FOREIGN KEY (mood_id) REFERENCES moods(id) ON DELETE CASCADE
);

CREATE TABLE dishes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE restaurant_dishes (
  restaurant_id INT NOT NULL,
  dish_id INT NOT NULL,
  PRIMARY KEY (restaurant_id, dish_id),
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
  FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE
);

CREATE TABLE menu_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  restaurant_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price INT NOT NULL,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE restaurant_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  restaurant_id INT NOT NULL,
  img VARCHAR(255) NOT NULL,
  sort_order INT DEFAULT 0,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  restaurant_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_fav (user_id, restaurant_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  restaurant_id INT NOT NULL,
  user_id INT NULL,
  author_name VARCHAR(100) NOT NULL,
  rating TINYINT NOT NULL,
  text TEXT NOT NULL,
  status ENUM('pending','approved','rejected') DEFAULT 'approved',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  restaurant_id INT NOT NULL,
  user_id INT NULL,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  booking_at DATETIME NOT NULL,
  guests VARCHAR(20) NOT NULL,
  status ENUM('new','confirmed','cancelled') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE callbacks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  status ENUM('new','done') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE subscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE collections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  img VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE collection_restaurants (
  collection_id INT NOT NULL,
  restaurant_id INT NOT NULL,
  PRIMARY KEY (collection_id, restaurant_id),
  FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE CASCADE,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

INSERT INTO moods (name) VALUES
 ('Уютный вечер'),('Быстрый перекус'),('Деловой обед'),('Произвести впечатление'),('Бюджетный вариант');

INSERT INTO dishes (name) VALUES ('BBQ'),('Супы'),('Рис'),('Закуски'),('Десерты');

INSERT INTO restaurants
 (name,description,city,address,phone,avg_check,rating,reviews_count,price_level,img,work_hours_week,work_hours_weekend)
VALUES
 ('Seoul Garden','Аутентичная корейская кухня в центре Москвы.','Москва','ул. Тверская, 15','+7 (495) 123-45-67',1800,4.8,234,3,'img/rest1.jpg','Пн-Чт: 12:00 - 23:00','Пт-Вс: 12:00 - 01:00'),
 ('Gangnam House','Деловые обеды и корейская классика.','СПб','Невский пр., 40','+7 (812) 111-22-33',2500,4.5,120,4,'img/rest2.jpg','Пн-Пт: 11:00 - 23:00','Сб-Вс: 12:00 - 00:00'),
 ('Gangnam BBQ','Премиальный BBQ и авторские закуски.','Москва','ул. Арбат, 20','+7 (495) 777-88-99',3200,4.9,310,4,'img/rest3.jpg','Пн-Чт: 12:00 - 00:00','Пт-Вс: 12:00 - 02:00'),
 ('K-Town','Быстрый перекус в корейском стиле.','Владивосток','ул. Светланская, 5','+7 (423) 200-30-40',900,4.6,98,2,'img/rest4.jpg','Пн-Вс: 10:00 - 22:00','Пн-Вс: 10:00 - 22:00'),
 ('Busan Kitchen','Бюджетные супы и рис.','Казань','ул. Баумана, 10','+7 (843) 500-60-70',700,4.7,76,1,'img/rest5.jpg','Пн-Вс: 09:00 - 21:00','Пн-Вс: 09:00 - 21:00'),
 ('Kimchi House','Уютное место для вечеров.','Екатеринбург','ул. Ленина, 25','+7 (343) 300-40-50',1500,4.4,64,3,'img/rest6.jpg','Пн-Чт: 12:00 - 23:00','Пт-Вс: 12:00 - 01:00');

INSERT INTO restaurant_moods VALUES
 (1,1),(2,3),(3,4),(4,2),(5,5),(6,1);

INSERT INTO restaurant_dishes VALUES
 (1,1),(1,3),(2,3),(2,2),(3,1),(3,4),(4,4),(4,3),(5,2),(5,3),(6,4),(6,2);

INSERT INTO menu_items (restaurant_id,name,description,price) VALUES
 (1,'Кальби на гриле','Маринованные говяжьи ребра',1450),
 (1,'Пибимпап','Рис, овощи, яйцо',780),
 (1,'Кимчи-чиге','Острый суп с тофу',650),
 (2,'Пулькоги','Маринованная говядина',1200),
 (3,'Хе из осетра','Фирменная закуска',890),
 (4,'Ттокпокки','Рисовые клецки',390),
 (5,'Рамен','Острый суп с лапшой',450),
 (6,'Кимчи','Традиционная закуска',250);

INSERT INTO restaurant_images (restaurant_id,img,sort_order) VALUES
 (1,'img/rest1-1.jpg',1),(1,'img/rest1-2.jpg',2),(1,'img/rest1-3.jpg',3);

INSERT INTO reviews (restaurant_id,author_name,rating,text) VALUES
 (1,'Анна К.',5,'Отличный корейский BBQ, вернемся!'),
 (1,'Дмитрий В.',5,'Пибимпап на высшем уровне.'),
 (2,'Ольга С.',4,'Хороший деловой обед.'),
 (3,'Иван П.',5,'Лучший BBQ в Москве!');

-- Пароль admin12345 — сгенерируйте хеш командой:
-- php -r "echo password_hash('admin12345', PASSWORD_BCRYPT);"
INSERT INTO users (first_name,last_name,email,password_hash,city,role) VALUES
 ('Админ','Сикура','admin@sikura.ru','$2y$10$Q9i4e0e0e0e0e0e0e0e0eO0e0e0e0e0e0e0e0e0e0e0e0e0e0e0e','Москва','admin');
-- ↑ Замените хеш на реальный результат password_hash()