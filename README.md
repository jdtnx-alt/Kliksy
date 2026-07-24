# Kliksy — Servicios a domicilio en Florencia, Caquetá

Kliksy es una plataforma web desarrollada en **Laravel** diseñada para conectar a profesionales de diversos sectores (barbería, plomería, electricidad, belleza, etc.) con clientes locales en Florencia, Caquetá. Permite a los usuarios buscar servicios, reservar citas de forma interactiva y gestionar perfiles, agendas e historiales financieros.

---

## 🛠️ Tecnologías y Requisitos

- **Backend**: PHP >= 8.2 & Laravel 11
- **Base de Datos**: MySQL / MariaDB (Recomendado Laragon en Windows)
- **Frontend**: Tailwind CSS (v4) & Alpine.js, compilado con Vite
- **Integraciones**:
  - **Google OAuth**: Para autenticación rápida de usuarios.
  - **Anthropic Claude API**: Para el análisis automático y resumen inteligente de reseñas de los profesionales.
  - **Resend**: Para envío y verificación de correos electrónicos.

---

## 🚀 Instalación y Despliegue Local

Sigue estos pasos para poner en marcha el proyecto en tu entorno local (utilizando Laragon en Windows):

### 1. Clonar el repositorio
Mueve el proyecto dentro del directorio raíz de tu servidor (por ejemplo, `C:\laragon\www\kliksy`).

### 2. Instalar dependencias
Abre una terminal en la carpeta del proyecto y ejecuta:
```bash
composer install
npm install
```

### 3. Configurar variables de entorno
Copia el archivo de ejemplo para crear tu configuración local:
```bash
copy .env.example .env
```
Abre el archivo `.env` recién creado y configura tus accesos a la base de datos y llaves de servicios:
- `DB_DATABASE=kliksy`
- `DB_USERNAME=root`
- `DB_PASSWORD=` *(o la contraseña de tu base de datos local)*

Genera la clave única de la aplicación:
```bash
php artisan key:generate
```

### 4. Migraciones y Seeders
Crea las tablas y los datos de prueba iniciales ejecutando:
```bash
php artisan migrate --seed
```

### 5. Compilar assets y arrancar servidores
Tienes dos formas de ejecutar el frontend:

- **Para desarrollo (recomendado)**: Ejecuta el servidor de Vite en tiempo real para reflejar cambios inmediatamente.
  ```bash
  npm run dev
  ```
  *Nota: Si estás usando un túnel de desarrollo (como VS Code Port Forwarding o Dev Tunnels), asegúrate de que tu puerto 5173 esté accesible o compila en producción.*

- **Para producción/pruebas sin servidor de assets**: Compila los archivos una única vez:
  ```bash
  npm run build
  ```

Luego, en Laragon haz clic en **"Start All"** y accede a la web mediante `http://kliksy.test` o ejecuta:
```bash
php artisan serve
```

---

## 🔒 Variables de Entorno y Buenas Prácticas de Seguridad

### Configuración en Producción
Para desplegar Kliksy en un servidor de producción de forma segura, es obligatorio realizar los siguientes ajustes en el archivo `.env`:

1. **Desactivar el modo depuración**: 
   ```env
   APP_DEBUG=false
   APP_ENV=production
   ```
   *Nunca expongas errores detallados en producción, ya que pueden revelar información interna del servidor y rutas del sistema.*

2. **Credenciales robustas**:
   Modifica el usuario y contraseña por defecto de la base de datos. Evita el uso de `root` sin contraseña en producción.

3. **Cifrado de Variables y Claves**:
   Todas las llaves privadas para servicios externos (**Google Client Secret**, **Anthropic API Key**, **Resend API Key**) deben definirse **exclusivamente en el archivo `.env`** del servidor. Bajo ninguna circunstancia deben escribirse directamente ("quemarse") en el código fuente.

4. **Anonimización de datos (IA)**:
   Al enviar datos a APIs externas (como el análisis de reseñas a Anthropic Claude), la plataforma está diseñada para omitir nombres reales y datos de identificación personal de los clientes, enviando únicamente el comentario genérico para proteger la privacidad de los usuarios.

---

## 💻 Comandos Útiles

- **Crear base de datos limpia**: `php artisan migrate:fresh --seed`
- **Limpiar caché general**: `php artisan optimize:clear`
- **Ver las rutas registradas**: `php artisan route:list`
- **Correr tareas en segundo plano (colas)**: `php artisan queue:work`

---

## 🔌 Documentación de la API y Endpoints Clave

Aunque Kliksy no cuenta con un archivo API dedicado independiente (utiliza el enrutamiento integrado de Laravel), posee múltiples endpoints dinámicos de consulta interactiva:

### 1. Análisis inteligente de reseñas
* **Ruta**: `/profesional/analisis-resenas` (GET)
* **Middleware**: `auth`
* **Descripción**: Obtiene las últimas 20 reseñas del profesional autenticado, las anonimiza y las envía a la API de Anthropic Claude para generar un resumen directo y sugerencias en un formato de máximo 3 frases.
* **Respuesta exitosa (JSON)**:
  ```json
  {
    "resumen": "Los clientes destacan la puntualidad y amabilidad del servicio. Se sugiere mejorar la comunicación previa a la cita."
  }
  ```

### 2. Obtener ranuras (slots) horarios disponibles
* **Ruta**: `/reservar/{profesionalId}/slots` (GET)
* **Descripción**: Retorna las horas disponibles de un profesional para una fecha específica recibida en la consulta `?fecha=YYYY-MM-DD`.
* **Parámetros de consulta**: `fecha` (ej. `2026-06-12`).
* **Respuesta exitosa (JSON)**:
  ```json
  [
    "08:00",
    "09:00",
    "10:00",
    "14:00",
    "15:00"
  ]
  ```

### 3. Registro de Reservas
* **Ruta**: `/reservas` (POST)
* **Middleware**: `auth`
* **Descripción**: Crea una nueva solicitud de reserva.
* **Parámetros**: `profesional_id`, `servicio_id`, `fecha`, `hora`.
