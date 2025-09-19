<?php
header('Content-Type: application/json');
echo json_encode($response);
// api/index.php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Disable error display
ini_set('display_errors', 0);
error_reporting(0);

// Include necessary files
require_once 'config/database.php';
require_once 'controllers/ItemController.php';

// Handle the request
// ... (your API logic here)
?>