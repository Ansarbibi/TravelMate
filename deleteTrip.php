<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

include "dp.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["trip_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Trip ID missing"
    ]);
    exit;
}

$trip_id = intval($data["trip_id"]);

$sql = "DELETE FROM trips WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $trip_id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        echo json_encode([
            "success" => true,
            "message" => "Trip deleted successfully"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Trip not found"
        ]);
    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Delete failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();

?>