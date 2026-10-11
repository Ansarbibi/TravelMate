<?php

include __DIR__ . "/dp.php";

header("Content-Type: application/json");

$email = $_GET["user_email"] ?? "";

if ($email === "") {
    echo json_encode([
        "success" => false,
        "message" => "Email is required"
    ]);
    exit;
}

$sql = "SELECT id, name, email FROM users WHERE email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "profile" => $user
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "User not found"
    ]);
}

$stmt->close();
$conn->close();

?>