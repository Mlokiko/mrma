<?php
class Note
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Pobiera wszystkie notatki przypisane do danego zgłoszenia RMA
    public function getByRmaId($rmaId)
    {
        $sql = "SELECT rn.*, u.username as author_name 
                FROM rma_notes rn
                JOIN users u ON rn.user_id = u.id
                WHERE rn.rma_id = ?
                ORDER BY rn.created_at DESC"; // Najnowsze na górze
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$rmaId]);
        return $stmt->fetchAll();
    }

    // Zapisuje nową notatkę w bazie danych
    public function create($rmaId, $userId, $text, $isInternal)
    {
        $sql = "INSERT INTO rma_notes (rma_id, user_id, note_text, is_internal) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$rmaId, $userId, $text, $isInternal]);
    }

    // Aktualizuje istniejącą notatkę (tylko jeśli należy do podanego użytkownika)
    public function updateNote($noteId, $userId, $text, $isInternal)
    {
        $sql = "UPDATE rma_notes 
                SET note_text = ?, is_internal = ?, updated_at = NOW() 
                WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$text, $isInternal, $noteId, $userId]);
    }
}