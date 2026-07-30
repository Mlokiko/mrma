<?php
class Rma
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // --- NOWE METODY DO POBIERANIA DANYCH DO FORMULARZA ---

    public function getLocalizations()
    {
        return $this->db->query("SELECT id, name FROM localizations ORDER BY name ASC")->fetchAll();
    }

    public function getDeviceModelsCombined()
    {
        // Łączymy tabele, aby wyświetlić ładną nazwę: Typ - Producent - Model
        $sql = "SELECT dm.id, dt.name as type_name, man.name as manufacturer_name, dm.name as model_name 
                FROM device_models dm
                JOIN device_types dt ON dm.device_type_id = dt.id
                JOIN device_manufacturers man ON dm.manufacturer_id = man.id
                WHERE dm.is_active = 1
                ORDER BY dt.name, man.name, dm.name";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        // Dodano pobieranie c.id, c.additional_phones oraz r.updated_at
        $sql = "SELECT r.*, 
                       IF(r.partner_id IS NOT NULL, 'Partner', c.client_type) as client_type,
                       l.name as localization_name,
                       c.id as client_id, c.first_name as client_first_name, c.last_name as client_last_name, 
                       c.nip as client_nip, c.primary_phone, c.additional_phones, c.email,
                       p.company_name as partner_company_name, p.primary_phone as partner_phone, p.email as partner_email,
                       dt.name as type_name, man.name as manufacturer_name, dm.name as model_name,
                       u.username as user_name
                FROM rma r
                LEFT JOIN localizations l ON r.localization_id = l.id
                LEFT JOIN clients c ON r.client_id = c.id
                LEFT JOIN partners p ON r.partner_id = p.id
                LEFT JOIN device_models dm ON r.device_model_id = dm.id
                LEFT JOIN device_types dt ON dm.device_type_id = dt.id
                LEFT JOIN device_manufacturers man ON dm.manufacturer_id = man.id
                LEFT JOIN users u ON r.received_by_user_id = u.id
                WHERE r.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAdjacentIds($id)
    {
        $prevStmt = $this->db->prepare("SELECT id FROM rma WHERE id < ? ORDER BY id DESC LIMIT 1");
        $prevStmt->execute([$id]);
        $prevId = $prevStmt->fetchColumn();

        $nextStmt = $this->db->prepare("SELECT id FROM rma WHERE id > ? ORDER BY id ASC LIMIT 1");
        $nextStmt->execute([$id]);
        $nextId = $nextStmt->fetchColumn();

        return ['prev' => $prevId, 'next' => $nextId];
        //return ['prev' => $Id - 1, 'next' => $Id + 1];
    }

    public function create($data)
    {
        $sql = "INSERT INTO rma (
                    localization_id, client_id, partner_id, device_model_id, 
                    serial_number, device_lock_code, issue_description, liquid_damage_status, 
                    received_by_user_id, is_express, estimated_cost, max_approved_cost, advance_payment
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['localization_id'],
            $data['client_id'],
            $data['partner_id'],
            $data['device_model_id'],
            $data['serial_number'] ?: null,
            $data['device_lock_code'] ?: null,
            $data['issue_description'],
            $data['liquid_damage_status'],
            $data['received_by_user_id'],
            $data['is_express'] ?? 0,
            $data['estimated_cost'] ?: null,
            $data['max_approved_cost'] ?: null,
            $data['advance_payment'] ?: null
        ]);
    }

    // Pobieranie listy RMA z obsługą wyszukiwarki i dołączeniem wszystkich relacji
    public function searchListAdvanced($query = '', $dbFilters = [], $month = null, $limit = null, $all = false)
    {
        $sql = "SELECT r.*, 
                       IF(r.partner_id IS NOT NULL, 'Partner', c.client_type) as client_type,
                       c.first_name as client_first_name, 
                       c.last_name as client_last_name, 
                       c.primary_phone,
                       p.company_name,
                       p.primary_phone as partner_phone,
                       man.name as manufacturer_name, 
                       dm.name as model_name,
                       dt.name as type_name
                FROM rma r
                LEFT JOIN clients c ON r.client_id = c.id
                LEFT JOIN partners p ON r.partner_id = p.id
                LEFT JOIN device_models dm ON r.device_model_id = dm.id
                LEFT JOIN device_manufacturers man ON dm.manufacturer_id = man.id
                LEFT JOIN device_types dt ON dm.device_type_id = dt.id";

        $params = [];
        $whereConditions = ["1=1"];

        // 1. Filtrowanie po miesiącu kalendarzowym (tylko gdy nie wybrano 'all' i brak limitu)
        if (!$all && empty($limit) && !empty($month)) {
            $whereConditions[] = "DATE_FORMAT(r.created_at, '%Y-%m') = ?";
            $params[] = $month;
        }

        // 2. Szukanie frazy tekstowej z głównego pola wyszukiwarki
        if (!empty($query)) {
            $likeQuery = '%' . $query . '%';
            $whereConditions[] = "(r.id = ? OR r.issue_description LIKE ? OR man.name LIKE ? OR dm.name LIKE ? OR r.status LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ? OR p.company_name LIKE ? OR c.primary_phone LIKE ? OR p.primary_phone LIKE ? OR dt.name LIKE ?)";
            $params[] = is_numeric($query) ? (int)$query : 0;
            $params[] = $likeQuery; $params[] = $likeQuery; $params[] = $likeQuery; $params[] = $likeQuery;
            $params[] = $likeQuery; $params[] = $likeQuery; $params[] = $likeQuery; $params[] = $likeQuery;
            $params[] = $likeQuery; $params[] = $likeQuery;
        }

        // 3. Zaawansowane filtry przesłane z panelu bocznego do bazy
        if (!empty($dbFilters['client_type'])) {
            $ctConditions = [];
            foreach ($dbFilters['client_type'] as $ct) {
                if ($ct === 'Partner') {
                    $ctConditions[] = "r.partner_id IS NOT NULL";
                } else {
                    $ctConditions[] = "(r.partner_id IS NULL AND c.client_type = ?)";
                    $params[] = $ct;
                }
            }
            if (!empty($ctConditions)) {
                $whereConditions[] = "(" . implode(" OR ", $ctConditions) . ")";
            }
        }

        if (!empty($dbFilters['status'])) {
            $placeholders = implode(',', array_fill(0, count($dbFilters['status']), '?'));
            $whereConditions[] = "r.status IN ($placeholders)";
            foreach ($dbFilters['status'] as $st) {
                $params[] = $st;
            }
        }

        if (!empty($dbFilters['type'])) {
            $whereConditions[] = "dt.name LIKE ?";
            $params[] = '%' . $dbFilters['type'] . '%';
        }

        if (!empty($dbFilters['manufacturer'])) {
            $whereConditions[] = "man.name LIKE ?";
            $params[] = '%' . $dbFilters['manufacturer'] . '%';
        }

        if (!empty($dbFilters['model'])) {
            $whereConditions[] = "dm.name LIKE ?";
            $params[] = '%' . $dbFilters['model'] . '%';
        }

        if (!empty($dbFilters['date_from'])) {
            $whereConditions[] = "r.created_at >= ?";
            $params[] = $dbFilters['date_from'] . ' 00:00:00';
        }

        if (!empty($dbFilters['date_to'])) {
            $whereConditions[] = "r.created_at <= ?";
            $params[] = $dbFilters['date_to'] . ' 23:59:59';
        }

        $sql .= " WHERE " . implode(" AND ", $whereConditions);
        $sql .= " ORDER BY r.created_at DESC";

        if (!empty($limit)) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateStatus($rmaId, $newStatus, $userId)
    {
        $oldStatus = $this->getById($rmaId)['status'];
        if ($oldStatus === $newStatus)
            return false;

        // Logika dat: jeśli "Wydane", uzupełnia obie daty. Inne końcowe uzupełniają tylko ended_at.
        $isEnded = in_array($newStatus, ['Gotowe', 'Wydane', 'Reklamacja', 'Anulowane']) ? "NOW()" : "NULL";
        $isPickedUp = ($newStatus === 'Wydane') ? "NOW()" : "NULL";

        try {
            $this->db->beginTransaction();

            $stmt1 = $this->db->prepare("UPDATE rma SET status = ?, ended_at = $isEnded, picked_up_at = $isPickedUp WHERE id = ?");
            $stmt1->execute([$newStatus, $rmaId]);

            // Notatka usunięta z zapytania
            $stmt2 = $this->db->prepare("INSERT INTO rma_status_history (rma_id, old_status, new_status, user_id, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt2->execute([$rmaId, $oldStatus, $newStatus, $userId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateCosts($rmaId, $est, $max, $parts, $internal, $final, $payment, $advance)
    {
        $sql = "UPDATE rma SET estimated_cost = ?, max_approved_cost = ?, parts_cost = ?, internal_cost = ?, final_cost = ?, payment_method = ?, advance_payment = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$est, $max, $parts, $internal, $final, $payment, $advance, $rmaId]);
    }

    public function getStatusHistory($rmaId)
    {
        $sql = "SELECT sh.*, u.username as user_name 
                FROM rma_status_history sh
                LEFT JOIN users u ON sh.user_id = u.id
                WHERE sh.rma_id = ?
                ORDER BY sh.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$rmaId]);
        return $stmt->fetchAll();
    }

    public function updateDeviceData($rmaId, $serialNumber, $lockCode, $liquidStatus)
    {
        $sql = "UPDATE rma SET serial_number = ?, device_lock_code = ?, liquid_damage_status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$serialNumber, $lockCode, $liquidStatus, $rmaId]);
    }

    public function updateWarranty($id, $months, $issuedAt, $scope, $covered)
{
    // Używamy Database::getInstance() w oparciu o Twój plik Database.php
    $db = Database::getInstance();
    $sql = "UPDATE rma SET 
            warranty_months = :months, 
            warranty_issued_at = :issuedAt, 
            warranty_scope = :scope, 
            warranty_covered = :covered 
            WHERE id = :id";
            
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':months' => $months,
        ':issuedAt' => $issuedAt,
        ':scope' => $scope,
        ':covered' => $covered,
        ':id' => $id
    ]);
}

public function deleteWarranty($id)
{
    $db = Database::getInstance();
    $sql = "UPDATE rma SET 
            warranty_months = NULL, 
            warranty_issued_at = NULL, 
            warranty_scope = NULL, 
            warranty_covered = NULL 
            WHERE id = :id";
            
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);
}
}