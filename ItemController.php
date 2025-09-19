<?php
// Add these headers at the VERY TOP (before any output)
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Disable error display to prevent HTML output
ini_set('display_errors', 0);
error_reporting(0);

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../models/Item.php';
require_once '../config/database.php';

// Initialize response array
$response = [];
$httpStatus = 200;

try {
    $database = new Database();
    $db = $database->getConnection();
    $item = new Item($db);

    // Handle GET request (fetch items)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : null;

        $stmt = $item->read($status, $type, $search);
        $num = $stmt->rowCount();

        if ($num > 0) {
            $items_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $items_arr[] = $row;
            }
            $response = [
                "success" => true,
                "data" => $items_arr
            ];
        } else {
            $httpStatus = 404;
            $response = [
                "success" => false,
                "message" => "No items found."
            ];
        }
    }

    // Handle POST request (create item)
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle image upload
        $target_dir = "../../uploads/";
        $imagePath = null;
        $uploadOk = 1;

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            $image_name = basename($_FILES["image"]["name"]);
            $target_file = $target_dir . uniqid() . "_" . $image_name;
            
            // Check if image is valid
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $imagePath = $target_file;
                } else {
                    $uploadOk = 0;
                }
            } else {
                $uploadOk = 0;
            }
        }

        if ($uploadOk == 0) {
            $httpStatus = 400;
            $response = [
                "success" => false,
                "error" => "Invalid image file"
            ];
        } else {
            // Set item properties
            $item->type = $_POST['type'] ?? '';
            $item->status = $_POST['status'] ?? '';
            $item->title = $_POST['title'] ?? '';
            $item->description = $_POST['description'] ?? '';
            $item->location = $_POST['location'] ?? '';
            $item->date = $_POST['date'] ?? '';
            $item->contact = $_POST['contact'] ?? '';
            $item->image = $imagePath;

            if ($item->create()) {
                $httpStatus = 201;
                $response = [
                    "success" => true,
                    "message" => "Item created successfully."
                ];
            } else {
                $httpStatus = 500;
                $response = [
                    "success" => false,
                    "message" => "Unable to create item."
                ];
            }
        }
    }

    // Handle unsupported methods
    else {
        $httpStatus = 405;
        $response = [
            "success" => false,
            "message" => "Method not allowed."
        ];
    }

} catch (Exception $e) {
    $httpStatus = 500;
    $response = [
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ];
}

// Set HTTP status and output JSON
http_response_code($httpStatus);
echo json_encode($response);
exit();
?>