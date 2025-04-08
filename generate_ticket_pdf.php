<?php
require_once 'includes/db.php';
require('fpdf.php'); // Include the FPDF library

if (isset($_GET['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

    // Fetch ticket details
    $result = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = '$order_id'");
    $ticket = mysqli_fetch_assoc($result);

    if ($ticket) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        $pdf->Cell(40, 10, 'Ticket for Order ID: ' . $ticket['order_id']);
        $pdf->Ln();
        $pdf->Cell(40, 10, 'Payment Status: ' . $ticket['payment_status']);
        $pdf->Output();
    }
}
?>
