<?php

require_once './fpdf/fpdf.php';

class PDF extends FPDF
{
    //Cabecera de página
    function Header()
    {
        //Logo
        $this->Image("./assets/logo_bar.jpg" , 10 ,8, 35 , 38 , "JPG" );
        //Arial bold 15
        $this->SetFont('Arial','B',15);
        //Movernos a la derecha
        $this->Cell(70);
        //Título
        $this->Cell(60,10,'TodoRojo Bar',1,0,'C');
        //Salto de línea
        $this->Ln(50);
        

    }

    //Pie de página
    function Footer()
    {
        $this->SetY(-10);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'     Uliczki Micaela',0,0,'C');
    }

    function Body($contenido)
    {

        $this->Cell(10);
        $this->SetFont('arial', 'B', 12);
        $this->MultiCell(0, 5, $contenido);
        $this->Ln();
    }
}

