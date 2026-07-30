<?php
class Localization
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        return $this->db->query("SELECT * FROM localizations ORDER BY id ASC")->fetchAll();
    }

    public function add($data)
    {
        $stmt = $this->db->prepare("INSERT INTO localizations (name, postal_code, city, street, building_number) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'], 
            $data['postal_code'] ?: null, 
            $data['city'] ?: null, 
            $data['street'] ?: null, 
            $data['building_number'] ?: null
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE localizations SET name=?, postal_code=?, city=?, street=?, building_number=? WHERE id=?");
        return $stmt->execute([
            $data['name'], 
            $data['postal_code'] ?: null, 
            $data['city'] ?: null, 
            $data['street'] ?: null, 
            $data['building_number'] ?: null, 
            $id
        ]);
    }
}