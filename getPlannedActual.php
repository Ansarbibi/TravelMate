<?php

include "dp.php";

$sql = "SELECT SUM(amount) AS actualExpenses FROM expenses";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "error" => "Database error"
    ]);
    exit;
}

$row = $result->fetch_assoc();

$actualExpenses = $row["actualExpenses"] ?? 0;

echo json_encode([
    "actualExpenses" => $actualExpenses
]);

$conn->close();

?>