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
    $sqlForm = "SELECT firstname, lastname, address, city, state, zip, layout FROM form_entries WHERE id = $id";
    $resultForm = mysqli_query($conn, $sqlForm);
    // Check if id has data
    if ($resultForm && mysqli_num_rows($resultForm) > 0) {
        $form = mysqli_fetch_assoc($resultForm);
        $GLOBALS['header1'] = $form['address'];
        $GLOBALS['header2'] = $form['city'] . " " . $form['state'];
        // Check if layout exists
        if (is_null($form['layout'])) {
            $layout_path = "../uploads/layouts/default.jpg";
        } else {
            $layout_path = "../uploads/layouts/" . $form['layout'];
        }
    } else {
        // error: no row exists with that id
        header('Location: ../appMenu.php');
    }

    // Get icon data
    $sqlIcons = "SELECT type, picture, notes, x_pos, y_pos FROM icons WHERE assignmentID = $id";
    $resultIcons = mysqli_query($conn, $sqlIcons);
    $icons = [];
    if ($resultIcons && mysqli_num_rows($resultIcons) > 0) {
        while($iconRow = mysqli_fetch_array($resultIcons))
        {
            $icons[] = $iconRow;
        }
    } else {
        // error: no icons
    }
} else {
    // error: invalid id
    header('Location: ../appMenu.php');
}

class PDF extends FPDF {

    // Page header
    function Header() {

        // Add logo to page
        $this->Image('../assets/hfh_Logo_Black.png',10,8,33);

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

    // Icon
    function DrawIcon($w, $h, $txt='',) {
        $this->Image('../images/alert-sever-icon.png', $w-1, $h-1, 7, 7);
        $this->Rect($w+6, $h+5, 2+strlen($txt)*2.3, -5, 'F');
        $this->Text($w+7, $h+4, $txt);
    }
}

// Instantiation of FPDF class
$pdf = new PDF();

// Define alias for number of pages
$pdf->AliasNbPages();
$pdf->SetFont('Times','',14);

// page 1
$pdf->AddPage();

// Home Layout
//list($x1, $y1) = getimagesize($layout_path);
$x1 = 1600;
$y1 = 1400;
// TEMP ^^
$scalar = 180 / $x1;
$originX = $pdf->GetX();
$originY = $pdf->GetY();
$w = $x1 * $scalar;
$h = $y1 * $scalar;
$pdf->Cell($w + $originX, $h + $originY - 20, "", 0, 1, 'C',$pdf->Image($layout_path,$originX,$originY,$w,0));

// Overlay Icons
$pdf->SetFillColor(255, 255, 255);
$iconNum = 0;
foreach($icons as $icon) {
    $row = array($icon['x_pos'], $icon['y_pos'], ++$iconNum);
    $pdf->DrawIcon($originX + ($row[0]-540) * 1.9 * $scalar, $originY + ($row[1]-375) * 1.9 * $scalar, $row[2]);
}

// Home Info
$pdf->Cell(30,6,"Homeowner:",0,0,'R');
$pdf->Cell(75,6,$form['firstname']." ".$form['lastname'],0,1,'L');
$pdf->Cell(30,6,"Address:",0,0,'R');
$pdf->Cell(75,6,$form['address'],0,0,'L');
$pdf->Cell(20,6,"State:",0,0,'R');
$pdf->Cell(20,6,$form['state'],0,1,'L');
$pdf->Cell(30,6,"City:",0,0,'R');
$pdf->Cell(75,6,$form['city'],0,0,'L');
$pdf->Cell(20,6,"Zip:",0,0,'R');
$pdf->Cell(20,6,$form['zip'],0,1,'L');
$pdf->Ln();

// Icon Table Header
$header = array('Number', 'Type', 'Notes');
$colWidths = array(20, 20, 150);
$iconNum = 0;
foreach($header as $col)
    $pdf->Cell($colWidths[$iconNum++],7,$col,1);
$pdf->Ln();
// Table Data
$iconNum = 0;
foreach($icons as $icon) {
    $row = array(++$iconNum, $icon['type'], $icon['picture'], $icon['notes']);
    $pdf->Cell($colWidths[0],6,$row[0],1);
    $pdf->Cell($colWidths[1],6,$row[1],1);
    $pdf->MultiCell($colWidths[2],6,$row[3],1);
    //$pdf->Ln();
    // Image
    if ($row[2] != '' && !is_null($row[2]) ) {
        $imagePath = "../uploads/photos/" . $row[2];
        list($img_x1, $img_y1) = getimagesize($imagePath);
        $scalar = 190 / $img_x1;
        $img_w = $img_x1 * $scalar;
        $img_h = $img_y1 * $scalar;
        if ($pdf->GetY() + $img_h > 200) {
            $pdf->AddPage();
        }
        $pdf->Cell(190,$img_h+1,"",1,0,'C',$pdf->Image($imagePath,$pdf->GetX()+1,$pdf->GetY()+1,188,0));
        $pdf->Ln();
    }
}

$pdf->Output();

?>
