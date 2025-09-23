<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Meues Tasques</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-2xl mx-auto p-6 bg-white mt-10 shadow-md rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Les Meues Tasques</h2>
        
        <form method="POST" action="create_task.php" class="mb-4 flex">
            <input type="text" name="title" placeholder="Nova tasca..." class="flex-1 p-2 border rounded" required>
            <button type="submit" class="ml-2 bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Afegir</button>
        </form>

        <ul class="divide-y divide-gray-200">
             // Visualitzar les tasques i poder esborrar
        </ul>

        <a href="logout.php" class="block mt-4 text-red-500 text-center">Tancar Sessió</a>
    </div>
</body>
</html>
