<?php
require_once 'app/Models/Device.php';

class DeviceController
{
    private $deviceModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }
        $this->deviceModel = new Device();
    }

    public function catalog()
    {
        // Obsługa formularzy (różne akcje na jednej stronie)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['form_action'] ?? '';

            switch ($action) {
                case 'add_type':
                    $this->deviceModel->createType($_POST['type_name']);
                    break;
                case 'add_manufacturer':
                    // Tablica zaznaczonych checkboxów (typów urządzeń)
                    $typeIds = $_POST['device_type_ids'] ?? [];
                    $this->deviceModel->createManufacturer($_POST['manufacturer_name'], $typeIds);
                    break;
                case 'add_model':
                    $this->deviceModel->createModel($_POST['manufacturer_id'], $_POST['type_id'], $_POST['model_name']);
                    break;
                case 'add_model_code':
                    $this->deviceModel->createModelCode($_POST['model_id'], $_POST['code_name']);
                    break;
            }

            // Odświeżenie strony po dodaniu (zapobiega ponownemu wysłaniu formularza F5)
            header("Location: " . BASE_URL . "device_catalog");
            exit;
        }

        // Pobieramy dane do wyświetlenia w listach rozwijanych
        $types = $this->deviceModel->getActiveTypes();
        $manufacturers = $this->deviceModel->getAllManufacturers();
        $models = $this->deviceModel->getAllModels();

        require_once 'app/Views/device/catalog.php';
    }
}