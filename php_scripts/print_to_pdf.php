<?php
// NOTE: any other output, INCLUDING ECHO, will break this

// Changed locations to work in php_scripts folder
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../index.php');
    exit();
}
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../index.php');
}

require('../fpdf/fpdf.php');
require_once "./db.php";

// Check if id exists
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT address, city, state, layout FROM form_entries WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    // Check if id has data
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $GLOBALS['header1'] = $row['address'];
        $GLOBALS['header2'] = $row['city'] . " " . $row['state'];
        // Check if layout exists
        if (is_null($row['layout'])) {
            $layout_path = "../uploads/layouts/default.jpg";
        } else {
            $layout_path = "../uploads/layouts/" . $row['layout'];
        }
    } else {
        // error: no row exists with that id
        header('Location: ../appMenu.php');
    }
} else {
    // error: invalid id
    header('Location: ../appMenu.php');
}

class PDF extends FPDF {

    // Page header
    function Header() {

        // Add logo to page
        $this->Image('../assets/Habitat_for_humanity_logo.png',10,8,33);

        // Set font family to Arial bold
        $this->SetFont('Arial','B',20);

        // Move to the right
        $this->Cell(40);

        // Header
        $this->Cell(0,10,$GLOBALS['header1'],0,1);
        $this->Cell(40);
        $this->Cell(0,10,$GLOBALS['header2'],0,0);

        // Line break
        $this->Ln(20);
    }

    // Page footer
    function Footer() {

        // Position at 1.5 cm from bottom
        $this->SetY(-15);

        // Arial italic 8
        $this->SetFont('Arial','I',8);

        // Page number
        $this->Cell(0,10,'Page ' .
            $this->PageNo() . '/{nb}',0,0,'C');
    }
}

// Instantiation of FPDF class
$pdf = new PDF();

// Define alias for number of pages
$pdf->AliasNbPages();
$pdf->SetFont('Times','',14);

//page 1
$pdf->AddPage();
list($x1, $y1) = getimagesize($layout_path);
$scalar = 160 / $x1;
$h = $y1 * $scalar + $pdf->GetY();
$pdf->Cell(0, $h, "", 0, 1, 'C',$pdf->Image($layout_path,$pdf->GetX(),$pdf->GetY(),0,160));

for($i = 1; $i <= 10; $i++)
    $pdf->Cell(0, 10, 'line number '
            . $i, 0, 1);
$pdf->Output();

?>
