<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include __DIR__ . "/dp.php";

$user_email = $_GET["user_email"] ?? "";

if ($user_email === "") {
    echo json_encode([
        "success" => false,
        "message" => "User email required"
    ]);
    exit;
}

$sql = "SELECT id, user_email, start_place, destination,
               start_date, end_date, budget
        FROM trips
        WHERE user_email = ?
        ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Query preparation failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $user_email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$trips = [];

while ($row = mysqli_fetch_assoc($result)) {
    $trips[] = [
        "from_place" => $row["start_place"],
        "to_place" => $row["destination"],
        "start_date" => $row["start_date"],
        "end_date" => $row["end_date"],
        "budget" => $row["budget"]
    ];
}

echo json_encode([
    "success" => true,
    "trips" => $trips
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
