<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "dp.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid data"
    ]);
    exit;
}

$user_email = $data["user_email"] ?? "";
$expense_name = $data["expense_name"] ?? "";
$category = $data["category"] ?? "";
$amount = $data["amount"] ?? 0;
$expense_date = $data["expense_date"] ?? date("Y-m-d");

if (
    $user_email === "" ||
    $expense_name === "" ||
    $category === "" ||
    $amount <= 0
) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter valid expense details"
    ]);
    exit;
}

$sql = "INSERT INTO expenses
        (user_email, expense_name, category, amount, expense_date)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sssds",
    $user_email,
    $expense_name,
    $category,
    $amount,
    $expense_date
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "Expense added successfully"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>