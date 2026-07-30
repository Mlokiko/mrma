<?php
class Partner
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        return $this->db->query("SELECT * FROM partners ORDER BY company_name ASC")->fetchAll();
    }

    public function create($data)
    {
        // Rozszerzenie zapytania INSERT o kolumnę additional_phones
        $sql = "INSERT INTO partners (company_name, representative_first_name, representative_last_name, primary_phone, additional_phones, email, address_location, internal_note) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['company_name'],
            $data['representative_first_name'] ?: null,
            $data['representative_last_name'] ?: null,
            $data['primary_phone'],
            $data['additional_phones'] ?? null, // Wstrzyknięcie przygotowanego ciągu JSON
            $data['email'] ?: null,
            $data['address_location'] ?: null,
            $data['internal_note'] ?: null
        ]);
    }

    // Pobiera dane konkretnego partnera po ID
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM partners WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Pobiera całą historię zgłoszeń RMA przypisanych do tego partnera B2B
    public function getRmaHistory($partnerId)
    {
        $sql = "SELECT r.id, r.created_at, r.picked_up_at, r.status, r.final_cost, 
                       dt.name as type_name, man.name as manufacturer_name, dm.name as model_name
                FROM rma r
                LEFT JOIN device_models dm ON r.device_model_id = dm.id
                LEFT JOIN device_manufacturers man ON dm.manufacturer_id = man.id
                LEFT JOIN device_types dt ON dm.device_type_id = dt.id
                WHERE r.partner_id = ?
                ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$partnerId]);
        return $stmt->fetchAll();
    }
}