// db/WeatherLocation.php
<?php
class WeatherLocation {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getFavorites() {
        $query = "SELECT * FROM favorites ORDER BY created_at DESC";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function addFavorite($location, $notes = '') {
        $query = "INSERT INTO favorites (location, notes) VALUES (:location, :notes)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':notes', $notes);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function deleteFavorite($id) {
        $query = "DELETE FROM favorites WHERE id = :id";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
