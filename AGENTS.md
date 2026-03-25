## agents.md

### Resumen
Aplicación PHP orientada a objetos que solicita al usuario N elementos y muestra N números aleatorios en una tabla. Implementación separada en archivos (clases y controlador) y sin bucles infinitos, switch o break. Punto de entrada: html/noo/index.php. Sin dependencias ni uso de Composer (todo con require_once).

### Entorno Docker requerido
Se asume el siguiente fragmento de docker-compose.yml ya disponible:

services:
  web:
    image: php:7.4-apache 
    ports:
      - "8082:80"  
    volumes:
      - ./html:/var/www/html  
    restart: always

La aplicación debe colocarse dentro de la carpeta html/noo/ en el host (montada en /var/www/html/noo/ dentro del contenedor). El punto de entrada será html/noo/index.php para poder acceder desde http://localhost:8082/noo/.

### Estructura de archivos (entregables) — colocar dentro de html/noo/
- html/noo/
  - index.php              — Front controller (punto de entrada) que inicia sesión y ejecuta App; usa require_once para incluir clases.
  - src/
    - App.php              — Coordina flujo PRG y orquesta clases.
    - Request.php          — Maneja y valida entrada HTTP.
    - RandomGenerator.php  — Genera números aleatorios y estadísticas.
    - Renderer.php         — Renderiza vistas y escapa salidas.
  - views/
    - form.php             — Fragmento HTML del formulario.
    - results.php          — Fragmento HTML de la tabla de resultados.
  - README.md              — Instrucciones breves de requisitos y ejecución.
  - .htaccess (opcional)   — configuración Apache si se desea.
  - storage/ (opcional)    — para archivos temporales si necesario (permisos ajustados).

Nota: No usar composer ni autoload; incluir manualmente con require_once desde index.php.

### Reglas de diseño (obligatorias)
- Programación orientada a objetos: cada responsabilidad en su clase.
- Archivos separados por clase como se listan arriba.
- No usar bucles infinitos, switch ni break.
- Validación servidor: n entero positivo entre 1 y 1000 por defecto.
- Campos opcionales para rango mínimo y máximo (min < max).
- Prevención de reenvío accidental: patrón PRG (Post/Redirect/Get) usando sesiones.
- Escapar toda salida HTML para evitar XSS.
- Comentarios breves en secciones clave del código.
- Compatible con PHP 7.4 (evitar sintaxis exclusiva de 8+).
- No dependencias externas; todo código en los archivos entregados y cargado vía require_once.

### Contratos de las clases (compatibles con PHP 7.4, sin Composer)

1) src/Request.php
- Propósito: Leer POST/GET, normalizar y validar parámetros.
- Métodos públicos:
  - __construct(array $get, array $post)
  - getInt(string $key, int $default = null) : ?int
  - validate() : array — devuelve ['errors' => [], 'data' => []]
  - all() : array
- Validaciones:
  - 'n' debe ser entero entre 1 y 1000 (por defecto).
  - 'min' y 'max' (si enviados) deben ser enteros y min < max; si no enviados, usar 1 y 10000.
- Implementación: usar filter_var(..., FILTER_VALIDATE_INT) compatible con PHP 7.4.

2) src/RandomGenerator.php
- Propósito: Generar N enteros aleatorios dentro de un rango y calcular estadísticas.
- Constructor: __construct(int $n, int $min = 1, int $max = 10000)
- Métodos públicos:
  - generate() : array   — devuelve el array de números (longitud n).
  - getSum() : int
  - getAverage() : float
  - getMin() : int
  - getMax() : int
- Reglas: No usar bucles infinitos; emplear for o foreach para iterar; usar random_int($min, $max).

3) src/Renderer.php
- Propósito: Renderizar vistas y escapar salidas.
- Métodos públicos:
  - renderForm(array $data = []) : string
  - renderResults(array $numbers, array $stats, array $previousInput = []) : string
