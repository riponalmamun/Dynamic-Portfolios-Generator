<?php
require('fpdf/fpdf.php');
include('database.php');

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM portfolios WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$portfolio = mysqli_fetch_assoc($result);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'Portfolio of ' . $portfolio['full_name']);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, 'Contact Info: ' . $portfolio['contact_info']);
$pdf->Ln();
$pdf->Cell(40, 10, 'Bio: ' . $portfolio['bio']);
$pdf->Ln();

// More sections for skills, experience, and projects...
$pdf->Output('D', 'portfolio_' . $portfolio['full_name'] . '.pdf');
?>
