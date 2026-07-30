<?php
session_start();

// ładowanie pliku .env do zmiennych środowiskowych
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Pomiń komentarze
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Dołączanie połączenia z bazą danych
require_once 'app/Database.php';

// ==========================================
// AUTOLOADER (Rozwiązanie problemu "Class not found")
// ==========================================
spl_autoload_register(function ($class_name) {
    $modelPath = 'app/Models/' . $class_name . '.php';
    $controllerPath = 'app/Controllers/' . $class_name . '.php';

    if (file_exists($modelPath)) {
        require_once $modelPath;
    } elseif (file_exists($controllerPath)) {
        require_once $controllerPath;
    }
});

// ==========================================
// AUTOMATYCZNY MIKRO-ROUTER (Przyjazne URLe)
// ==========================================
// Automatycznie wykrywamy folder projektu w XAMPP (całkowity brak podatności na błędy ludzkie)
$baseDir = dirname($_SERVER['SCRIPT_NAME']);
$baseDir = str_replace('\\', '/', $baseDir);
$baseUrl = rtrim($baseDir, '/') . '/';
define('BASE_URL', $baseUrl);

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Bezpieczne wyciąganie ścieżki (odporne na wielkość liter w adresie URL)
$baseUrlLen = strlen(BASE_URL);
if (strncasecmp($requestPath, BASE_URL, $baseUrlLen) === 0) {
    $path = substr($requestPath, $baseUrlLen);
} else {
    $path = $requestPath;
}

$path = trim($path, '/');
$segments = explode('/', $path);

// Pełne wsparcie dla starych linków typu ?action=... (wsteczna kompatybilność)
$action = $_GET['action'] ?? null;

if (!$action && $path !== '') {
    $route = $segments[0];           // np. 'rma' lub 'client'
    $param = $segments[1] ?? null;   // np. identyfikator ID (np. '5')

    switch ($route) {
        // Obsługa ścieżki: /rma/5 -> rma_details | /rma -> rma_list
        case 'rma':
            if (is_numeric($param)) {
                $action = 'rma_details';
                $_GET['id'] = $param; // Przekazujemy ID symulując tradycyjne $_GET
            } else {
                $action = 'rma_list';
            }
            break;
            
        // Obsługa ścieżki: /client/5 -> client_details | /client -> client_list
        case 'client':
            if (is_numeric($param)) {
                $action = 'client_details';
                $_GET['id'] = $param;
            } else {
                $action = 'client_list';
            }
            break;
        // ==========================================
        // NOWO DODANY BLOK DLA PARTNERÓW B2B
        // ==========================================
        case 'partner':
            if (is_numeric($param)) {
                $action = 'partner_details';
                $_GET['id'] = $param; // Przekazujemy ID symulując tradycyjne $_GET
            } else {
                $action = 'partner_list';
            }
            break;

        // Pozostałe ścieżki jednoczłonowe (np. /rma_add, /login, /rma_notes/5)
        default:
            $action = $route;
            if ($param) {
                $_GET['id'] = $param;
            }
            break;
    }
} elseif (!$action) {
    $action = 'login'; // Jeśli adres to czysty localhost/twój_projekt/
}

// Routing - Wywołujemy kontrolery (Autoloader sam pobierze ich pliki!)
switch ($action) {
    
    // ==========================================
    // AUTORYZACJA I KONTO
    // ==========================================
    case 'login': (new AuthController())->login(); break;
    case 'register': (new AuthController())->register(); break;
    case 'logout': (new AuthController())->logout(); break;
    case 'account': (new AuthController())->account(); break;
        
    // ==========================================
    // ZGŁOSZENIA RMA
    // ==========================================
    case 'rma_list': (new RmaController())->index(); break;
    case 'rma_add': (new RmaController())->add(); break;
    case 'rma_details': (new RmaController())->details(); break;
    case 'rma_notes': (new RmaController())->notes(); break;
    case 'rma_pdf': require_once 'app/Controllers/RmaController.php'; (new RmaController())->generatePdf(); break;
    case 'warranty_pdf':
        require_once 'app/Controllers/RmaController.php';
        // Automatyczne wykrywanie ID ze ścieżki przyjaznego URL lub klasycznego $_GET
        $rmaId = null;
        if (isset($_GET['id'])) {
            $rmaId = (int)$_GET['id'];
        } else {
            // Pobieranie ID z przyjaznego adresu (np. /warranty_pdf/24)
            $urlParts = explode('/', rtrim($requestPath, '/'));
            $rmaId = (int)end($urlParts);
        }       
        (new RmaController())->generateWarrantyPdf($rmaId);
        break;

    // ==========================================
    // MAGAZYN
    // ==========================================
    case 'warehouse_list': (new WarehouseController())->index(); break;
    case 'warehouse_add': (new WarehouseController())->add(); break;

    // ==========================================
    // KATALOG URZĄDZEŃ (Baza sprzętu)
    // ==========================================
    case 'device_catalog': (new DeviceController())->catalog(); break;

    // ==========================================
    // API (Komunikacja w tle dla JavaScript)
    // ==========================================
    case 'api_get_manufacturers': (new ApiController())->getManufacturers(); break;
    case 'api_get_models': (new ApiController())->getModels(); break;
    case 'api_search_clients': (new ApiController())->searchClients(); break;
    case 'api_quick_add_type': (new ApiController())->quickAddType(); break;
    case 'api_quick_add_man': (new ApiController())->quickAddManufacturer(); break;
    case 'api_quick_add_model': (new ApiController())->quickAddModel(); break;
    case 'api_quick_add_code': (new ApiController())->quickAddCode(); break;

    // ==========================================
    // KLIENCI I PARTNERZY
    // ==========================================
    case 'client_list': require_once 'app/Controllers/ClientController.php'; (new ClientController())->index(); break;
    case 'client_details': (new ClientController())->details(); break;
    case 'partner_list': require_once 'app/Controllers/PartnerController.php'; (new PartnerController())->index(); break;
    case 'partner_details': require_once 'app/Controllers/PartnerController.php'; (new PartnerController())->details(); break;
    case 'partner_add': require_once 'app/Controllers/PartnerController.php'; (new PartnerController())->add(); break;

    // ==========================================
    // ADMINISTRACJA
    // ==========================================
    case 'admin_panel': require_once 'app/Controllers/AdminController.php'; (new AdminController())->index(); break;
    case 'admin_users_list': require_once 'app/Controllers/AdminController.php'; (new AdminController())->usersList(); break;
    case 'admin_user_details': require_once 'app/Controllers/AdminController.php'; (new AdminController())->userDetails(); break;
    case 'admin_user_delete': require_once 'app/Controllers/AdminController.php'; (new AdminController())->deleteUser(); break;
    case 'admin_localizations': require_once 'app/Controllers/AdminController.php'; (new AdminController())->localizations(); break;
        
    // ==========================================
    // OBSŁUGA BŁĘDU 404
    // ==========================================
    default:
        $errorMessage = '404 Not Found.';
        require_once 'app/Views/errors/404.php';
        break;
}