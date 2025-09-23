<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Tasca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-96 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Afegir una Tasca</h2>
        <form method="POST" action="create_task.php">
            <input type="text" name="title" placeholder="Títol de la tasca" class="w-full p-2 mb-3 border rounded" required>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Guardar</button>
        </form>
        <a href="tasks.php" class="block mt-4 text-blue-500 text-center">Tornar a la llista</a>
    </div>
</body>
</html>
