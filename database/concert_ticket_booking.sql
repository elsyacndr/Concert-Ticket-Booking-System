-- ============================================================
-- Concert Ticket Booking System - Database Schema & Seed Data
-- Database: concert_ticket_booking
-- ============================================================

CREATE DATABASE IF NOT EXISTS `concert_ticket_booking` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `concert_ticket_booking`;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT(11) NOT NULL AUTO_INCREMENT,
    `role`       ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(100) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `phone`      VARCHAR(20) DEFAULT NULL,
    `photo`      VARCHAR(255) DEFAULT 'default.png',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: events
-- ============================================================
CREATE TABLE IF NOT EXISTS `events` (
    `id`          INT(11) NOT NULL AUTO_INCREMENT,
    `code`        VARCHAR(20) NOT NULL UNIQUE,
    `name`        VARCHAR(150) NOT NULL,
    `artist`      VARCHAR(150) NOT NULL,
    `location`    VARCHAR(200) NOT NULL,
    `event_date`  DATE NOT NULL,
    `event_time`  TIME NOT NULL,
    `price`       DECIMAL(12,2) NOT NULL DEFAULT 0,
    `quota`       INT(11) NOT NULL DEFAULT 0,
    `sold`        INT(11) NOT NULL DEFAULT 0,
    `poster`      VARCHAR(255) DEFAULT 'default_poster.jpg',
    `description` TEXT DEFAULT NULL,
    `status`      ENUM('upcoming','ongoing','finished','cancelled') NOT NULL DEFAULT 'upcoming',
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: bookings
-- ============================================================
CREATE TABLE IF NOT EXISTS `bookings` (
    `id`            INT(11) NOT NULL AUTO_INCREMENT,
    `user_id`       INT(11) NOT NULL,
    `event_id`      INT(11) NOT NULL,
    `booking_code`  VARCHAR(30) NOT NULL UNIQUE,
    `total_ticket`  INT(11) NOT NULL DEFAULT 1,
    `total_price`   DECIMAL(12,2) NOT NULL DEFAULT 0,
    `booking_date`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payment_proof` VARCHAR(255) DEFAULT NULL,
    `status`        ENUM('pending','paid','rejected','completed') NOT NULL DEFAULT 'pending',
    `notes`         TEXT DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE CASCADE,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA: Admin
-- password: password (hashed with password_hash)
-- ============================================================
INSERT INTO `users` (`role`, `name`, `email`, `password`, `phone`, `photo`) VALUES
('admin', 'Super Admin', 'admin@concert.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081200000000', 'default.png');

-- ============================================================
-- SEED DATA: 10 Customers
-- password: password123
-- ============================================================
INSERT INTO `users` (`role`, `name`, `email`, `password`, `phone`, `photo`) VALUES
('customer', 'Rizky Pratama',    'rizky@mail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081211111111', 'default.png'),
('customer', 'Siti Rahayu',      'siti@mail.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081222222222', 'default.png'),
('customer', 'Budi Santoso',     'budi@mail.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081233333333', 'default.png'),
('customer', 'Dewi Anggraini',   'dewi@mail.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081244444444', 'default.png'),
('customer', 'Andi Wijaya',      'andi@mail.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081255555555', 'default.png'),
('customer', 'Nurul Hidayah',    'nurul@mail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081266666666', 'default.png'),
('customer', 'Fajar Ramadhan',   'fajar@mail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081277777777', 'default.png'),
('customer', 'Indah Permata',    'indah@mail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081288888888', 'default.png'),
('customer', 'Hendra Kusuma',    'hendra@mail.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081299999999', 'default.png'),
('customer', 'Lestari Wulandari','lestari@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081200001111', 'default.png');

-- ============================================================
-- SEED DATA: 10 Events
-- ============================================================
INSERT INTO `events` (`code`, `name`, `artist`, `location`, `event_date`, `event_time`, `price`, `quota`, `sold`, `poster`, `description`, `status`) VALUES
('EVT001', 'NEON LIGHTS FESTIVAL 2026',     'Coldplay x Imagine Dragons',  'Gelora Bung Karno, Jakarta',      '2026-07-15', '18:00:00', 850000,  5000, 1200, 'default_poster.jpg', 'Festival musik terbesar tahun ini! Dua band legendaris dunia hadir di Jakarta untuk malam yang tak terlupakan. Nikmati pertunjukan cahaya spektakuler dan musik yang memukau.', 'upcoming'),
('EVT002', 'SOUNDWAVE CAMPUS FEST',         'Sheila On 7 & Payung Teduh',  'Lapangan Universitas Indonesia',  '2026-06-20', '15:00:00', 150000,  3000,  890, 'default_poster.jpg', 'Festival musik kampus terbesar se-Indonesia! Dua band indie favorit tampil live untuk mahasiswa dan pecinta musik indie.', 'upcoming'),
('EVT003', 'PURPLE HAZE NIGHT',             'Tulus & Raisa',               'Istora Senayan, Jakarta',         '2026-06-28', '19:30:00', 450000,  2000,  750, 'default_poster.jpg', 'Malam romantis bersama dua penyanyi terbaik Indonesia. Rasakan keindahan musik pop Indonesia yang menyentuh hati.', 'upcoming'),
('EVT004', 'ELECTRIC STORM RAVE',           'DJ Marshmello & DJ Snake',    'Beach City International Stadium','2026-08-10', '21:00:00', 650000,  8000, 3200, 'default_poster.jpg', 'Pesta EDM terbesar! Dua DJ kelas dunia hadir untuk menggoyang malam Anda dengan beat yang luar biasa.', 'upcoming'),
('EVT005', 'INDIE VIBES SHOWCASE',          'Fourtwnty & Hindia',          'Taman Ismail Marzuki, Jakarta',   '2026-05-30', '16:00:00', 200000,  1500,  980, 'default_poster.jpg', 'Showcase musik indie terbaik Indonesia. Nikmati alunan musik yang thoughtful dan lirik yang bermakna.', 'ongoing'),
('EVT006', 'RETRO WAVE CONCERT',            'Dewa 19 Reunion',             'Stadion Utama GBK, Jakarta',      '2026-05-10', '19:00:00', 350000,  6000, 5800, 'default_poster.jpg', 'Reuni legendaris Dewa 19! Nostalgia bersama lagu-lagu hits yang menemani generasi 90an.', 'finished'),
('EVT007', 'K-WAVE FESTIVAL INDONESIA',     'BLACKPINK & BTS',             'Jakarta International Expo',      '2026-09-05', '17:00:00', 1200000, 10000, 4500, 'default_poster.jpg', 'Festival K-Pop terbesar di Asia Tenggara! Dua grup K-Pop terpopuler dunia hadir di Jakarta.', 'upcoming'),
('EVT008', 'ACOUSTIC SOUL NIGHT',           'Yura Yunita & Isyana Sarasvati','Balai Sarbini, Jakarta',        '2026-07-22', '19:00:00', 300000,  1000,  320, 'default_poster.jpg', 'Malam akustik yang intim dan penuh jiwa. Dua penyanyi berbakat Indonesia tampil dengan aransemen akustik yang memukau.', 'upcoming'),
('EVT009', 'METAL MAYHEM FESTIVAL',         'Burgerkill & Seringai',       'Lapangan Parkir JIEXPO',          '2026-04-15', '18:00:00', 250000,  4000, 3900, 'default_poster.jpg', 'Festival metal terbesar Indonesia! Dua band metal terbaik tanah air hadir untuk menggempur telinga Anda.', 'finished'),
('EVT010', 'JAZZ UNDER THE STARS',          'Barry Likumahuwa & Tompi',    'Amphitheater Ancol, Jakarta',     '2026-08-30', '19:30:00', 400000,  800,   150, 'default_poster.jpg', 'Malam jazz yang elegan di bawah bintang. Nikmati alunan jazz terbaik Indonesia di venue outdoor yang indah.', 'upcoming');

-- ============================================================
-- SEED DATA: Sample Bookings
-- ============================================================
INSERT INTO `bookings` (`user_id`, `event_id`, `booking_code`, `total_ticket`, `total_price`, `booking_date`, `payment_proof`, `status`) VALUES
(2,  1, 'BK-2026-000001', 2, 1700000, '2026-05-01 10:00:00', 'payment_sample.jpg', 'completed'),
(3,  2, 'BK-2026-000002', 3,  450000, '2026-05-02 11:00:00', 'payment_sample.jpg', 'completed'),
(4,  3, 'BK-2026-000003', 1,  450000, '2026-05-03 12:00:00', 'payment_sample.jpg', 'paid'),
(5,  4, 'BK-2026-000004', 4, 2600000, '2026-05-04 13:00:00', 'payment_sample.jpg', 'paid'),
(6,  5, 'BK-2026-000005', 2,  400000, '2026-05-05 14:00:00', NULL,                 'pending'),
(7,  7, 'BK-2026-000006', 2, 2400000, '2026-05-06 15:00:00', 'payment_sample.jpg', 'completed'),
(8,  8, 'BK-2026-000007', 1,  300000, '2026-05-07 16:00:00', NULL,                 'pending'),
(9,  1, 'BK-2026-000008', 3, 2550000, '2026-05-08 17:00:00', 'payment_sample.jpg', 'paid'),
(10, 2, 'BK-2026-000009', 2,  300000, '2026-05-09 18:00:00', 'payment_sample.jpg', 'rejected'),
(11, 3, 'BK-2026-000010', 1,  450000, '2026-05-10 19:00:00', NULL,                 'pending');
