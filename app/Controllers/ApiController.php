<?php
require_once 'app/Models/Device.php';
require_once 'app/Models/Client.php';

class ApiController
{
    public function getManufacturers()
    {
        $typeId = $_GET['type_id'] ?? 0;
        $model = new Device();
        echo json_encode($model->getManufacturersByType($typeId));
    }

    public function getModels()
    {
        $typeId = $_GET['type_id'] ?? 0;
        $manufacturerId = $_GET['manufacturer_id'] ?? 0;
        $model = new Device();
        echo json_encode($model->getModels($typeId, $manufacturerId));
    }

    public function searchClients()
    {
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 2) {
            echo json_encode([]);
            return;
        }
        $model = new Client();
        echo json_encode($model->search($query));
    }

    // --- QUICK ADD (Szybkie dodawanie z formularza) ---
    public function quickAddType()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        require_once 'app/Models/Device.php';
        $db = Database::getInstance();
        (new Device())->createType($data['name']);
        echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'name' => $data['name']]);
    }

    public function quickAddManufacturer()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        require_once 'app/Models/Device.php';
        $db = Database::getInstance();
        // Tworzymy producenta i od razu wiążemy z aktualnie wybranym typem!
        (new Device())->createManufacturer($data['name'], [$data['type_id']]);
        echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'name' => $data['name']]);
    }

    public function quickAddModel()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        require_once 'app/Models/Device.php';
        $db = Database::getInstance();
        (new Device())->createModel($data['manufacturer_id'], $data['type_id'], $data['name']);
        echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'name' => $data['name']]);
    }

    public function quickAddCode()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        require_once 'app/Models/Device.php';
        $db = Database::getInstance();
        (new Device())->createModelCode($data['model_id'], $data['name']);
        echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'name' => $data['name']]);
    }
}