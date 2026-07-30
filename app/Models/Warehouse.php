<?php
class Warehouse
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllParts()
    {
        $sql = "SELECT wp.*, wpc.name as category_name 
                FROM warehouse_parts wp
                JOIN warehouse_parts_categories wpc ON wp.category_id = wpc.id
                ORDER BY wp.updated_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // Pobieranie wszystkich dostępnych kategorii części
    public function getCategories()
    {
        return $this->db->query("SELECT id, name FROM warehouse_parts_categories ORDER BY name ASC")->fetchAll();
    }

    // Zapis nowej części do bazy danych
    public function createPart($data)
    {
        $sql = "INSERT INTO warehouse_parts (
                    category_id, manufacturer, part_model_code, condition_status, 
                    color, item_type, is_original, market_price, quantity, 
                    description, storage_location, compatible_device_ids, 
                    test_compatible_device_ids, technical_attributes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['category_id'],
            $data['manufacturer'] ?: null,
            $data['part_model_code'] ?: null,
            $data['condition_status'],
            $data['color'] ?: null,
            $data['item_type'],
            $data['is_original'] ?? 1,
            $data['market_price'] ?: null,
            $data['quantity'] ?? 0,
            $data['description'] ?: null,
            $data['storage_location'] ?: null,
            json_encode([]), // compatible_device_ids (fallback)
            json_encode([]), // test_compatible_device_ids (fallback)
            json_encode([])  // technical_attributes (fallback)
        ]);
    }
}