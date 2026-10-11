<?php

session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/registration/db.php';


// Check student login
if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        'count' => 0
    ]);

    exit;
}


$user_id = intval($_SESSION['user_id']);


// Count unread notifications
$sql = "
    SELECT COUNT(*) AS unread_count
    FROM notifications n
    LEFT JOIN notification_reads nr
        ON n.id = nr.notification_id
        AND nr.user_id = ?

    WHERE n.status = 'Active'

    AND (
        n.target_user_id IS NULL
        OR n.target_user_id = ?
    )

    AND nr.id IS NULL
";


$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    echo json_encode([
        'count' => 0
    ]);

    exit;
}


mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $user_id,
    $user_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$row = mysqli_fetch_assoc($result);


$count = intval($row['unread_count'] ?? 0);


echo json_encode([
    'count' => $count
]);

?>