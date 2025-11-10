<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: admin.php');
    exit;
}
$admin = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Administrativo - Portal Académico</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
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
.dropdown { display: none; }
.dropdown.show { display: block; animation: dropdownFade 0.2s ease-out; }
@keyframes dropdownFade { from {opacity:0; transform:translateY(-10px);} to {opacity:1; transform:translateY(0);} }
</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-white shadow-sm border-b border-gray-200 p-4 flex justify-between items-center">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-bold">🛠️</div>
        <h1 class="text-xl font-semibold text-gray-900">Panel Administrativo</h1>
    </div>
    <div class="relative">
        <button onclick="toggleUserMenu()" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-100">
            <span><?=htmlspecialchars($admin['nombre'] ?? 'Administrador')?></span>
            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
        <div id="user-dropdown" class="dropdown absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Perfil</a>
            <hr class="my-2">
            <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50">Cerrar Sesión</a>
        </div>
    </div>
</header>

<!-- Navigation -->
<nav class="bg-white shadow-sm p-4 flex space-x-2">
    <div class="nav-item active px-4 py-2 rounded" onclick="showSection('contenido', event)">Subir Contenido</div>
    <div class="nav-item px-4 py-2 rounded" onclick="loadAlumnos(event)">Administrar Alumnos</div>
    <div class="nav-item px-4 py-2 rounded" onclick="loadMaterias(event)">Administrar Materias</div>
</nav>

<!-- Main Content -->
<main class="max-w-6xl mx-auto p-4 space-y-6">

    <!-- Subir Contenido -->
    <section id="contenido" class="content-section active">
        <h2 class="text-2xl font-bold mb-4">Subir Contenido por Materia</h2>
        <div class="bg-white p-6 rounded shadow-sm border border-gray-200">
            <form action="subir_contenido.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selecciona la materia</label>
                    <select name="materia" class="w-full border rounded p-2">
                        <option value="">-- Selecciona una materia --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título del contenido</label>
                    <input type="text" name="titulo" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Archivo</label>
                    <input type="file" name="archivo" class="w-full border rounded p-2" required>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Subir</button>
            </form>
        </div>
    </section>

    <!-- Administrar Alumnos -->
    <section id="alumnos" class="content-section">
        <h2 class="text-2xl font-bold mb-4">Administrar Alumnos</h2>
        <div class="bg-white p-6 rounded shadow-sm border border-gray-200">
            <p class="text-gray-600 mb-4">Aquí podrás ver, editar o eliminar alumnos registrados.</p>
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">#</th>
                        <th class="border p-2">Nombre</th>
                        <th class="border p-2">Número</th>
                        <th class="border p-2">Correo</th>
                        <th class="border p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-alumnos">
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">Sin datos cargados</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Administrar Materias -->
    <section id="materias" class="content-section">
        <h2 class="text-2xl font-bold mb-4">Administrar Materias</h2>
        <div class="bg-white p-6 rounded shadow-sm border border-gray-200">
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">ID</th>
                        <th class="border p-2">Materia</th>
                        <th class="border p-2">Clave</th>
                        <th class="border p-2">Carrera</th>
                        <th class="border p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-materias">
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">Haz clic en "Administrar Materias" para cargar datos.</td></tr>
                </tbody>
            </table>
        </div>
    </section>

</main>

<script>
const supabaseUrl = "https://vxorllitblvcfjtofwdl.supabase.co";
const supabaseKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InZ4b3JsbGl0Ymx2Y2ZqdG9md2RsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjA4ODg0NDYsImV4cCI6MjA3NjQ2NDQ0Nn0.5RztDY6R6szpUjIl6OB6fYZhXnTlvIikvNrKHcHXPhc";
const supabase = window.supabase.createClient(supabaseUrl, supabaseKey);

function showSection(id, event) {
    document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
    event.currentTarget.classList.add('active');
}

function toggleUserMenu() {
    document.getElementById('user-dropdown').classList.toggle('show');
}

async function loadMaterias(event) {
    showSection('materias', event);
    const tbody = document.getElementById('tabla-materias');
    tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-gray-500">Cargando materias...</td></tr>`;

    try {
    const { data: usuario, error: errorUsuario } = await supabase
        .from('estudiantes')
        .select('Carrera')
        .eq('NumeroEstudiante', '<?php echo $admin["numero"]; ?>')
        .single();

    if (errorUsuario) throw errorUsuario;

    const { data: materias, error: errorMaterias } = await supabase
        .from('Asignaturas')
        .select('*')
        .eq('Carrera', usuario.Carrera);

    if (errorMaterias) throw errorMaterias;

    if (!materias || materias.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="p-4 text-center text-gray-500">
                    No hay materias registradas.
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = materias.map((m) => `
        <tr class="hover:bg-gray-50">
            <td class="border p-2 text-center">${m.id}</td>
            <td class="border p-2">${m.Materia}</td>
            <td class="border p-2">${m.Clave}</td>
            <td class="border p-2">${m.Carrera}</td>
            <td class="border p-2 text-center space-x-2">
                <button class="text-blue-600 hover:underline" onclick="editarMateria(${m.id})">Editar</button>
                <button class="text-red-600 hover:underline" onclick="eliminarMateria(${m.id})">Eliminar</button>
            </td>
        </tr>
    `).join('');
} catch (err) {
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="p-4 text-center text-red-600">
                Error al cargar materias: ${err.message}
            </td>
        </tr>`;
}
}

async function loadAlumnos(event) {
    showSection('alumnos', event);
    const tbody = document.getElementById('tabla-alumnos');
    tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-gray-500">Cargando alumnos...</td></tr>`;

    try {

        const { data: alumnos, error } = await supabase
            .from('estudiantes')
            .select('*')
            .order('Nombre', { ascending: true });

        if (error) throw error;

        if (!alumnos || alumnos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        No hay alumnos registrados.
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = alumnos.map((a) => `
            <tr class="hover:bg-gray-50">
                <td class="border p-2 text-center">${a.id}</td>
                <td class="border p-2">${a.Nombre || 'Sin nombre'}</td>
                <td class="border p-2">${a.NumeroEstudiante || '—'}</td>
                <td class="border p-2">${a.Carrera || '—'}</td>
                <td class="border p-2 text-center space-x-2">
                    <button class="text-blue-600 hover:underline" onclick="editarAlumno(${a.id})">Editar</button>
                    <button class="text-red-600 hover:underline" onclick="eliminarAlumno(${a.id})">Eliminar</button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="p-4 text-center text-red-600">
                    Error al cargar alumnos: ${err.message}
                </td>
            </tr>`;
    }
}
</script>

</body>
</html>
