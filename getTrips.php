<?php

include __DIR__ . "/dp.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

$user_email = trim($_GET["user_email"] ?? "");

if ($user_email === "") {

    echo json_encode([
        "success" => false,
        "message" => "User email is required",
        "trips" => []
    ]);

    exit;
}

$sql = "SELECT
            id,
            user_email,
            start_place,
            destination,
            start_date,
            end_date,
            duration,
            budget
        FROM trips
        WHERE user_email = ?
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $conn->error,
        "trips" => []
    ]);

    exit;
}

$stmt->bind_param("s", $user_email);

$stmt->execute();

$result = $stmt->get_result();

$trips = [];

while ($row = $result->fetch_assoc()) {

    $trips[] = $row;

}

echo json_encode([
    "success" => true,
    "trips" => $trips
]);

$stmt->close();
$conn->close();

?>