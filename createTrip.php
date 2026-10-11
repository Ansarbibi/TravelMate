<?php

include __DIR__ . "/dp.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);

    exit;
}

/* Read JSON data */

$input = json_decode(
    file_get_contents("php://input"),
    true
);

$user_email = trim($input["user_email"] ?? "");
$start_place = trim($input["start_place"] ?? "");
$destination = trim($input["destination"] ?? "");
$start_date = trim($input["start_date"] ?? "");
$end_date = trim($input["end_date"] ?? "");
$budget = $input["budget"] ?? 0;

/* Validate */

if (
    $user_email === "" ||
    $start_place === "" ||
    $destination === "" ||
    $start_date === "" ||
    $end_date === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Please fill all trip details."
    ]);

    exit;
}

/* Check dates */

$start = new DateTime($start_date);
$end = new DateTime($end_date);

if ($end < $start) {

    echo json_encode([
        "success" => false,
        "message" => "End date cannot be before start date."
    ]);

    exit;
}

/* Calculate duration */

$duration = $start->diff($end)->days + 1;

/* Insert trip */

$sql = "INSERT INTO trips
        (user_email, start_place, destination, start_date, end_date, duration, budget)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "SQL Error: " . $conn->error
    ]);

    $conn->close();
    exit;
}

$stmt->bind_param(
    "sssssid",
    $user_email,
    $start_place,
    $destination,
    $start_date,
    $end_date,
    $duration,
    $budget
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Trip created successfully",
        "trip_id" => $stmt->insert_id,
        "duration" => $duration
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Trip creation failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();

?>