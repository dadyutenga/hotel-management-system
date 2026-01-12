<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: hotels.php');
    exit;
}

$db = Database::getInstance();
$conn = $db->getConnection();

try {
    // Generate requisition number
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM booking_requisitions 
        WHERE YEAR(created_at) = YEAR(NOW())
    ");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] + 1;
    $requisitionNumber = 'BRQ-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

    // Insert booking requisition
    $stmt = $conn->prepare("
        INSERT INTO booking_requisitions (
            requisition_id, tenant_id, requisition_number, full_name, email, phone,
            check_in_date, check_out_date, adults, children, rooms, booking_type,
            special_requests, status, created_at
        ) VALUES (
            UUID(), :tenant_id, :requisition_number, :full_name, :email, :phone,
            :check_in, :check_out, :adults, :children, :rooms, :booking_type,
            :special_requests, 'pending', NOW()
        )
    ");
    
    $stmt->execute([
        'tenant_id' => $_POST['hotel_id'],
        'requisition_number' => $requisitionNumber,
        'full_name' => trim($_POST['full_name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'check_in' => $_POST['check_in'],
        'check_out' => $_POST['check_out'],
        'adults' => $_POST['adults'],
        'children' => $_POST['children'] ?? 0,
        'rooms' => $_POST['rooms'],
        'booking_type' => $_POST['booking_type'],
        'special_requests' => trim($_POST['special_requests'] ?? '')
    ]);

    // Redirect to success page
    header('Location: requisition-success.php?ref=' . $requisitionNumber . '&hotel=' . urlencode($_POST['hotel_name']));
    exit;

} catch (Exception $e) {
    // Redirect to error page
    header('Location: hotel-details.php?id=' . $_POST['hotel_id'] . '&error=submission_failed');
    exit;
}
