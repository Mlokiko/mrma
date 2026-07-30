<?php
class Client
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $sql = "INSERT INTO clients (client_type, first_name, last_name, nip, primary_phone, additional_phones, email, preferred_contact) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['client_type'],
            $data['first_name'],
            $data['last_name'] ?? null,
            $data['nip'] ?? null,
            $data['primary_phone'],
            $data['additional_phones'] ?? null,
            $data['email'] ?? null,
            $data['preferred_contact'] ?? null
        ]);

        return $this->db->lastInsertId();
    }
    public function getAll()
    {
        // Pobieramy wszystkich klientów posortowanych po nazwisku/imieniu
        return $this->db->query("SELECT * FROM clients ORDER BY last_name ASC, first_name ASC")->fetchAll();
    }

    public function search($term)
    {
        $term = '%' . $term . '%';
        $sql = "SELECT * FROM clients 
                WHERE first_name LIKE ? OR last_name LIKE ? OR primary_phone LIKE ? OR email LIKE ?
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$term, $term, $term, $term]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Aktualizuje telefon i dorzuca do JSONa nowe numery
    public function updateContact($id, $primaryPhone, $preferredContact, $newAdditionalPhones = [])
    {
        $client = $this->getById($id);

        $existingPhones = [];
        // Jeśli klient miał już wpisane inne numery, odkodowujemy je z JSONa
        if (!empty($client['additional_phones'])) {
            $existingPhones = json_decode($client['additional_phones'], true) ?: [];
        }

        // Łączymy stare numery z nowo dodanymi
        $mergedPhones = array_merge($existingPhones, $newAdditionalPhones);
        $phonesJson = empty($mergedPhones) ? null : json_encode($mergedPhones, JSON_UNESCAPED_UNICODE);

        $sql = "UPDATE clients SET primary_phone = ?, preferred_contact = ?, additional_phones = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$primaryPhone, $preferredContact, $phonesJson, $id]);
    }

    // Aktualizuje notatkę wewnętrzną przypisaną do klienta
    public function updateNote($id, $note)
    {
        $stmt = $this->db->prepare("UPDATE clients SET internal_note = ? WHERE id = ?");
        return $stmt->execute([$note, $id]);
    }

    public function getRmaHistory($clientId)
    {
        // Upewnij się, że wybierasz r.final_cost oraz r.picked_up_at!
        $sql = "SELECT r.id, r.created_at, r.picked_up_at, r.status, r.final_cost, 
                       dt.name as type_name, man.name as manufacturer_name, dm.name as model_name
                FROM rma r
                LEFT JOIN device_models dm ON r.device_model_id = dm.id
                LEFT JOIN device_manufacturers man ON dm.manufacturer_id = man.id
                LEFT JOIN device_types dt ON dm.device_type_id = dt.id
                WHERE r.client_id = ?
                ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public function getRelatedClientsDetails($jsonString)
    {
        if (empty($jsonString))
            return [];

        $relations = json_decode($jsonString, true);
        if (!is_array($relations) || empty($relations))
            return [];

        // Wyciągamy same ID do tablicy
        $ids = array_column($relations, 'id');
        if (empty($ids))
            return [];

        // Przygotowujemy zapytanie z odpowiednią liczbą znaków zapytania
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id, first_name, last_name, primary_phone FROM clients WHERE id IN ($placeholders)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        $clientsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Łączymy dane z bazy z nazwą relacji z naszego JSONa
        $result = [];
        foreach ($clientsData as $client) {
            foreach ($relations as $rel) {
                if ($rel['id'] == $client['id']) {
                    $client['relation_type'] = $rel['relation'];
                    $result[] = $client;
                    break;
                }
            }
        }

        return $result;
    }
    /**
     * Pobiera wszystkie relacje klienta (w dwie strony) z tabeli pivot przy użyciu szybkich JOIN-ów
     */
    public function getAllRelatedClients($clientId)
    {
        $result = [];
        $db = Database::getInstance();

        // 1. Relacje zainicjowane przez TEGO klienta
        $sqlMy = "SELECT cr.related_client_id as id, cr.relation_type, c.first_name, c.last_name, c.primary_phone
                  FROM client_relations cr
                  JOIN clients c ON cr.related_client_id = c.id
                  WHERE cr.client_id = ?";
        $stmtMy = $db->prepare($sqlMy);
        $stmtMy->execute([$clientId]);

        foreach ($stmtMy->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $row['initiated_by_me'] = true; // Flaga dla widoku: Ja go dodałem
            $result[$row['id']] = $row;
        }

        // 2. Relacje odwrotne: Ktoś inny dodał mnie u siebie
        $sqlTheir = "SELECT cr.client_id as id, cr.relation_type, c.first_name, c.last_name, c.primary_phone
                     FROM client_relations cr
                     JOIN clients c ON cr.client_id = c.id
                     WHERE cr.related_client_id = ?";
        $stmtTheir = $db->prepare($sqlTheir);
        $stmtTheir->execute([$clientId]);

        foreach ($stmtTheir->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($result[$row['id']]))
                continue; // Pomijamy duble
            $row['initiated_by_me'] = false; // Flaga dla widoku: On mnie dodał
            $result[$row['id']] = $row;
        }

        return array_values($result);
    }

    /**
     * Synchronizuje (czyści i zapisuje na nowo) relacje w tabeli pivot przy użyciu Transakcji
     */
    public function syncRelations($clientId, $relations)
    {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            // 1. Usuwamy stare relacje, które wyszły od tego użytkownika
            $stmtDelete = $db->prepare("DELETE FROM client_relations WHERE client_id = ?");
            $stmtDelete->execute([$clientId]);

            // 2. Wstawiamy zaktualizowaną listę
            if (!empty($relations)) {
                $stmtInsert = $db->prepare("INSERT INTO client_relations (client_id, related_client_id, relation_type) VALUES (?, ?, ?)");
                foreach ($relations as $rel) {
                    $stmtInsert->execute([$clientId, $rel['id'], $rel['relation']]);
                }
            }

            $db->commit();
        } catch (PDOException $e) {
            $db->rollBack();
            throw $e; // Przekaż błąd dalej w razie awarii bazy
        }
    }

}