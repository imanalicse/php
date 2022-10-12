<?php
namespace App\PDF;

require '../vendor/autoload.php';

$pdf = new \Clegginabox\PDFMerger\PDFMerger;

$pdf->addPDF('pdf_files/1.pdf', 'all');
$pdf->addPDF('pdf_files/2.pdf', 'all');
// $pdf->addPDF('samplepdfs/three.pdf', 'all');

//You can optionally specify a different orientation for each PDF


$pdf->merge('file', 'pdf_files/merge.pdf', 'P');

// REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
// Last parameter is for orientation (P for protrait, L for Landscape).
// This will be used for every PDF that doesn't have an orientation specified
