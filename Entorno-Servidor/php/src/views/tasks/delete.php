<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esborrar Tasca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-96 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4 text-center">Vols esborrar aquesta tasca?</h2>
        <form method="POST" action="delete_task.php">
            <input type="hidden" name="task_id" value="<?= $_GET['id'] ?>">
            <button type="submit" class="w-full bg-red-500 text-white p-2 rounded hover:bg-red-600">Sí, Esborrar</button>
        </form>
        <a href="tasks.php" class="block mt-4 text-blue-500 text-center">Cancel·lar</a>
    </div>
</body>
</html>
