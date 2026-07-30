<?php
class PartnerController
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }
    }

    public function index()
    {
        require_once 'app/Models/Partner.php';
        $partners = (new Partner())->getAll();
        require_once 'app/Views/partner/list.php';
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'app/Models/Partner.php';

            // Przetwarzanie i walidacja dodatkowych numerów telefonów partnera
            $additionalPhones = [];
            if (isset($_POST['add_phone_number']) && is_array($_POST['add_phone_number'])) {
                foreach ($_POST['add_phone_number'] as $index => $number) {
                    $number = trim($number);
                    if (!empty($number)) {
                        $additionalPhones[] = [
                            'number' => $number,
                            'description' => isset($_POST['add_phone_desc'][$index]) ? trim($_POST['add_phone_desc'][$index]) : ''
                        ];
                    }
                }
            }
            
            // Konwersja na JSON do zapisu w dedykowanej kolumnie bazy
            $jsonPhones = empty($additionalPhones) ? null : json_encode($additionalPhones, JSON_UNESCAPED_UNICODE);

            $data = [
                'company_name' => $_POST['company_name'],
                'representative_first_name' => $_POST['representative_first_name'],
                'representative_last_name' => $_POST['representative_last_name'],
                'primary_phone' => $_POST['primary_phone'],
                'additional_phones' => $jsonPhones, // Nowo dodane pole mapowane z widoku
                'email' => $_POST['email'],
                'address_location' => $_POST['address_location'],
                'internal_note' => $_POST['internal_note']
            ];
            
            (new Partner())->create($data);
            header("Location: " . BASE_URL . "partner_list");
            exit;
        }
        require_once 'app/Views/partner/add.php';
    }

    // Profil i szczegółowe informacje o partnerze B2B wraz z edycją
    public function details()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        if (!isset($_GET['id'])) {
            header("Location: " . BASE_URL . "partner_list");
            exit;
        }

        $id = (int) $_GET['id'];
        require_once 'app/Models/Partner.php';
        $partnerModel = new Partner();

        // OBSŁUGA ZAPISU DANYCH (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
            $db = Database::getInstance();
            $actionType = $_POST['action_type'];

            if ($actionType === 'update_note') {
                $stmt = $db->prepare("UPDATE partners SET internal_note = ? WHERE id = ?");
                $stmt->execute([$_POST['internal_note'], $id]);
            } elseif ($actionType === 'edit_partner_core') {
                // Przetwarzanie i walidacja dodatkowych numerów telefonów partnera B2B
                $additionalPhones = [];
                if (isset($_POST['add_phone_number']) && is_array($_POST['add_phone_number'])) {
                    foreach ($_POST['add_phone_number'] as $index => $number) {
                        $number = trim($number);
                        if (!empty($number)) {
                            $additionalPhones[] = [
                                'number' => $number,
                                'description' => isset($_POST['add_phone_desc'][$index]) ? trim($_POST['add_phone_desc'][$index]) : ''
                            ];
                        }
                    }
                }
                $jsonPhones = empty($additionalPhones) ? null : json_encode($additionalPhones, JSON_UNESCAPED_UNICODE);

                // Aktualizacja rdzennych informacji rejestrowych
                $sql = "UPDATE partners 
                        SET company_name = ?, representative_first_name = ?, representative_last_name = ?, 
                            primary_phone = ?, additional_phones = ?, email = ?, address_location = ? 
                        WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $_POST['company_name'],
                    !empty($_POST['representative_first_name']) ? $_POST['representative_first_name'] : null,
                    !empty($_POST['representative_last_name']) ? $_POST['representative_last_name'] : null,
                    $_POST['primary_phone'],
                    $jsonPhones,
                    !empty($_POST['email']) ? $_POST['email'] : null,
                    !empty($_POST['address_location']) ? $_POST['address_location'] : null,
                    $id
                ]);
            }
            header("Location: " . BASE_URL . "partner/" . $id);
            exit;
        }

        $partner = $partnerModel->getById($id);

        if (!$partner) {
            $errorMessage = "Partner o identyfikatorze #{$id} nie istnieje w bazie danych.";
            require_once 'app/Views/errors/404.php';
            exit;
        }

        // Pobieramy historię napraw powiązanych z tym partnerem
        $rmaHistory = $partnerModel->getRmaHistory($id);

        require_once 'app/Views/partner/details.php';
    }
}