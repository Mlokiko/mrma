<?php
require_once 'app/Models/Warehouse.php';

class WarehouseController
{
    private $warehouseModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }
        $this->warehouseModel = new Warehouse();
    }

    public function index()
    {
        $parts = $this->warehouseModel->getAllParts();
        require_once 'app/Views/warehouse/list.php';
    }

    // NOWA METODA: Obsługa dodawania części
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category_id' => $_POST['category_id'],
                'manufacturer' => $_POST['manufacturer'],
                'part_model_code' => $_POST['part_model_code'],
                'condition_status' => $_POST['condition_status'],
                'color' => $_POST['color'],
                'item_type' => $_POST['item_type'],
                'is_original' => isset($_POST['is_original']) ? 1 : 0,
                'market_price' => $_POST['market_price'],
                'quantity' => (int) $_POST['quantity'],
                'description' => $_POST['description'],
                'storage_location' => $_POST['storage_location']
            ];

            $this->warehouseModel->createPart($data);
            header("Location: " . BASE_URL . "warehouse_list");
            exit;
        }

        // Pobieramy kategorie dla widoku formularza
        $categories = $this->warehouseModel->getCategories();
        require_once 'app/Views/warehouse/add.php';
    }
}