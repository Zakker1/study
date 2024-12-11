<?php
require_once '../config/database.php';

// Разрешаем CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Обработка preflight запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Получаем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $query = "SELECT 
                        d.id,
                        d.name,
                        r.name as region,
                        w.name as waste_type,
                        s.name as status,
                        d.area,
                        d.coordinates,
                        DATE_FORMAT(d.created_at, '%d.%m.%Y') as created_at
                    FROM deposits d
                    LEFT JOIN regions r ON d.region_id = r.id
                    LEFT JOIN waste_types w ON d.waste_type_id = w.id
                    LEFT JOIN deposit_statuses s ON d.status_id = s.id
                    ORDER BY d.id DESC";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'POST':
            if (isset($_POST['draw'])) { // DataTables запрос
                $draw = $_POST['draw'];
                $start = $_POST['start'];
                $length = $_POST['length'];
                $search = $_POST['search']['value'];
                
                // Базовый запрос
                $query = "FROM deposits d
                        LEFT JOIN regions r ON d.region_id = r.id
                        LEFT JOIN waste_types w ON d.waste_type_id = w.id
                        LEFT JOIN deposit_statuses s ON d.status_id = s.id";
                
                // Условия поиска
                $where = [];
                $params = [];
                
                if (!empty($search)) {
                    $where[] = "(d.name LIKE :search OR r.name LIKE :search OR w.name LIKE :search)";
                    $params[':search'] = "%$search%";
                }
                
                if (!empty($_POST['region'])) {
                    $where[] = "d.region_id = :region_id";
                    $params[':region_id'] = $_POST['region'];
                }
                
                if (!empty($_POST['status'])) {
                    $where[] = "d.status_id = :status_id";
                    $params[':status_id'] = $_POST['status'];
                }
                
                if (!empty($where)) {
                    $query .= " WHERE " . implode(" AND ", $where);
                }
                
                // Подсчет общего количества записей
                $countQuery = "SELECT COUNT(*) " . $query;
                $stmt = $pdo->prepare($countQuery);
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                $stmt->execute();
                $recordsTotal = $recordsFiltered = $stmt->fetchColumn();
                
                // Получение данных с пагинацией
                $query = "SELECT 
                            d.id,
                            d.name,
                            r.name as region,
                            w.name as waste_type,
                            s.name as status,
                            d.area,
                            d.coordinates,
                            DATE_FORMAT(d.created_at, '%d.%m.%Y') as created_at
                        " . $query . "
                        ORDER BY d.id DESC
                        LIMIT :start, :length";
                
                $stmt = $pdo->prepare($query);
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
                $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
                $stmt->execute();
                
                echo json_encode([
                    'draw' => (int)$draw,
                    'recordsTotal' => (int)$recordsTotal,
                    'recordsFiltered' => (int)$recordsFiltered,
                    'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
                ]);
            } else { // Обычный POST запрос для создания записи
                $data = json_decode(file_get_contents('php://input'), true);
                
                $query = "INSERT INTO deposits (name, region_id, waste_type_id, status_id, area, coordinates, description) 
                         VALUES (:name, :region_id, :waste_type_id, :status_id, :area, :coordinates, :description)";
                
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    'name' => $data['name'],
                    'region_id' => $data['region_id'],
                    'waste_type_id' => $data['waste_type_id'],
                    'status_id' => $data['status_id'],
                    'area' => $data['area'],
                    'coordinates' => $data['coordinates'],
                    'description' => $data['description']
                ]);
                
                echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['id'])) {
                throw new Exception('ID not provided');
            }
            
            $query = "UPDATE deposits 
                     SET name = :name,
                         region_id = :region_id,
                         waste_type_id = :waste_type_id,
                         status_id = :status_id,
                         area = :area,
                         coordinates = :coordinates,
                         description = :description
                     WHERE id = :id";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'id' => $data['id'],
                'name' => $data['name'],
                'region_id' => $data['region_id'],
                'waste_type_id' => $data['waste_type_id'],
                'status_id' => $data['status_id'],
                'area' => $data['area'],
                'coordinates' => $data['coordinates'],
                'description' => $data['description']
            ]);
            
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception('ID not provided');
            }
            
            $stmt = $pdo->prepare("DELETE FROM deposits WHERE id = :id");
            $stmt->execute(['id' => $_GET['id']]);
            
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
