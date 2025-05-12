<?php
$hash_guardado = '$2y$10$NDPEB3XVPcWNybnJn9loKu3v2I6CuZxAXcvS7wFy/zx...'; // Copia la contraseña cifrada desde la BD
$contraseña_usuario = '246'; // Prueba con la contraseña real

if (password_verify($contraseña_usuario, $hash_guardado)) {
    echo "✅ Coincide";
} else {
    echo "❌ No coincide";
}
