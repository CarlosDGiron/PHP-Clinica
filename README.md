# Sistema de gestión para clínica

Este repositorio contiene una aplicación web de gestión para una clínica. El proyecto está organizado como una aplicación PHP que consume la API interna del mismo servidor.


## Estructura del proyecto

- `app/` - Código PHP del servidor
  - `config/` - Configuraciones (base de datos, rutas, constantes como `URL_ROUTE`, TTL de token, etc.)
  - `Controllers/` - Controladores para cada recurso (LoginController, UserController, etc.).
  - `Models/` - Lógica de acceso a datos (Database wrapper, modelos por entidad).
  - `middleware/` - Middlewares (por ejemplo `AuthMiddleware.php` que valida tokens).
  - `Views/` - Plantillas PHP que inyectan `URL_ROUTE` y cargan los assets.
  - `libraries/` - Clases base como `Core` (rutado) y `Controllers` (base para cargar modelos y middleware).

- `public/` - Archivos públicos servidos por Apache/Nginx
  - `index.php` - Punto de entrada que referencia `app/config/Config.php` y arranca el `Core`.
  - `css/`, `js/`, `lib/`, `img/` - Activos estáticos del frontend.
  - `js/login.js`, `js/services/apiService.js` - Lógica cliente para inicio de sesión y consumo de API.

- `apache2-custom.conf`, `.htaccess` - Configuración del servidor, tomar en cuenta que la aplicacion esta dockerizada (SSL, redirección a `public/`, manejo de Authorization/CORS en setups específicos).