- Escapado: usar htmlspecialchars con ENT_QUOTES y 'UTF-8'.
- Debe retornar strings completos para ser impresos por index.php.
- Incluir rutas relativas a views/ para incluir los fragmentos.

4) src/App.php
- Propósito: Coordinar Request, RandomGenerator y Renderer; implementar PRG.
- Métodos públicos:
  - __construct(Request $req, Renderer $renderer)
  - run() : void
- Flujo:
  - index.php debe llamar session_start() antes de crear App.
  - Si método POST:
    - Validar entrada con Request::validate()
    - Si errores: guardar errores y entrada en $_SESSION y redirigir a GET (PRG)
    - Si ok: crear RandomGenerator, generar números, almacenar resultados y estadísticas en $_SESSION, redirigir a GET
  - Si método GET:
    - Cargar datos previos y errores/resultado desde $_SESSION si existen
    - Limpiar esas claves de $_SESSION tras leerlas
    - Usar Renderer para producir HTML: form + (opcional) results
- Implementar redirecciones con header('Location: ...') y exit; compatible con PHP 7.4.

### Contenido de index.php (puntos clave)
- Ubicación: html/noo/index.php
- Debe arrancar session_start().
- Debe incluir clases con require_once __DIR__ . '/src/Request.php', etc.
- Construir Request con $_GET y $_POST; inyectarlo en App con Renderer; llamar App::run().
- index.php servirá como único punto de entrada para el contenedor cuando se acceda a http://localhost:8082/noo/.

Ejemplo mínimo de inclusión en index.php:
require_once __DIR__ . '/src/Request.php';
require_once __DIR__ . '/src/RandomGenerator.php';
require_once __DIR__ . '/src/Renderer.php';
require_once __DIR__ . '/src/App.php';

### Vistas (views/)
- views/form.php: formulario HTML con action apuntando a ./index.php (ruta relativa) y método POST; campos: n (number), min (number, optional), max (number, optional); conservar valores previos usando datos inyectados.
- views/results.php: tabla con columnas Índice y Número aleatorio; fila final con Suma, Promedio (formateado a 2 decimales), Mínimo y Máximo.
- Todas las salidas deben pasar por htmlspecialchars.
- Las vistas deben ser incluidas por Renderer mediante rutas relativas (por ejemplo, __DIR__ . '/../views/form.php').

### README.md (breve — colocar en html/noo/ README.md)
- Requisitos: Docker + docker-compose; puerto mapeado 8082.
- Instrucciones:
  1. Colocar esta carpeta en ./html/noo/ del proyecto que contiene docker-compose.yml.
  2. Ejecutar docker-compose up -d
  3. Abrir http://localhost:8082/noo/ (index.php será el punto de entrada)
  4. PHP 7.4 es el target; la app evita sintaxis de PHP 8+.
- Nota: No se requiere Composer; todas las clases se incluyen con require_once.

### Permisos y despliegue en contenedor
- Asegurar permisos de lectura para Apache dentro del contenedor (archivos 644, carpetas 755).
- Si Apache no reconoce index.php en /noo/, confirmar que DirectoryIndex incluye index.php o acceder explícitamente a /noo/index.php.

### Ejemplo de comportamiento esperado
- Usuario abre http://localhost:8082/noo/ → ve formulario con campos: n (requerido), min (opcional), max (opcional) y botón Generar.
- Envía POST con n=10, min=1, max=100 → App valida y redirige (PRG) a GET.
- GET muestra formulario con valores previos y una tabla 10 filas con columnas: Índice, Número aleatorio; fila final con Suma, Promedio (2 decimales), Mínimo y Máximo.

### Notas de seguridad y rendimiento
- Escapar siempre la salida.
- Validar y sanear toda entrada.
- No permitir n > 1000 para evitar consumo excesivo de memoria/CPU.
- No usar switch ni break en ninguna clase ni script.

--- End of agents.md