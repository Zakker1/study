-- Создание таблицы регионов
CREATE TABLE IF NOT EXISTS regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Название региона',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы типов отходов
CREATE TABLE IF NOT EXISTS waste_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Тип отходов',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы статусов месторождений
CREATE TABLE IF NOT EXISTS deposit_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Статус месторождения',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание основной таблицы месторождений
CREATE TABLE IF NOT EXISTS deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Название месторождения',
    region_id INT COMMENT 'ID региона',
    waste_type_id INT COMMENT 'ID типа отходов',
    status_id INT COMMENT 'ID статуса',
    area DECIMAL(10,2) COMMENT 'Площадь',
    coordinates VARCHAR(255) COMMENT 'Координаты',
    description TEXT COMMENT 'Описание',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id),
    FOREIGN KEY (waste_type_id) REFERENCES waste_types(id),
    FOREIGN KEY (status_id) REFERENCES deposit_statuses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставка базовых данных
INSERT INTO regions (name) VALUES 
('Северный регион'),
('Южный регион'),
('Восточный регион'),
('Западный регион');

INSERT INTO waste_types (name) VALUES 
('Твердые бытовые отходы'),
('Промышленные отходы'),
('Строительные отходы'),
('Сельскохозяйственные отходы');

INSERT INTO deposit_statuses (name) VALUES 
('Активный'),
('Закрытый'),
('На рассмотрении'),
('В разработке');
