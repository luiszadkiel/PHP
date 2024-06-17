<!-- login.php -->
<?php
session_start();

// Credenciales de los usuarios (simuladas para este ejemplo)
$users = [
    'admin' => '$2y$10$R1W2Q.GUcY00EGo1JEPKt.3Zly5Inpyv5I4pPB3FGsA54mI8eEF2O', // Contraseña: admin123
    'invitado' => '$2y$10$UdF3S/MYB9FcN9Q4Xvdb6.2v7av30NfDPBk.M.mTyG.AkTlyI8u1O' // Contraseña: guest123
];

// Verificar si se enviaron datos de login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar las credenciales
   // if (array_key_exists($username, $users) && password_verify($password, $users[$username])) {
        // Autenticación exitosa, establecer sesión
   //     $_SESSION['username'] = $username;

        // Redirigir según el rol
        if ($username === 'admin' && $password === 'admin123' ) {
            header('Location: menu.html');
        }
        else if ($username === 'invitado' && $password === 'guest123' ) {
            header('Location: menu2.html');
        } else {
            header('Location: index.php'); // Redirigir a página de login 
        }
        exit();
    } else {
    //    echo "Credenciales incorrectas.";
   // }
}
