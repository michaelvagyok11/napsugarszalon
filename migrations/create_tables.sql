-- create_tables.sql

-- Használj utf8mb4 karakterkódolást
CREATE DATABASE IF NOT EXISTS nail_salon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nail_salon;

-- Admin tábla
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Szolgáltatások tábla
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    duration_minutes INT NOT NULL,      -- időtartam percekben
    price DECIMAL(10,2) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Munkarend (munkaidők) tábla – heti beállítás
CREATE TABLE working_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weekday TINYINT NOT NULL,           -- 0 = vasárnap, 1 = hétfő, … 6 = szombat
    start_time TIME NOT NULL,           -- pl. '09:00:00'
    end_time TIME NOT NULL,             -- pl. '17:00:00'
    active TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY (weekday)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Foglalások tábla
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    client_email VARCHAR(255) NOT NULL,
    client_phone VARCHAR(50) NOT NULL,
    booking_date DATE NOT NULL,
    booking_start TIME NOT NULL,
    booking_end TIME NOT NULL,
    status ENUM('booked','cancelled') NOT NULL DEFAULT 'booked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
