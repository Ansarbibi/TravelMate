<?php

ob_start();
include __DIR__ . "/dp.php";
ob_end_clean();

$sql = "SELECT * FROM trips ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    header("Content-Type: application/json");
    echo json_encode([]);
    exit;
}

$trips = [];

while ($row = mysqli_fetch_assoc($result)) {
    $trips[] = $row;
}

header("Content-Type: application/json");
echo json_encode($trips);

mysqli_close($conn);

?>