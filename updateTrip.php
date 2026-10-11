<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include __DIR__ . "/dp.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id =
    intval($data["id"] ?? 0);

$start_place =
    trim($data["start_place"] ?? "");

$destination =
    trim($data["destination"] ?? "");

$start_date =
    $data["start_date"] ?? "";

$end_date =
    $data["end_date"] ?? "";

$budget =
    floatval($data["budget"] ?? 0);


if($id <= 0){

    echo json_encode([
        "success" => false,
        "message" => "Trip ID missing"
    ]);

    exit;
}


if(
    $start_place === "" ||
    $destination === "" ||
    $start_date === "" ||
    $end_date === "" ||
    $budget <= 0
){

    echo json_encode([
        "success" => false,
        "message" => "Please fill all trip fields"
    ]);

    exit;
}


// Calculate duration

$start =
    new DateTime($start_date);

$end =
    new DateTime($end_date);

$duration =
    $start->diff($end)->days + 1;


// Update trip

$sql = "UPDATE trips SET
        start_place = ?,
        destination = ?,
        start_date = ?,
        end_date = ?,
        duration = ?,
        budget = ?
        WHERE id = ?";


$stmt =
    $conn->prepare($sql);


if(!$stmt){

    echo json_encode([
        "success" => false,
        "message" => "SQL Error: " . $conn->error
    ]);

    exit;
}


$stmt->bind_param(
    "ssssidi",
    $start_place,
    $destination,
    $start_date,
    $end_date,
    $duration,
    $budget,
    $id
);


if($stmt->execute()){

    echo json_encode([
        "success" => true,
        "message" => "Trip updated successfully"
    ]);

}
else{

    echo json_encode([
        "success" => false,
        "message" => "Error updating trip"
    ]);

}


$stmt->close();
$conn->close();

?>