<?php
require_once '../includes/functions.php';

// Keamanan
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $status = $_POST['status'] ?? null;

    // Daftar status yang valid untuk keamanan
    $allowed_statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    if (!empty($order_id) && !empty($status) && in_array($status, $allowed_statuses)) {
        global $conn;
        $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $status, $order_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Arahkan kembali ke halaman manajemen pesanan
header('Location: orders.php');
exit();
?>
