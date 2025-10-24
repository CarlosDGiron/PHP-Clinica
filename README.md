# Sistema de gestión para clínica

Este repositorio contiene una aplicación web de gestión para una clínica (pacientes, empleados, ventas, inventario, módulos y roles). El proyecto está organizado como una aplicación PHP ligera con un frontend estático que consume la API interna del mismo servidor.

> Nota: Se ignora el folder `movil` (aplicación móvil) en esta descripción.

## Resumen

La aplicación sigue una arquitectura monolítica donde el servidor PHP sirve tanto las vistas (HTML/CSS/JS) como los endpoints JSON que utiliza el frontend. No es una SPA completa en un framework moderno; en cambio, usa módulos JavaScript y fetch para comunicarse con el backend.

Principales características:
- Autenticación por token (tokens almacenados en la tabla `session_tokens`).
- Control de sesiones con vencimiento (TTL configurado en `app/config/Config.php`).
- Sistema de roles y permisos que controla acceso y visibilidad de módulos.
- Módulos para manejar usuarios, pacientes, empleados, inventario, ventas, exámenes, etc.

## Estructura del proyecto (visión general)

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

- `apache2-custom.conf`, `.htaccess` - Ejemplos de configuración del servidor (SSL, redirección a `public/`, manejo de Authorization/CORS en setups específicos).

## Lógica del backend

1. Routing y carga
	- `public/index.php` carga `app/config/Config.php` y el autoloader. La clase `Core` interpreta la URL y mapea la primera parte a un controlador (según `app/config/RouteMap.php`) y el segundo segmento a un método (mapeo de nombres en `ROUTE_METHOD_MAP`).

2. Controladores
	- Los controladores extienden una clase base `Controllers` que carga automáticamente el modelo correspondiente y el middleware de autenticación si existe.
	- `LoginController` expone por ejemplo:
	  - `login` (ruta `login/ingresar`): recibe POST con JSON (username/password), verifica credenciales con `LoginModel::getLoginUser`, y si es correcto genera un token seguro (bin2hex(random_bytes(32))) que se guarda en `session_tokens` con `expires_at`, `ip_address` y `user_agent`.
	  - `token` (ruta `login/token`): usa `AuthMiddleware->validateToken()` para validar el token recibido por cabecera `Authorization: Bearer <token>` y devuelve información del usuario y sus módulos.

3. Middleware de autenticación
	- `AuthMiddleware.php` extrae la cabecera Authorization desde diferentes fuentes (compatibilidad con entornos CGI/FastCGI y proxies), busca la sesión en la tabla `session_tokens`, comprueba expiración y actualiza `expires_at` para mantener viva la sesión.
	- Si el token es inválido o no existe, el middleware devuelve 401.

4. Acceso a datos
	- Los modelos usan la clase `Database` (en `app/config/Database.php`) para preparar y ejecutar consultas con binding de parámetros. Las principales tablas afectadas incluyen `user`, `role`, `module`, `permission` y `session_tokens`.

## Lógica del frontend

1. Vistas y assets
	- Las plantillas en `app/Views/` inyectan la constante `URL_ROUTE` para construir rutas relativas a los assets y endpoints.
	- `public/js` contiene lógica modular (por ejemplo `modules/*.js`) que invoca `apiService.fetchData` para interactuar con la API.

2. Auth flow
	- El formulario en `LoginView.php` envía credenciales al endpoint `POST ${urlBase}/login/ingresar` con `Content-Type: application/json`.
	- Si la respuesta contiene `success: true` y `token`, el frontend guarda dicho token en `localStorage` y lo usa en llamadas siguientes mediante la cabecera `Authorization: Bearer <token>`.
	- `apiService.fetchData` añade la cabecera Authorization sólo si existe `token` en `localStorage` (evita enviar `Bearer null` y problemas con preflight/CORS).

3. Manejo de errores y expiración
	- Cuando una petición devuelve 401, el frontend borra el token local y muestra un mensaje de sesión expirada o de acceso no autorizado.

## HTTPS y proxy

Si la aplicación se despliega detrás de un proxy o con terminación TLS (HTTPS offloaded), hay algunas configuraciones importantes que ya están incluidas o que pueden ser necesarias:

- `app/config/Config.php` detecta `X-Forwarded-Proto` y `X-Forwarded-Ssl` para construir correctamente `URL_ROUTE` con `https://` cuando el proxy realiza la terminación TLS.
- `.htaccess` contiene una regla para preservar `Authorization` y encabezados CORS mínimos útiles para pruebas. Dependiendo del servidor/proxy (NGINX, Cloudflare, etc.) puede ser necesario reenviar explícitamente Authorization o configurar `RequestHeader` en Apache.

## Recomendaciones de despliegue

- Asegurar que el proxy (si existe) reenvíe la cabecera `Authorization` a PHP. Con Apache en algunos setups puede requerir `RewriteRule` o `RequestHeader` para propagar `Authorization`.
- Habilitar HTTPS y comprobar que `URL_ROUTE` use `https://` (Config.php ya contempla proxies comunes).
- En producción, restringir `Access-Control-Allow-Origin` a los orígenes necesarios (no dejar `*`).
- Considerar usar cookies httpOnly/secure + SameSite para la sesión si se migra de tokens locales a cookies.

## Cómo ejecutar en desarrollo

1. Configura tu servidor Apache/Nginx para que apunte a la carpeta `public/`.
2. Copia `app/config/Database.php.example` a `app/config/Database.php` y actualiza credenciales.
3. Asegura que PHP puede escribir (si se necesita) en `storage/`.
4. Importa la base de datos `clinica.sql`.
5. Accede al sitio desde `http://localhost/` o `https://tu-dominio/` según tu configuración.

## Archivos importantes

- `app/config/Config.php` - configuración general y detección de protocolo.
- `app/Controllers/LoginController.php` - endpoints de login y token.
- `app/middleware/AuthMiddleware.php` - validación de token.
- `public/js/services/apiService.js` - cliente fetch con manejo de Authorization.
- `.htaccess` y `apache2-custom.conf` - ejemplos de configuración de servidor.

## Contribuir

Si quieres contribuir, abre un issue primero describiendo el cambio propuesto. Para cambios grandes (migración a SPA, OAuth, etc.) propón un plan y pruebas.

---
Si quieres que ajuste o amplíe alguna sección (por ejemplo más detalles del esquema DB, endpoints disponibles o flujos de autorización), dime cuáles y lo añado.
