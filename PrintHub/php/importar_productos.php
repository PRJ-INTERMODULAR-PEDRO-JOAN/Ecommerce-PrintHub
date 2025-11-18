<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// --- Configuración de rutas ---
$uploadDir = '../uploads/';
$jsonFilePath = '../data/products.json'; 

$response = [
    'status' => 'error',
    'message' => 'Error desconocido.',
    'imported_productes' => 0,
    'imported_impresoras' => 0,
    'errors' => []
];

if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] === UPLOAD_ERR_OK) {
    
    $fileTmpPath = $_FILES['excelFile']['tmp_name'];
    $fileName = basename($_FILES['excelFile']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $allowedExtensions = ['xlsx', 'xls', 'csv'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        $response['message'] = 'Error: Tipo de archivo no permitido.';
    } else {
        $uploadedFilePath = $uploadDir . uniqid('import_') . '.' . $fileExtension;
        
        if (move_uploaded_file($fileTmpPath, $uploadedFilePath)) {
            try {
                $spreadsheet = IOFactory::load($uploadedFilePath);

                // Inicializamos arrays
                $productes = [];
                $impresoras = [];
                $errorsList = [];
                $idCounter = 1;

                // --- Procesar Hoja1: Products ---
                if ($spreadsheet->sheetNameExists('Hoja1')) {
                    $sheet1 = $spreadsheet->getSheetByName('Hoja1')->toArray(null, true, true, true);
                    $header1 = array_shift($sheet1);

                    foreach ($sheet1 as $rowIndex => $row) {
                        if (empty($row['A']) && empty($row['B'])) continue;
                        $sku = $row['A'];
                        $nom = $row['B'];
                        $descripcio = $row['C'];
                        $img = $row['D'];
                        $preu = $row['E'];
                        $estoc = $row['F'];

                        if (!is_numeric($preu) || $preu < 0) {
                            $errorsList[] = "Hoja1 Fila " . ($rowIndex+1) . " (SKU: $sku): Preu no válido ($preu).";
                            continue;
                        }
                        if (!is_numeric($estoc) || $estoc < 0) {
                            $errorsList[] = "Hoja1 Fila " . ($rowIndex+1) . " (SKU: $sku): Estoc no válido ($estoc).";
                            continue;
                        }
                        if (empty($nom)) {
                            $errorsList[] = "Hoja1 Fila " . ($rowIndex+1) . " (SKU: $sku): Nom vacío.";
                            continue;
                        }

                        $productes[] = [
                            'id' => $idCounter++,
                            'sku' => $sku,
                            'nom' => $nom,
                            'descripcio' => $descripcio,
                            'img' => $img,
                            'preu' => (float)$preu,
                            'estoc' => (int)$estoc
                        ];
                    }
                }

                // --- Procesar Hoja2: Impresoras ---
                if ($spreadsheet->sheetNameExists('Hoja2')) {
                    $sheet2 = $spreadsheet->getSheetByName('Hoja2')->toArray(null, true, true, true);
                    $header2 = array_shift($sheet2);

                    foreach ($sheet2 as $rowIndex => $row) {
                        if (empty($row['A']) && empty($row['B'])) continue;
                        $sku = $row['A'];
                        $nom = $row['B'];
                        $descripcio = $row['C'];
                        $img = $row['D'];
                        $preu = $row['E'];
                        $estoc = $row['F'];

                        if (!is_numeric($preu) || $preu < 0) {
                            $errorsList[] = "Hoja2 Fila " . ($rowIndex+1) . " (SKU: $sku): Preu no válido ($preu).";
                            continue;
                        }
                        if (!is_numeric($estoc) || $estoc < 0) {
                            $errorsList[] = "Hoja2 Fila " . ($rowIndex+1) . " (SKU: $sku): Estoc no válido ($estoc).";
                            continue;
                        }
                        if (empty($nom)) {
                            $errorsList[] = "Hoja2 Fila " . ($rowIndex+1) . " (SKU: $sku): Nom vacío.";
                            continue;
                        }

                        $impresoras[] = [
                            'id' => $idCounter++,
                            'sku' => $sku,
                            'nom' => $nom,
                            'descripcio' => $descripcio,
                            'img' => $img,
                            'preu' => (float)$preu,
                            'estoc' => (int)$estoc
                        ];
                    }
                }

                $jsonData = [
                    'productes' => $productes,
                    'impresoras' => $impresoras
                ];

                if (file_put_contents($jsonFilePath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
                    $response['status'] = 'success';
                    $response['message'] = '¡Importación completada!';
                    $response['imported_productes'] = count($productes);
                    $response['imported_impresoras'] = count($impresoras);
                    $response['errors'] = $errorsList;
                } else {
                    $response['message'] = 'No se pudo escribir en products.json.';
                }

            } catch (Exception $e) {
                $response['message'] = 'Error procesando Excel: ' . $e->getMessage();
            }

            unlink($uploadedFilePath);

        } else {
            $response['message'] = 'No se pudo mover el archivo subido.';
        }
    }
} else {
    $response['message'] = 'Error al subir el archivo.';
}

// Mostrar resultado
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado de la Importación</title>
    <style>
        body { font-family: Arial; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 90vh; }
        .container { background: #fff; padding: 30px; border-radius: 10px; width: 700px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .success { color: #28a745; } .error { color: #dc3545; }
        .errors-list { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px 15px; border-radius: 5px; max-height: 200px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Resultado de la Importación</h2>
        <?php if ($response['status'] === 'success'): ?>
            <h3 class="success"><?php echo htmlspecialchars($response['message']); ?></h3>
            <p>Productos importados: <?php echo $response['imported_productes']; ?></p>
            <p>Impresoras importadas: <?php echo $response['imported_impresoras']; ?></p>
        <?php else: ?>
            <h3 class="error"><?php echo htmlspecialchars($response['message']); ?></h3>
        <?php endif; ?>

        <?php if (!empty($response['errors'])): ?>
            <h4>Errores encontrados:</h4>
            <ul class="errors-list">
                <?php foreach ($response['errors'] as $error) echo "<li>".htmlspecialchars($error)."</li>"; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
