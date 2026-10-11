<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "dp.php";

$data = json_decode(file_get_contents("php://input"), true);

file_put_contents("budget_test.txt",json_encode($data)

);

$user_email = $data["user_email"] ?? "";
$days = $data["days"] ?? 0;
$daily_cost = $data["daily_cost"] ?? 0;
$transport_cost = $data["transport_cost"] ?? 0;
$accommodation_cost = $data["accommodation_cost"] ?? 0;
$total_budget = $data["total_budget"] ?? 0;

if (
    $user_email === "" ||
    $days <= 0
) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid budget data"
    ]);

    exit;
}

$sql = "INSERT INTO budget_estimates
        (user_email, days, daily_cost, transport_cost,
         accommodation_cost, total_budget)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sidddd",
    $user_email,
    $days,
    $daily_cost,
    $transport_cost,
    $accommodation_cost,
    $total_budget
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "Budget saved successfully"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Database error: " . mysqli_smt_error($stmt)
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>