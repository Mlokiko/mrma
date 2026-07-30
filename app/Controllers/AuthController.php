<?php
class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->userModel->login($_POST['username'], $_POST['password']);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['account_type'] = $user['account_type'];
                header("Location: " . BASE_URL . "rma_list");
                exit;
            } else {
                $error = "Błędne dane logowania.";
            }
        }
        require_once 'app/Views/auth/login.php';
    }

    public function register()
    {
        // BLOKADA: Tylko zalogowany Administrator może rejestrować konta
        if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'Admin') {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->userModel->register(
                $_POST['username'],
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['email'],
                $_POST['password']
            );
            // Po rejestracji wracamy do listy użytkowników w panelu admina
            header("Location: " . BASE_URL . "admin_panel");
            exit;
        }
        require_once 'app/Views/auth/register.php';
    }

    public function logout()
    {
        session_destroy();
        header("Location: " . BASE_URL . "login");
        exit;
    }

    public function account()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        $db = Database::getInstance();
        $successMessage = '';
        $errorMessage = '';

        // OBSŁUGA FORMULARZY (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Formularz 1: Aktualizacja danych profilu
            if (isset($_POST['update_profile'])) {
                $firstName = $_POST['first_name'] ?? '';
                $lastName = $_POST['last_name'] ?? '';
                $email = $_POST['email'] ?? '';
                $phone = $_POST['phone_number'] ?? '';

                if ($this->userModel->updateProfile($_SESSION['user_id'], $firstName, $lastName, $email, $phone)) {
                    $successMessage = "Dane profilu zostały pomyślnie zaktualizowane.";
                } else {
                    $errorMessage = "Wystąpił błąd podczas zapisu danych.";
                }
            }
            
            // Formularz 2: Zmiana hasła
            if (isset($_POST['change_password'])) {
                $oldPassword = $_POST['old_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if ($newPassword !== $confirmPassword) {
                    $errorMessage = "Nowe hasło i hasło powtórzone nie są identyczne.";
                } elseif (strlen($newPassword) < 6) {
                    $errorMessage = "Nowe hasło musi mieć co najmniej 6 znaków.";
                } else {
                    // Weryfikacja starego hasła
                    if ($this->userModel->verifyPassword($_SESSION['user_id'], $oldPassword)) {
                        // Zapis nowego hasła
                        if ($this->userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                            $successMessage = "Hasło zostało pomyślnie zmienione.";
                        } else {
                            $errorMessage = "Wystąpił błąd systemowy podczas zmiany hasła.";
                        }
                    } else {
                        $errorMessage = "Aktualne hasło jest nieprawidłowe.";
                    }
                }
            }
        }

        // Pobranie aktualnych danych użytkownika
        $stmt = $db->prepare("SELECT id, username, first_name, last_name, email, phone_number, account_type, last_login FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        require_once 'app/Views/auth/account.php';
    }
}