<?php
require('fpdf.php'); // Include FPDF library

// Check if cart_data is set
if (isset($_POST['cart_data'])) {
    $cart_data = json_decode($_POST['cart_data'], true);

    // Initialize PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Set background color to pastel lavender
    $pdf->SetFillColor(230, 230, 250);
    $pdf->Rect(0, 0, 210, 297, 'F');

    // Add a logo
    $pdf->Image('logo.png', 10, 10, 30); // Ensure 'logo.png' is in the same directory
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 40, 'Shopping Cart Invoice', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Add Order ID
    $order_id = uniqid('Order-');
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, 'Order ID: ' . $order_id, 0, 1, 'C');
    $pdf->Ln(5);

    // Add table headers with background color
    $pdf->SetFillColor(230, 230, 230);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(70, 10, 'Product Name', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Quantity', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Price (Rs.)', 1, 1, 'C', true);

    // Add table content
    $pdf->SetFont('Arial', '', 12);
    $total = 0;
    foreach ($cart_data as $item) {
        $pdf->Cell(70, 10, $item['name'], 1, 0, 'C');
        $pdf->Cell(40, 10, $item['quantity'], 1, 0, 'C');
        $pdf->Cell(40, 10, number_format($item['price'], 2), 1, 1, 'C');
        $total += $item['quantity'] * $item['price'];
    }

    // Add total
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(110, 10, 'Total', 1, 0, 'C', true);
    $pdf->Cell(40, 10, number_format($total, 2), 1, 1, 'C');
    $pdf->Ln(10);

    // Add thank you message
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, 'Thank you for your purchase!', 0, 1, 'C');

    // Output PDF
    $pdf->Output('D', 'Cart_Invoice.pdf');
} else {
    echo "No cart data received.";
}
?>
