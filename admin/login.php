<?php
require_once 'auth.php';
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usuario = $_POST['usuario'] ?? '';
  $clave   = $_POST['clave'] ?? '';
  if ($usuario === 'admin' && $clave === 'admin123') {
    $_SESSION['usuario_admin'] = 'admin'; header('Location: panel.php'); exit;
  } else { $mensaje = 'Usuario o contraseña incorrectos'; }
}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login administración</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50"><main class="max-w-md mx-auto mt-16 bg-white border border-slate-200 p-6 rounded-2xl">
<h1 class="text-xl font-bold mb-4">Administración</h1>
<?php if ($mensaje): ?><section class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-3 mb-3"><?php echo htmlspecialchars($mensaje); ?></section><?php endif; ?>
<form method="post" class="space-y-3">
  <section><label class="font-medium" for="usuario">Usuario</label><input class="w-full border rounded-lg px-3 py-2" name="usuario" required></section>
  <section><label class="font-medium" for="clave">Contraseña</label><input class="w-full border rounded-lg px-3 py-2" type="password" name="clave" required></section>
  <button class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">Ingresar</button>
</form></main></body></html>
