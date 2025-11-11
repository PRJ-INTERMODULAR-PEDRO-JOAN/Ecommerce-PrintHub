<?php
// Flux 3: Incluir autoloader de Composer
// La ruta es '../vendor/autoload.php' porque este script está en /php/
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// --- Configuración de rutas ---
// Ruta donde se guardará el Excel subido (relativa a este script)
$uploadDir = '../uploads/';
// Ruta donde se guardará el JSON final (relativa a este script)
// Esta es la ruta al archivo que vigila el JSON Server
$jsonFilePath = '../data/products.json'; 

// Inicializar la respuesta que daremos al usuario
$response = [
    'status' => 'error',
    'message' => 'Error desconocido.',
    'imported' => 0,
    'errors' => []
];

// --- Flux 2: Rebre i desar el fitxer ---
if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] === UPLOAD_ERR_OK) {
    
    $fileTmpPath = $_FILES['excelFile']['tmp_name'];
    $fileName = basename($_FILES['excelFile']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Validar extensión
    $allowedExtensions = ['xlsx', 'xls', 'csv'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        $response['message'] = 'Error: Tipo de archivo no permitido. Sube solo .xlsx, .xls o .csv.';
    } else {
        // Mover el archivo subido a la carpeta /uploads/
        $uploadedFilePath = $uploadDir . uniqid('import_') . '.' . $fileExtension;
        
        if (move_uploaded_file($fileTmpPath, $uploadedFilePath)) {
            
            // --- Flux 3: Llegir el contingut de l’Excel ---
            try {
                $spreadsheet = IOFactory::load($uploadedFilePath);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                // --- Flux 4 & 5: Validar i Generar Array ---
                $productes = [];
                $errorsList = [];
                $importedCount = 0;
                
                // Quitar la fila de cabecera (títulos)
                $header = array_shift($sheetData); 
                
                // *** ATENCIÓN ***
                // Asumimos el orden de columnas del Excel:
                // Columna A = SKU (ej: FIG-LNK-01)
                // Columna B = Nom (ej: Figura Link)
                // Columna C = Descripcio (ej: Figura de resina...)
                // Columna D = Img (ej: public/FiguraLink.png)
                // Columna E = Preu (ej: 29.99)
                // Columna F = Estoc (ej: 10)

                $idCounter = 1; // Para generar IDs únicos
                foreach ($sheetData as $rowIndex => $row) {
                    
                    // Saltamos filas vacías (si no tiene ni SKU ni Nombre)
                    if (empty($row['A']) && empty($row['B'])) {
                        continue;
                    }

                    $sku = $row['A'];
                    $nom = $row['B'];
                    $descripcio = $row['C'];
                    $img = $row['D']; // La ruta de la imagen tal como la tienes en /public/
                    $preu = $row['E'];
                    $estoc = $row['F'];

                    // Flux 4: Validación de datos
                    if (!is_numeric($preu) || $preu < 0) {
                        $errorsList[] = "Fila " . ($rowIndex) . " (SKU: $sku): El 'Preu' no es válido ($preu).";
                        continue;
                    }
                    if (!is_numeric($estoc) || $estoc < 0) {
                        $errorsList[] = "Fila " . ($rowIndex) . " (SKU: $sku): El 'Estoc' no es válido ($estoc).";
                        continue;
                    }
                    if (empty($nom)) {
                         $errorsList[] = "Fila " . ($rowIndex) . " (SKU: $sku): El 'Nom' no puede estar vacío.";
                        continue;
                    }
                    
                    // Si todo OK, añadimos al array de productos
                    $productes[] = [
                        'id' => $idCounter++,
                        'sku' => $sku,
                        'nom' => $nom,
                        'descripcio' => $descripcio,
                        'img' => $img, 
                        'preu' => (float)$preu,
                        'estoc' => (int)$estoc
                    ];
                    $importedCount++;
                }

                // --- Flux 5: Generar l’arxiu JSON ---
                // Formateamos el array final para que coincida con la estructura de JSON Server
                $jsonData = ['productes' => $productes];
                
                // Guardamos el JSON en /data/products.json
                // JSON_PRETTY_PRINT = para que sea legible
                // JSON_UNESCAPED_SLASHES = para que no ponga '\' en las rutas de imagen
                if (file_put_contents($jsonFilePath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
                    $response['status'] = 'success';
                    $response['message'] = '¡Importación completada!';
                    $response['imported'] = $importedCount;
                    $response['errors'] = $errorsList;
                } else {
                    $response['message'] = 'Error: No se pudo escribir en el archivo products.json. Verifica los permisos de la carpeta /data.';
                }

            } catch (Exception $e) {
                $response['message'] = 'Error procesando el archivo Excel: ' . $e->getMessage();
            }
            
            // Borrar el archivo Excel subido para limpiar
            unlink($uploadedFilePath);

        } else {
            $response['message'] = 'Error: No se pudo mover el archivo subido a "uploads". Verifica los permisos de la carpeta.';
        }
    }
} else {
    $response['message'] = 'Error al subir el archivo (código: ' . ($_FILES['excelFile']['error'] ?? 'N/A') . ')';
}

// --- Flux 7: Mostrar resultat final ---
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado de la Importación</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 90vh; }
        .result-container { background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 700px; width: 100%; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .summary { font-size: 1.1em; margin-bottom: 20px; }
        .errors-list { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px 10px 10px 30px; border-radius: 5px; max-height: 200px; overflow-y: auto; }
        .errors-list li { margin-bottom: 5px; }
        a { color: #007bff; text-decoration: none; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="result-container">
        <h2>Resultado de la Importación</h2>

        <?php if ($response['status'] === 'success'): ?>
            <h3 class="success"><?php echo htmlspecialchars($response['message']); ?></h3>
            <p class="summary"><strong>Productos importados correctamente:</strong> <?php echo $response['imported']; ?></p>
        <?php else: ?>
            <h3 class="error"><?php echo htmlspecialchars($response['message']); ?></h3>
        <?php endif; ?>

        <?php if (!empty($response['errors'])): ?>
            <h4>Se encontraron <?php echo count($response['errors']); ?> filas con errores (fueron ignoradas):</h4>
            <ul class="errors-list">
                <?php foreach ($response['errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <hr style="margin: 20px 0;">
        <p><a href="../admin_importar.html">Importar otro archivo</a></p>
        <p><a href="http://localhost:3000/productes" target="_blank">Ver API de Productos (JSON Server) &rarr;</a></p>
    </div>
</body>
</html>