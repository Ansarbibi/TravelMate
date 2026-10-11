<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include __DIR__ . "/dp.php";


/* ONLY POST REQUEST */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request"
    ]);

    exit;
}


/* GET JSON DATA */

$data = json_decode(
    file_get_contents("php://input"),
    true
);


$name =
    trim($data["name"] ?? "");

$old_email =
    trim($data["old_email"] ?? "");

$new_email =
    trim($data["new_email"] ?? "");


/* VALIDATION */

if (
    $name === "" ||
    $old_email === "" ||
    $new_email === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Name and email are required."
    ]);

    exit;
}


/* CHECK NEW EMAIL */

if ($old_email !== $new_email) {

    $checkSql =
        "SELECT id
         FROM users
         WHERE email = ?";

    $checkStmt =
        $conn->prepare($checkSql);

    if (!$checkStmt) {

        echo json_encode([
            "success" => false,
            "message" => "Email check failed."
        ]);

        exit;
    }

    $checkStmt->bind_param(
        "s",
        $new_email
    );

    $checkStmt->execute();

    $checkResult =
        $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {

        echo json_encode([
            "success" => false,
            "message" => "This email is already registered."
        ]);

        $checkStmt->close();
        $conn->close();

        exit;
    }

    $checkStmt->close();
}


/* START TRANSACTION */

$conn->begin_transaction();

try {


    /* UPDATE USERS */

    $sql =
        "UPDATE users
         SET name = ?, email = ?
         WHERE email = ?";

    $stmt =
        $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("User update failed.");
    }

    $stmt->bind_param(
        "sss",
        $name,
        $new_email,
        $old_email
    );

    if (!$stmt->execute()) {
        throw new Exception("User update failed.");
    }

    $stmt->close();


    /* UPDATE TRIPS */

    $sql =
        "UPDATE trips
         SET user_email = ?
         WHERE user_email = ?";

    $stmt =
        $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ss",
            $new_email,
            $old_email
        );

        $stmt->execute();

        $stmt->close();
    }


    /* UPDATE EXPENSES */

    $sql =
        "UPDATE expenses
         SET user_email = ?
         WHERE user_email = ?";

    $stmt =
        $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ss",
            $new_email,
            $old_email
        );

        $stmt->execute();

        $stmt->close();
    }


    /* UPDATE BUDGET ESTIMATES */

    $sql =
        "UPDATE budget_estimates
         SET user_email = ?
         WHERE user_email = ?";

    $stmt =
        $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ss",
            $new_email,
            $old_email
        );

        $stmt->execute();

        $stmt->close();
    }


    /* COMMIT */

    $conn->commit();


    echo json_encode([
        "success" => true,
        "message" => "Profile updated successfully."
    ]);

}
catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        "success" => false,
        "message" => "Profile update failed."
    ]);
}


$conn->close();

?>