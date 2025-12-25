<?php
// api.php
header('Content-Type: application/json; charset=utf-8');
// Para pruebas locales (opcional):
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type');
// if ($_SERVER['REQUEST_METHOD']==='OPTIONS') { exit; }

require __DIR__ . '/db.php';

function get_body() {
  // Acepta JSON o form-data
  $raw = file_get_contents('php://input');
  $json = json_decode($raw, true);
  if (json_last_error() === JSON_ERROR_NONE && is_array($json)) return $json;
  // fallback form-urlencoded
  return $_POST ?? [];
}

// Asegura tabla correcta (id AI PK) — opcional, pero ayuda si la creaste sin AI
// Asegura tabla correcta (id AI PK)
$pdo->exec("
  CREATE TABLE IF NOT EXISTS entiejemplo (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    descripcion VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");


$action = $_GET['action'] ?? '';

try {
  switch ($action) {
    case 'create': {
      $data = get_body();
      if (!isset($data['descripcion']) || trim($data['descripcion'])==='') {
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>'Falta "descripcion"']);
        break;
      }
      $stmt = $pdo->prepare("INSERT INTO entiejemplo (descripcion) VALUES (:d)");
      $stmt->execute([':d'=>$data['descripcion']]);
      echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
      break;
    }

    case 'read': {
      if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT id, descripcion FROM entiejemplo WHERE id = :id");
        $stmt->execute([':id'=>(int)$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'No encontrado']); }
        else { echo json_encode(['ok'=>true,'data'=>$row]); }
      } else {
        $limit  = isset($_GET['limit'])  ? max(1,(int)$_GET['limit'])  : 100;
        $offset = isset($_GET['offset']) ? max(0,(int)$_GET['offset']) : 0;
        $stmt = $pdo->prepare("SELECT id, descripcion FROM entiejemplo ORDER BY id DESC LIMIT :lim OFFSET :off");
        $stmt->bindValue(':lim',$limit,PDO::PARAM_INT);
        $stmt->bindValue(':off',$offset,PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(),'limit'=>$limit,'offset'=>$offset]);
      }
      break;
    }

    case 'update': {
      $data = get_body();
      if (!isset($data['id'],$data['descripcion'])) {
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>'Faltan "id" y/o "descripcion"']);
        break;
      }
      $stmt = $pdo->prepare("UPDATE entiejemplo SET descripcion=:d WHERE id=:id");
      $stmt->execute([':d'=>$data['descripcion'], ':id'=>(int)$data['id']]);
      echo json_encode(['ok'=>true,'rows_affected'=>$stmt->rowCount()]);
      break;
    }

    case 'delete': {
      $data = get_body();
      if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>'Falta "id"']);
        break;
      }
      $stmt = $pdo->prepare("DELETE FROM entiejemplo WHERE id=:id");
      $stmt->execute([':id'=>(int)$data['id']]);
      echo json_encode(['ok'=>true,'rows_affected'=>$stmt->rowCount()]);
      break;
    }

    default: {
      echo json_encode([
        'ok'=>true,
        'endpoints'=>[
          'POST   ?action=create   body:{descripcion}',
          'GET    ?action=read     (lista)',
          'GET    ?action=read&id=1',
          'POST   ?action=update   body:{id, descripcion}',
          'POST   ?action=delete   body:{id}'
        ]
      ]);
    }
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Server error','detail'=>$e->getMessage()]);
}
