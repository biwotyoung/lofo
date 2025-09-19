<?php
class Item {
    private $conn;
    private $table = "items";

    public $id;
    public $type;
    public $status;
    public $title;
    public $description;
    public $location;
    public $date;
    public $image;
    public $contact;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all items (with optional filters)
    public function read($status = null, $type = null, $search = null) {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        
        if ($status && $status !== 'all') {
            $query .= " AND status = :status";
        }
        if ($type && $type !== 'all') {
            $query .= " AND type = :type";
        }
        if ($search) {
            $query .= " AND (title LIKE :search OR description LIKE :search)";
        }
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);

        if ($status && $status !== 'all') {
            $stmt->bindParam(':status', $status);
        }
        if ($type && $type !== 'all') {
            $stmt->bindParam(':type', $type);
        }
        if ($search) {
            $searchTerm = "%$search%";
            $stmt->bindParam(':search', $searchTerm);
        }

        $stmt->execute();
        return $stmt;
    }

    // Create new item
    public function create() {
        $query = "INSERT INTO " . $this->table . "
            SET type = :type,
                status = :status,
                title = :title,
                description = :description,
                location = :location,
                date = :date,
                image = :image,
                contact = :contact";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->contact = htmlspecialchars(strip_tags($this->contact));

        // Bind parameters
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':contact', $this->contact);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>