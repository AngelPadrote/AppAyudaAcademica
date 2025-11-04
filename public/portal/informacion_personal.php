<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php'); 
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal Académico - Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body { font-family: 'Inter', sans-serif; }

.nav-item { transition: all 0.3s ease; position: relative; cursor: pointer; }
.nav-item:hover { transform: translateY(-2px); }
.nav-item.active { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }
.nav-item.active::before {
    content: '';
    position: absolute; bottom: -2px; left: 50%;
    transform: translateX(-50%);
    width: 0; height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid #f8fafc;
}

.content-section { display: none; }
.content-section.active { display: block; animation: fadeIn 0.4s ease-in; }

@keyframes fadeIn { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

.subject-card { transition: all 0.3s ease; cursor: pointer; }
.subject-card:hover { transform: scale(1.02); box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); }

.dropdown { display: none; }
.dropdown.show { display: block; animation: dropdownFade 0.2s ease-out; }
@keyframes dropdownFade { from {opacity:0; transform:translateY(-10px);} to {opacity:1; transform:translateY(0);} }
</style>
</head>
<body class="bg-gray-50 min-h-screen">

<header class="bg-white shadow-sm border-b border-gray-200 p-4 flex justify-between items-center">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-blue-500 text-white rounded-lg flex items-center justify-center">🎓</div>
        <h1 class="text-xl font-semibold text-gray-900">Portal Académico</h1>
    </div>
    <div class="relative">
        <button onclick="toggleUserMenu()" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-100">
            <span><?=htmlspecialchars($user['nombre'] ?? $user['numero'])?></span>
            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
        <div id="user-dropdown" class="dropdown absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Mi Perfil</a>
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Configuración</a>
            <hr class="my-2">
            <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50">Cerrar Sesión</a>
        </div>
    </div>
</header>

<!-- Navigation -->
<nav class="bg-white shadow-sm p-4 flex space-x-2">
    <div class="nav-item active px-4 py-2 rounded" onclick="showSection('malla')">Malla Curricular</div>
    <div class="nav-item px-4 py-2 rounded" onclick="showSection('recomendaciones')">Recomendaciones</div>
    <div class="nav-item px-4 py-2 rounded" onclick="showSection('guia')">Guía Académica</div>
    <div class="nav-item px-4 py-2 rounded" onclick="showSection('informacion')">Información</div>
</nav>

<!-- Content Sections -->
<main class="max-w-7xl mx-auto p-4 space-y-6">
    <!-- Malla Curricular -->
    <section id="malla" class="content-section active">
        <h2 class="text-2xl font-bold mb-4">Malla Curricular</h2>
        <p>Contenido de la malla curricular aquí (accesos en proceso).</p>
    </section>

    <!-- Recomendaciones -->
    <section id="recomendaciones" class="content-section">
        <h2 class="text-2xl font-bold mb-4">Recomendaciones de Materias</h2>
        <p>Contenido de recomendaciones de materias aquí.</p>
    </section>

    <!-- Guía Académica -->
    <section id="guia" class="content-section">
        <h2 class="text-2xl font-bold mb-4">Guía Académica</h2>
        <p>Contenido de la guía académica aquí.</p>
    </section>

    <!-- Información -->
    <section id="informacion" class="content-section">
        <h2 class="text-2xl font-bold mb-4">Información Personal</h2>
        <div class="grid md:grid-cols-2 gap-6 bg-white p-6 rounded shadow-sm border border-gray-200">
            <div>
                <p><strong>Nombre:</strong> <?=htmlspecialchars($user['nombre'])?></p>
                <p><strong>Número:</strong> <?=htmlspecialchars($user['numero'])?></p>
                <p><strong>Carrera:</strong> <?=htmlspecialchars($user['carrera'])?></p>
            </div>
            <div>
                <p><strong>Correo Institucional:</strong> <?=htmlspecialchars($user['correoInstitucional'])?></p>
                <p><strong>Correo Personal:</strong> <?=htmlspecialchars($user['email'])?></p>
                <p><strong>Teléfono:</strong> +52 <?=htmlspecialchars($user['telefono'])?></p>
                <p><strong>Estado Académico:</strong> <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm"><?=htmlspecialchars($user['estado'])?></span></p>
            </div>
        </div>
    </section>
</main>

<script>
function showSection(id){
    document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
    event.currentTarget.classList.add('active');
}

function toggleUserMenu(){
    document.getElementById('user-dropdown').classList.toggle('show');
}
</script>
</body>
</html>
