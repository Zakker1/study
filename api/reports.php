<?php
require_once '../config/database.php';
require_once '../vendor/autoload.php'; // Требуется установить TCPDF через composer

use TCPDF;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Создаем новый PDF документ
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Устанавливаем информацию о документе
    $pdf->SetCreator('CRM System');
    $pdf->SetAuthor('CRM System');
    $pdf->SetTitle('Отчет по месторождениям');
    
    // Устанавливаем данные заголовка
    $pdf->SetHeaderData('', 0, 'Отчет по месторождениям', 'Сгенерировано: ' . date('d.m.Y H:i:s'));
    
    // Устанавливаем шрифт для кириллицы
    $pdf->SetFont('dejavusans', '', 12);
    
    // Добавляем страницу
    $pdf->AddPage();
    
    // Формируем SQL запрос в зависимости от фильтров
    $where = [];
    $params = [];
    
    if (!empty($data['region_id'])) {
        $where[] = "d.region_id = :region_id";
        $params['region_id'] = $data['region_id'];
    }
    
    if (!empty($data['date_start'])) {
        $where[] = "d.created_at >= :date_start";
        $params['date_start'] = $data['date_start'];
    }
    
    if (!empty($data['date_end'])) {
        $where[] = "d.created_at <= :date_end";
        $params['date_end'] = $data['date_end'];
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $query = "SELECT 
                d.name,
                r.name as region,
                w.name as waste_type,
                s.name as status,
                d.area,
                d.coordinates,
                d.created_at
            FROM deposits d
            LEFT JOIN regions r ON d.region_id = r.id
            LEFT JOIN waste_types w ON d.waste_type_id = w.id
            LEFT JOIN deposit_statuses s ON d.status_id = s.id
            $whereClause
            ORDER BY d.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $deposits = $stmt->fetchAll();
    
    // Добавляем данные в PDF
    $html = '<h1>Отчет по месторождениям</h1>';
    $html .= '<table border="1" cellpadding="4">
                <tr>
                    <th>Название</th>
                    <th>Регион</th>
                    <th>Тип отходов</th>
                    <th>Статус</th>
                    <th>Площадь</th>
                    <th>Координаты</th>
                    <th>Дата создания</th>
                </tr>';
    
    foreach ($deposits as $deposit) {
        $html .= '<tr>
                    <td>'.$deposit['name'].'</td>
                    <td>'.$deposit['region'].'</td>
                    <td>'.$deposit['waste_type'].'</td>
                    <td>'.$deposit['status'].'</td>
                    <td>'.$deposit['area'].'</td>
                    <td>'.$deposit['coordinates'].'</td>
                    <td>'.date('d.m.Y', strtotime($deposit['created_at'])).'</td>
                </tr>';
    }
    
    $html .= '</table>';
    
    // Добавляем HTML в PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Закрываем и выводим PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="report.pdf"');
    echo $pdf->Output('report.pdf', 'S');
}
