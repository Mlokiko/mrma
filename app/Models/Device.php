<?php
class Device
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getActiveTypes()
    {
        return $this->db->query("SELECT id, name FROM device_types WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
    }

    public function getManufacturersByType($typeId)
    {
        $sql = "SELECT m.id, m.name 
                FROM device_manufacturers m
                JOIN manufacturer_device_types mdt ON m.id = mdt.manufacturer_id
                WHERE mdt.device_type_id = ? AND m.is_active = 1
                ORDER BY m.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$typeId]);
        return $stmt->fetchAll();
    }

    public function getModels($typeId, $manufacturerId)
    {
        $sql = "SELECT id, name FROM device_models 
                WHERE device_type_id = ? AND manufacturer_id = ? AND is_active = 1
                ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$typeId, $manufacturerId]);
        return $stmt->fetchAll();
    }

    public function createType($name)
    {
        $stmt = $this->db->prepare("INSERT INTO device_types (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public function createManufacturer($name, $typeIds)
    {
        try {
            $this->db->beginTransaction();

            // 1. Zapis producenta
            $stmt = $this->db->prepare("INSERT INTO device_manufacturers (name) VALUES (?)");
            $stmt->execute([$name]);
            $manufacturerId = $this->db->lastInsertId();

            // 2. Zapis powiązań z typami (np. Samsung produkuje Smartfony i Tablety)
            if (!empty($typeIds) && is_array($typeIds)) {
                $stmtLink = $this->db->prepare("INSERT INTO manufacturer_device_types (manufacturer_id, device_type_id) VALUES (?, ?)");
                foreach ($typeIds as $typeId) {
                    $stmtLink->execute([$manufacturerId, $typeId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function createModel($manufacturerId, $typeId, $name)
    {
        $stmt = $this->db->prepare("INSERT INTO device_models (manufacturer_id, device_type_id, name) VALUES (?, ?, ?)");
        return $stmt->execute([$manufacturerId, $typeId, $name]);
    }

    public function createModelCode($modelId, $codeName)
    {
        $stmt = $this->db->prepare("INSERT INTO device_model_codes (device_model_id, code_name) VALUES (?, ?)");
        return $stmt->execute([$modelId, $codeName]);
    }

    // Pomocnicze funkcje do pobierania list w formularzu (niezależne od typu)
    public function getAllManufacturers()
    {
        return $this->db->query("SELECT id, name FROM device_manufacturers WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
    }

    public function getAllModels()
    {
        return $this->db->query("SELECT id, name FROM device_models WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
    }
}