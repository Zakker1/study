<?php
require_once '../config/database.php';

try {
    $sql = file_get_contents(__DIR__ . '/create_deposits_table.sql');
    $pdo->exec($sql);
    echo "Таблица месторождений успешно создана.\n";
} catch (PDOException $e) {
    die("Ошибка при создании таблицы: " . $e->getMessage() . "\n");
}
