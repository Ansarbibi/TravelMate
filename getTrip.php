<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

include "dp.php";

$id = $_GET["id"] ?? "";

if ($id == "") {
    echo json_encode([
        "success" => false,
        "message" => "Trip ID missing"
    ]);
    exit;
}

$id = intval($id);

$sql = "SELECT * FROM trips WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Database query failed"
    ]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    echo json_encode([
        "success" => true,
        "trip" => $row
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Trip not found"
    ]);
}

$stmt->close();
$conn->close();

?>