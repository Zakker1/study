-- Скрипт для создания базы данных и реализации вкладок

-- Создаем таблицу для месторождений
CREATE TABLE Mestorozhdeniya (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    resource_type VARCHAR(255) NOT NULL,
    estimated_volume DOUBLE PRECISION,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Вкладка 'Просмотр месторождений'
-- Пример запроса для просмотра всех записей в таблице месторождений
SELECT * FROM Mestorozhdeniya;

-- Вкладка 'Добавить месторождение'
-- Пример процедуры добавления данных в таблицу
INSERT INTO Mestorozhdeniya (name, location, resource_type, estimated_volume)
VALUES ('Месторождение А', 'Казахстан', 'Уголь', 1000000.0);

-- Вкладка 'Сформировать отчет'
-- Для формирования отчета можно использовать сторонние библиотеки
-- Например, через Python:
-- Используйте библиотеку ReportLab или pandas для экспорта таблицы в PDF

-- Псевдокод для Python:
-- import pandas as pd
-- from sqlalchemy import create_engine
-- engine = create_engine('postgresql://user:password@localhost/dbname')
-- df = pd.read_sql("SELECT * FROM Mestorozhdeniya", engine)
-- df.to_pdf("otchet.pdf")

-- Вкладка 'Настройки'
-- Создаем таблицу для хранения параметров настроек
CREATE TABLE Settings (
    id SERIAL PRIMARY KEY,
    setting_name VARCHAR(255) NOT NULL,
    setting_value VARCHAR(255) NOT NULL
);

-- Пример добавления настройки
INSERT INTO Settings (setting_name, setting_value)
VALUES ('default_report_format', 'PDF');
