-- Создание базы данных
CREATE DATABASE IF NOT EXISTS deposits_crm;
USE deposits_crm;

-- Таблица регионов
CREATE TABLE regions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица типов отходов
CREATE TABLE waste_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица статусов месторождений
CREATE TABLE deposit_statuses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Основная таблица месторождений
CREATE TABLE deposits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    region_id INT,
    waste_type_id INT,
    status_id INT,
    coordinates VARCHAR(100),
    area DECIMAL(10,2),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id),
    FOREIGN KEY (waste_type_id) REFERENCES waste_types(id),
    FOREIGN KEY (status_id) REFERENCES deposit_statuses(id)
);

-- Таблица для хранения документов месторождений
CREATE TABLE deposit_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    deposit_id INT,
    document_type VARCHAR(50),
    file_path VARCHAR(255),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deposit_id) REFERENCES deposits(id)
);

-- Таблица пользователей
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP
);

-- Таблица для логирования изменений
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action_type VARCHAR(50),
    table_name VARCHAR(50),
    record_id INT,
    changes TEXT,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Вставка начальных данных для статусов
INSERT INTO deposit_statuses (name, description) VALUES
('Активный', 'Месторождение в активной разработке'),
('Законсервированный', 'Временно приостановленная разработка'),
('Исчерпанный', 'Месторождение полностью исчерпано'),
('Планируемый', 'Планируется начало разработки');

-- Вставка тестовых типов отходов
INSERT INTO waste_types (name, description) VALUES
('Твердые бытовые отходы', 'Отходы потребления, образующиеся в населенных пунктах'),
('Промышленные отходы', 'Отходы производства и потребления, образующиеся в промышленности'),
('Строительные отходы', 'Отходы, образующиеся при строительстве и ремонте'),
('Сельскохозяйственные отходы', 'Отходы, образующиеся в процессе сельскохозяйственного производства');

-- Вставка регионов Казахстана
INSERT INTO regions (name) VALUES
('Абайская область'),
('Акмолинская область'),
('Актюбинская область'),
('Алматинская область'),
('Атырауская область'),
('Восточно-Казахстанская область'),
('Жамбылская область'),
('Жетысуская область'),
('Западно-Казахстанская область'),
('Карагандинская область'),
('Костанайская область'),
('Кызылординская область'),
('Мангистауская область'),
('Павлодарская область'),
('Северо-Казахстанская область'),
('Туркестанская область'),
('Улытауская область'),
('г. Алматы'),
('г. Астана'),
('г. Шымкент');

-- Создание индексов для оптимизации запросов
CREATE INDEX idx_deposits_region ON deposits(region_id);
CREATE INDEX idx_deposits_status ON deposits(status_id);
CREATE INDEX idx_deposits_waste_type ON deposits(waste_type_id);
CREATE INDEX idx_activity_log_user ON activity_log(user_id);
CREATE INDEX idx_activity_log_timestamp ON activity_log(action_timestamp);
