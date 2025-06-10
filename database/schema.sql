-- Database schema for Parking Management System

-- Drop database if it exists and create a new one
DROP DATABASE IF EXISTS parking_management;
CREATE DATABASE parking_management;
USE parking_management;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'agent') NOT NULL DEFAULT 'agent',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User activity logs
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Parking space types
CREATE TABLE space_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    hourly_rate DECIMAL(10, 2) NOT NULL
);

-- Parking spaces
CREATE TABLE parking_spaces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    space_number VARCHAR(10) NOT NULL,
    type_id INT NOT NULL,
    status ENUM('available', 'occupied', 'reserved', 'maintenance') NOT NULL DEFAULT 'available',
    floor INT NOT NULL DEFAULT 1,
    zone VARCHAR(20),
    FOREIGN KEY (type_id) REFERENCES space_types(id),
    UNIQUE KEY unique_space_number (space_number)
);

-- Vehicle types
CREATE TABLE vehicle_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    requires_special_space BOOLEAN DEFAULT FALSE
);

-- Vehicles
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_plate VARCHAR(20) NOT NULL,
    type_id INT NOT NULL,
    owner_name VARCHAR(100),
    owner_phone VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_id) REFERENCES vehicle_types(id),
    UNIQUE KEY unique_license_plate (license_plate)
);

-- Parking tickets
CREATE TABLE parking_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    space_id INT NOT NULL,
    entry_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    exit_time DATETIME NULL,
    amount_paid DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('active', 'completed', 'cancelled') NOT NULL DEFAULT 'active',
    created_by INT NOT NULL,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (space_id) REFERENCES parking_spaces(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Reservations
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    space_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'active', 'checked_in', 'completed', 'cancelled', 'no_show') NOT NULL DEFAULT 'pending',
    customer_email VARCHAR(100),
    customer_phone VARCHAR(20),
    vehicle_type_id INT,
    license_plate VARCHAR(20),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (space_id) REFERENCES parking_spaces(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types(id)
);

-- Payments
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'credit_card', 'debit_card', 'mobile_payment') NOT NULL,
    transaction_reference VARCHAR(100),
    payment_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    processed_by INT NOT NULL,
    FOREIGN KEY (ticket_id) REFERENCES parking_tickets(id),
    FOREIGN KEY (processed_by) REFERENCES users(id)
);

-- Settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default space types
INSERT INTO space_types (name, description, hourly_rate) VALUES
('Standard', 'Standard parking space for regular vehicles', 2.50),
('Handicap', 'Accessible parking for handicapped individuals', 1.50),
('VIP', 'Premium parking spaces with additional benefits', 5.00);

-- Insert default vehicle types
INSERT INTO vehicle_types (name, description, requires_special_space) VALUES
('Car', 'Standard passenger vehicle', FALSE),
('Motorcycle', 'Two-wheeled motor vehicle', FALSE),
('Truck', 'Large goods vehicle', TRUE),
('Trailer', 'Vehicle designed to be pulled by another vehicle', TRUE);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('reservation_limit_days', '30', 'Maximum number of days in advance for reservations'),
('reservation_penalty', '10.00', 'Penalty amount for no-show reservations'),
('opening_time', '08:00:00', 'Facility opening time'),
('closing_time', '20:00:00', 'Facility closing time'),
('tax_rate', '0.08', 'Tax rate applied to parking fees');

-- Insert a default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@parking.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'admin');
