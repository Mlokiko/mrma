<?php
// Na górze pliku, pod require modeli (jeśli dodajesz tam includes), upewnij się że masz:
require_once 'app/Models/Client.php';

class RmaController
{
    private $rmaModel;
    private $clientModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }
        $this->rmaModel = new Rma();
        $this->clientModel = new Client();
    }

   public function index()
    {
        $query = trim($_GET['q'] ?? '');
        $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : null;
        $all = isset($_GET['all']) && $_GET['all'] == '1';
        
        // Zarządzanie zakresem dat (Miesiąc kalendarzowy)
        // Domyślnie bieżący miesiąc, chyba że wymuszono pobranie wszystkich lub określono limit
        $currentMonth = date('Y-m');
        $month = $_GET['month'] ?? ($all || $limit ? null : $currentMonth);
        if ($all || $limit) {
            $month = null; 
        }

        // Struktura zaawansowanych filtrów dla bazy danych
        $dbFilters = [
            'client_type' => $_GET['f_client_type'] ?? [],
            'status' => $_GET['f_status'] ?? [],
            'type' => trim($_GET['f_type'] ?? ''),
            'manufacturer' => trim($_GET['f_manufacturer'] ?? ''),
            'model' => trim($_GET['f_model'] ?? ''),
            'date_from' => trim($_GET['f_date_from'] ?? ''),
            'date_to' => trim($_GET['f_date_to'] ?? '')
        ];

        // Szybkie przekierowanie, jeśli wpisano czyste ID zlecenia bez innych filtrów
        if (!empty($query) && is_numeric($query) && empty($dbFilters['client_type']) && empty($dbFilters['status']) && empty($dbFilters['type']) && empty($dbFilters['manufacturer']) && empty($dbFilters['model'])) {
            $rmaExists = $this->rmaModel->getById((int) $query);
            if ($rmaExists) {
                header("Location: " . BASE_URL . "rma/" . (int) $query);
                exit;
            }
        }

        // Pobranie danych przy użyciu nowej metody zaawansowanej
        $rmasList = $this->rmaModel->searchListAdvanced($query, $dbFilters, $month, $limit, $all);

        require_once 'app/Views/rma/list.php';
    }

    public function details()
    {
        if (!isset($_GET['id'])) {
            header("Location: " . BASE_URL . "rma");
            exit;
        }

        $id = (int) $_GET['id'];

        // --- OBSŁUGA FORMULARZY EDYCJI (POST) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actionType = $_POST['action_type'] ?? '';

            if ($actionType === 'update_status') {
                $this->rmaModel->updateStatus($id, $_POST['new_status'], $_SESSION['user_id']); // Usunięto status_note

            } elseif ($actionType === 'update_device') {
                $liquidStatus = (isset($_POST['is_liquid_damage']) && $_POST['is_liquid_damage'] === '1') ? $_POST['liquid_damage_status'] : 'None';
                $this->rmaModel->updateDeviceData($id, $_POST['serial_number'] ?: null, $_POST['device_lock_code'] ?: null, $liquidStatus);

            } elseif ($actionType === 'update_costs') {
                $this->rmaModel->updateCosts(
                    $id,
                    $_POST['estimated_cost'] ?: null,
                    $_POST['max_approved_cost'] ?: null,
                    $_POST['parts_cost'] ?: null,
                    $_POST['internal_cost'] ?: null,
                    $_POST['final_cost'] ?: null,
                    $_POST['payment_method'] ?: null,
                    $_POST['advance_payment'] ?: null
                );
            } elseif ($actionType === 'delete_warranty') {
                $this->rmaModel->deleteWarranty($id);
            }
            // Przeładowanie, aby zapobiec ponownemu wysłaniu danych F5
            header("Location: " . BASE_URL . "rma/" . $id);
            exit;
        }

        // --- POBIERANIE WIDOKU (GET) ---
        $rma = $this->rmaModel->getById($id);

        if (!$rma) {
            $errorMessage = "Zgłoszenie RMA #{$id} nie istnieje lub zostało usunięte z bazy danych.";
            require_once 'app/Views/errors/404.php';
            exit;
        }

        $adjacent = $this->rmaModel->getAdjacentIds($id);
        $statusHistory = $this->rmaModel->getStatusHistory($id);

        require_once 'app/Views/rma/details.php';
    }

    public function add()
    {
        require_once 'app/Models/Client.php';
        require_once 'app/Models/Partner.php';
        $this->clientModel = new Client();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $clientType = $_POST['base_client_type']; // Individual, Company, Partner
            $clientId = null;
            $partnerId = null;
            $prefContact = !empty($_POST['preferred_contact']) ? $_POST['preferred_contact'] : null;

            if ($clientType === 'Partner') {
                $partnerId = $_POST['partner_id'];
            }

            // Tworzymy klienta jeśli nie jest to partner LUB jeśli to partner, ale zaznaczono checkbox kontaktu bezpośredniego
            if ($clientType !== 'Partner' || ($clientType === 'Partner' && isset($_POST['add_end_client']) && $_POST['add_end_client'] === '1')) {

                $additionalPhones = [];
                if (isset($_POST['add_phone_number']) && is_array($_POST['add_phone_number'])) {
                    foreach ($_POST['add_phone_number'] as $index => $number) {
                        if (!empty($number)) {
                            $additionalPhones[] = ['number' => $number, 'description' => $_POST['add_phone_desc'][$index] ?? ''];
                        }
                    }
                }

                if ($_POST['client_status'] === 'old') {
                    $clientId = $_POST['existing_client_id'];
                    $this->clientModel->updateContact($clientId, $_POST['client_phone'], $prefContact, $additionalPhones);
                } else {
                    // Jeśli to klient od Partnera, domyślnie rejestrujemy go jako osobę fizyczną (Individual)
                    $actualClientType = ($clientType === 'Company') ? 'Company' : 'Individual';
                    $nip = !empty($_POST['client_nip']) ? $_POST['client_nip'] : null;

                    $clientData = [
                        'client_type' => $actualClientType,
                        'first_name' => $_POST['client_first_name'],
                        'last_name' => $_POST['client_last_name'] ?: null,
                        'nip' => $nip, // Dodano nip
                        'primary_phone' => $_POST['client_phone'],
                        'additional_phones' => empty($additionalPhones) ? null : json_encode($additionalPhones, JSON_UNESCAPED_UNICODE),
                        'email' => $_POST['client_email'] ?: null,
                        'preferred_contact' => $prefContact
                    ];
                    $clientId = $this->clientModel->create($clientData);
                }
            }

            $liquidDamageStatus = (isset($_POST['is_liquid_damage']) && $_POST['is_liquid_damage'] === '1') ? $_POST['liquid_damage_status'] : 'None';

            $rmaData = [
                'localization_id' => $_POST['localization_id'],
                'client_id' => $clientId, // Może być wypełnione nawet gdy mamy partner_id
                'partner_id' => $partnerId,
                // Usunięto partner_client_phone
                'device_model_id' => $_POST['device_model_id'],
                'serial_number' => $_POST['serial_number'] ?: null,
                'device_lock_code' => $_POST['device_lock_code'] ?: null,
                'issue_description' => $_POST['issue_description'],
                'liquid_damage_status' => $liquidDamageStatus,
                'received_by_user_id' => $_POST['received_by_user_id'] ?? $_SESSION['user_id'],
                'is_express' => isset($_POST['is_express']) ? 1 : 0,
                'estimated_cost' => $_POST['estimated_cost'] ?: null,
                'max_approved_cost' => $_POST['max_approved_cost'] ?: null,
                'advance_payment' => $_POST['advance_payment'] ?: null
            ];

            $this->rmaModel->create($rmaData);
            header("Location: " . BASE_URL . "rma_list");
            exit;
        }

        require_once 'app/Models/Device.php';
        $localizations = $this->rmaModel->getLocalizations();
        $deviceTypes = (new Device())->getActiveTypes();
        $partners = (new Partner())->getAll();
        $users = (new User())->getAll(); // DODANE (Pobiera listę pracowników)

        require_once 'app/Views/rma/add.php';
    }

    public function notes()
    {
        if (!isset($_GET['id'])) {
            header("Location: " . BASE_URL . "rma_list");
            exit;
        }

        $rmaId = (int) $_GET['id'];

        // Dołączamy i inicjalizujemy model notatek
        require_once 'app/Models/Note.php';
        $noteModel = new Note();

        // OBSŁUGA POST: Dodawanie lub edycja notatki
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $text = trim($_POST['note_text'] ?? '');
            $isInternal = isset($_POST['is_internal']) ? 1 : 0;
            $noteId = $_POST['note_id'] ?? null; // Pobieramy ID notatki (jeśli to edycja)

            if (!empty($text)) {
                if (!empty($noteId)) {
                    // Edycja istniejącej notatki
                    $noteModel->updateNote($noteId, $_SESSION['user_id'], $text, $isInternal);
                } else {
                    // Tworzenie nowej notatki
                    $noteModel->create($rmaId, $_SESSION['user_id'], $text, $isInternal);
                }
            }

            // Przeładowanie strony z użyciem przyjaznych URLi (lub starych)
            if (defined('BASE_URL')) {
                header("Location: " . BASE_URL . "rma_notes/" . $rmaId);
            } else {
                header("Location: " . BASE_URL . "rma_notes&id=" . $rmaId);
            }
            exit;
        }
        // OBSŁUGA GET: Pobranie danych do wyświetlenia
        $rma = $this->rmaModel->getById($rmaId);
        if (!$rma) {
            $errorMessage = "Zgłoszenie RMA #{$rmaId} nie istnieje lub zostało usunięte z bazy danych.";
            require_once 'app/Views/errors/404.php';
            exit;
        }

        $notes = $noteModel->getByRmaId($rmaId);

        require_once 'app/Views/rma/notes.php';
    }

    public function generatePdf()
    {
        if (!isset($_GET['id'])) {
            die("Brak ID zgłoszenia.");
        }

        $id = (int) $_GET['id'];
        $rma = $this->rmaModel->getById($id);

        if (!$rma) {
            die("Zgłoszenie nie istnieje.");
        }

        // Nie używamy require_once do headera i footera, bo chcemy czysty wydruk!
        require_once 'app/Views/rma/print.php';
    }

   public function generateWarrantyPdf($id)
    {
        if (!$id) {
            die("Brak poprawnego identyfikatora zgłoszenia RMA.");
        }

        $rma = $this->rmaModel->getById($id);
        if (!$rma) {
            die("Zgłoszenie o podanym ID nie istnieje w systemie.");
        }

        // Zabezpieczenie: Zapisujemy do bazy TYLKO przy wysłaniu formularza (POST), 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postWarrantyMonths = isset($_POST['warranty_months']) ? (int) $_POST['warranty_months'] : 12;
            $postUseGeneric = isset($_POST['use_detailed_warranty']) ? false : true;
            $postWarrantyScope = isset($_POST['warranty_scope']) ? trim($_POST['warranty_scope']) : '';
            $postWarrantyCovered = isset($_POST['warranty_covered']) ? trim($_POST['warranty_covered']) : '';

            // Jeżeli gwarancja ma już datę wydania w bazie, to ZACHOWAJ JĄ. W przeciwnym razie ustaw obecną datę.
            $issuedAt = !empty($rma['warranty_issued_at']) ? $rma['warranty_issued_at'] : date('Y-m-d H:i:s');

            // ZAPIS DO BAZY DANYCH
            $this->rmaModel->updateWarranty(
                $id, 
                $postWarrantyMonths, 
                $issuedAt, 
                $postUseGeneric ? null : $postWarrantyScope, 
                $postUseGeneric ? null : $postWarrantyCovered
            );

            // Odświeżamy dane z bazy, by widok PDF użył już zapisanych, najnowszych informacji
            $rma = $this->rmaModel->getById($id);
        }

        // --- PRZYGOTOWANIE WSZYSTKICH ZMIENNYCH DLA WIDOKU (PDF) ---
        // 1. Zmienne konfiguracyjne
        $warrantyMonths = !empty($rma['warranty_months']) ? (int) $rma['warranty_months'] : 12;
        $warrantyScope = $rma['warranty_scope'] ?? '';
        $warrantyCovered = $rma['warranty_covered'] ?? '';
        
        // Jeśli oba pola są puste, uznajemy, że to gwarancja ogólna (bez szczegółów)
        $useGeneric = empty($warrantyScope) && empty($warrantyCovered);

        // 2. Obiekty daty dla PHP
        $issuedDate = !empty($rma['warranty_issued_at']) ? new DateTime($rma['warranty_issued_at']) : new DateTime();
        $expiryDate = clone $issuedDate;
        $expiryDate->modify("+" . $warrantyMonths . " months");

        // Ładujemy widok PDF
        require_once 'app/Views/rma/warranty_print.php';
    }
}