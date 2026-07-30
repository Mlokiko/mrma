<?php
class AdminController
{
    public function __construct()
    {
        // Globalne zabezpieczenie: Tylko Admin ma wstęp do panelu administracyjnego
        if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'Admin') {
            header("Location: " . BASE_URL . "rma_list");
            exit;
        }
    }

    public function index()
    {
        // Główny panel - teraz czysty, przygotowany pod globalne ustawienia
        require_once 'app/Views/admin/index.php';
    }

    public function usersList()
    {
        // Osobna strona: Pobieramy użytkowników tylko wtedy, gdy wchodzimy na dedykowaną listę
        $userModel = new User();
        $users = $userModel->getAll(); 
        
        require_once 'app/Views/admin/users_list.php';
    }

    public function userDetails()
    {
        $userId = $_GET['id'] ?? null;
        if (!$userId) {
            header("Location: " . BASE_URL . "admin_users_list");
            exit;
        }

        $userModel = new User();
        $user = $userModel->getById($userId);
        
        if (!$user) {
            die("Użytkownik o podanym ID nie istnieje.");
        }

        require_once 'app/Views/admin/user_details.php';
    }

    public function deleteUser()
    {
        $userId = $_GET['id'] ?? null;
        
        if ($userId && (int)$userId !== (int)$_SESSION['user_id']) {
            $userModel = new User();
            $userModel->delete($userId);
        }

        // Po usunięciu wracamy na osobną stronę listy użytkowników
        header("Location: " . BASE_URL . "admin_users_list");
        exit;
    }

    public function localizations()
    {
        require_once 'app/Models/Localization.php';
        $locModel = new Localization();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action_type'] ?? '';
            
            if ($action === 'add') {
                $locModel->add($_POST);
            } elseif ($action === 'edit') {
                $locModel->update($_POST['id'], $_POST);
            }
            
            header("Location: " . BASE_URL . "admin_localizations");
            exit;
        }

        $localizations = $locModel->getAll();
        require_once 'app/Views/admin/localizations.php';
    }
}