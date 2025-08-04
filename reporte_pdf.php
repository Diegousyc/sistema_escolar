<link rel="stylesheet" href="style.css">
<?php
require("fpdf/fpdf.php"); // ✅ Debes tener la carpeta "fpdf" subida en tu hosting
include "conexion.php";

$alumno_id = $_GET['alumno_id'];

// ✅ Obtener datos del alumno
$alumno = $conexion->query("
    SELECT a.nombre AS alumno, g.nombre AS grupo, c.nombre AS carrera
    FROM alumnos a
    JOIN grupos g ON a.grupo_id = g.id
    JOIN carreras c ON g.carrera_id = c.id
    WHERE a.id = $alumno_id
")->fetch_assoc();

// ✅ Obtener calificaciones
$materias = $conexion->query("
    SELECT m.nombre AS materia, cal.c1, cal.c2, cal.c3, cal.promedio
    FROM calificaciones cal
    JOIN materias m ON cal.materia_id = m.id
    WHERE cal.alumno_id = $alumno_id
");

// ✅ Calcular promedio general
$total = 0;
$count = 0;
while($m = $materias->fetch_assoc()){
    $total += $m['promedio'];
    $count++;
    $materias_array[] = $m;
}
$promedio_general = $count > 0 ? round($total / $count, 1) : 0;
$liberado = $promedio_general >= 6 ? "✅ LIBERADO" : "❌ NO LIBERADO";

// ✅ Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,"REPORTE DE CALIFICACIONES",0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,"Alumno: ".$alumno['alumno'],0,1);
$pdf->Cell(0,8,"Carrera: ".$alumno['carrera'],0,1);
$pdf->Cell(0,8,"Grupo: ".$alumno['grupo'],0,1);
$pdf->Ln(5);

// ✅ Tabla de materias
$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,8,"Materia",1,0,'C');
$pdf->Cell(30,8,"C1",1,0,'C');
$pdf->Cell(30,8,"C2",1,0,'C');
$pdf->Cell(30,8,"C3",1,0,'C');
$pdf->Cell(30,8,"Promedio",1,1,'C');

$pdf->SetFont('Arial','',12);

foreach($materias_array as $m){
    $pdf->Cell(60,8,$m['materia'],1,0);
    $pdf->Cell(30,8,$m['c1'],1,0,'C');
    $pdf->Cell(30,8,$m['c2'],1,0,'C');
    $pdf->Cell(30,8,$m['c3'],1,0,'C');
    $pdf->Cell(30,8,$m['promedio'],1,1,'C');
}

// ✅ Promedio general y liberación
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,"Promedio General: $promedio_general",0,1);
$pdf->Cell(0,8,"Estado: $liberado",0,1);

$pdf->Output();
?>
