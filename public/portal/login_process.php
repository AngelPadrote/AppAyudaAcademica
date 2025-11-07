<?php

session_start();
require 'db.php';

echo $numero = $_POST['numero'] ?? '';
echo $password = $_POST['password'] ?? '';

if ($numero === '' || $password === '') {
    header('Location: PreLogin.php?error=' . urlencode('Completa todos los campos.'));
    exit;
}

$estudiante = getEstudiante($numero);


if ($estudiante) {
    if ($password=== $estudiante['Password']) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'numero' => $estudiante['NumeroEstudiante'],
            'nombre' => $estudiante['Nombre'],
            'carrera' => $estudiante['Carrera'],
            'correoInstitucional' => $estudiante['InstitucionalEmail'],
            'email' => $estudiante['Email'],
            'telefono' => $estudiante['NumeroCelular'],
            'estado' => $estudiante['Estado']

        ];
        header('Location: informacion_personal.php');
        exit;
    } else {
        header('Location: login.php?error=' . urlencode('Usuario o contraseña incorrecta.'));
        exit;
    }
} else {
    header('Location: login.php?error=' . urlencode('Usuario no encontrado.'));
    exit;
}
