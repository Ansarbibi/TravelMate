<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "dp.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? 0;
$user_email = $data["user_email"] ?? "";

if ($id <= 0 || $user_email === "") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid data"
    ]);

    exit;
}

$sql = "DELETE FROM expenses
        WHERE id = ? AND user_email = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $id,
    $user_email
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "Expense deleted successfully"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Database delete failed"
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>