<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-96 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Registra't</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <input type="text" name="username" placeholder="Usuari" class="w-full p-2 mb-3 border rounded" required>
            <input type="password" name="password" placeholder="Contrasenya" class="w-full p-2 mb-3 border rounded" required>
            <button type="submit" class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600">Registrar</button>
        </form>
        <p class="mt-4 text-sm text-center">
            Ja tens compte? <a href="login.php" class="text-blue-500">Inicia sessió</a>
        </p>
    </div>
</body>
</html>
