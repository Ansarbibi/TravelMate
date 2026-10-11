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

$input = json_decode(file_get_contents("php://input"), true);

$name = trim($input["name"] ?? "");
$email = trim($input["email"] ?? "");
$password = $input["password"] ?? "";

if ($name === "" || $email === "" || $password === "") {
    echo json_encode([
        "success" => false,
        "message" => "Please fill all fields."
    ]);
    exit;
}

/* Check existing email */

$checkSql = "SELECT id FROM users WHERE email = ?";

$checkStmt = $conn->prepare($checkSql);

if (!$checkStmt) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $conn->error
    ]);
    exit;
}

$checkStmt->bind_param("s", $email);
$checkStmt->execute();

$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {

    echo json_encode([
        "success" => false,
        "message" => "Email already registered. Please login."
    ]);

    $checkStmt->close();
    $conn->close();
    exit;
}

$checkStmt->close();

/* Hash password */

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

/* Insert user */

$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);

    $conn->close();
    exit;
}

$stmt->bind_param(
    "sss",
    $name,
    $email,
    $hashedPassword
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Registration successful. Please login."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Registration failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();

?>