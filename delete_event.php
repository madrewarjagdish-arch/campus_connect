<?php
include 'db.php';

$id = $_GET['id'];

$sql = "DELETE FROM events WHERE id = $id";

if ($conn->query($sql)) {
    header("Location: view_events.php");
    exit();
} else {
    echo "Error deleting event: " . $conn->error;
}
?>