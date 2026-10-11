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

$email = trim($input["email"] ?? "");
$password = $input["password"] ?? "";

if ($email === "" || $password === "") {

    echo json_encode([
        "success" => false,
        "message" => "Please enter email and password."
    ]);

    exit;
}

/* Find user */

$sql = "SELECT id, name, email, password
        FROM users
        WHERE email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $conn->error
    ]);

    $conn->close();
    exit;
}

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    /* Check password */

    if (password_verify($password, $user["password"])) {

        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "user" => [
                "id" => $user["id"],
                "name" => $user["name"],
                "email" => $user["email"]
            ]
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password."
        ]);
    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
}

$stmt->close();
$conn->close();

?>