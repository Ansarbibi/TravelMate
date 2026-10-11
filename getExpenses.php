<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "dp.php";

$user_email = $_GET["user_email"] ?? "";

if ($user_email === "") {

    echo json_encode([
        "success" => false,
        "message" => "User email required"
    ]);

    exit;
}

$sql = "SELECT id, expense_name, category, amount, expense_date
        FROM expenses
        WHERE user_email = ?
        ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $user_email
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$expenses = [];

while ($row = mysqli_fetch_assoc($result)) {

    $expenses[] = $row;

}

echo json_encode([
    "success" => true,
    "expenses" => $expenses
]);

mysqli_stmt_close($stmt);

mysqli_close($conn);

?>