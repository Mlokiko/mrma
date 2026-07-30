<?php
class ClientController
{

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }
        require_once 'app/Models/Client.php';
        $clients = (new Client())->getAll();
        require_once 'app/Views/client/list.php';
    }

    // NOWA METODA: Profil i szczegóły klienta
    public function details()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        if (!isset($_GET['id'])) {
            header("Location: " . BASE_URL . "client_list");
            exit;
        }

        $id = (int) $_GET['id'];
        require_once 'app/Models/Client.php';
        $clientModel = new Client();

        // OBSŁUGA PRZETWARZANIA DANYCH POST (Notatki, Statystyki, Edycja)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
            $actionType = $_POST['action_type'];
            $db = Database::getInstance();

            switch ($actionType) {
                case 'update_note':
                    $clientModel->updateNote($id, $_POST['internal_note']);
                    break;

                case 'recalculate_stats':
                    $history = $clientModel->getRmaHistory($id);
                    $totalSpent = 0.00;
                    $totalRma = count($history);

                    foreach ($history as $rmaItem) {
                        // Upewnij się, że pole final_cost istnieje. Jeśli u Ciebie nazywa się inaczej, zmień to poniżej:
                        if (isset($rmaItem['final_cost']) && $rmaItem['final_cost'] !== null && $rmaItem['final_cost'] !== '') {
                            $totalSpent += (float) $rmaItem['final_cost'];
                        }
                    }

                    // Używamy zmienionej nazwy kolumny: rma_count
                    $stmtCalc = $db->prepare("UPDATE clients SET total_spent = ?, rma_count = ? WHERE id = ?");
                    $stmtCalc->execute([$totalSpent, $totalRma, $id]);
                    break;

                case 'edit_client_core':
                    // Przetwarzanie interaktywnych dodatkowych numerów telefonów
                    $jsonPhones = null;
                    if (isset($_POST['additional_phones_number']) && is_array($_POST['additional_phones_number'])) {
                        $finalPhones = [];
                        foreach ($_POST['additional_phones_number'] as $index => $number) {
                            $number = trim($number);
                            if (!empty($number)) {
                                $desc = isset($_POST['additional_phones_desc'][$index]) ? trim($_POST['additional_phones_desc'][$index]) : '';
                                $finalPhones[] = ['number' => $number, 'description' => $desc];
                            }
                        }
                        if (!empty($finalPhones)) {
                            $jsonPhones = json_encode($finalPhones, JSON_UNESCAPED_UNICODE);
                        }
                    }

                    // Obsługa "Nie wybrano" jako NULL
                    $prefContact = !empty($_POST['preferred_contact']) ? $_POST['preferred_contact'] : null;

                    $sql = "UPDATE clients 
                            SET first_name = ?, last_name = ?, primary_phone = ?, additional_phones = ?, email = ?, preferred_contact = ? 
                            WHERE id = ?";
                    $stmtUpdate = $db->prepare($sql);
                    $stmtUpdate->execute([
                        $_POST['first_name'],
                        !empty($_POST['last_name']) ? $_POST['last_name'] : null,
                        $_POST['primary_phone'],
                        $jsonPhones,
                        !empty($_POST['email']) ? $_POST['email'] : null,
                        $prefContact,
                        $id
                    ]);
                    break;
                case 'edit_relations':
                    $newRelations = [];
                    if (isset($_POST['related_id']) && is_array($_POST['related_id'])) {
                        foreach ($_POST['related_id'] as $index => $relId) {
                            $relId = (int) trim($relId);
                            $relName = trim($_POST['related_name'][$index] ?? '');

                            if ($relId > 0 && $relId != $id) {
                                $newRelations[] = [
                                    'id' => $relId,
                                    'relation' => $relName
                                ];
                            }
                        }
                    }

                    // WYWOŁANIE NOWEGO MECHANIZMU PIVOT ZAMIAST ZAPISU JSON
                    $clientModel->syncRelations($id, $newRelations);
                    break;
            }
            header("Location: " . BASE_URL . "client/" . $id);
            exit;
        }

        $client = $clientModel->getById($id);

        if (!$client) {
            $errorMessage = "Klient o identyfikatorze #{$id} nie istnieje w bazie danych.";
            require_once 'app/Views/errors/404.php';
            exit;
        }

        // Pobieramy historię napraw tego klienta
        $rmaHistory = $clientModel->getRmaHistory($id);
        $relatedClients = $clientModel->getAllRelatedClients($id);

        require_once 'app/Views/client/details.php';
    }
}