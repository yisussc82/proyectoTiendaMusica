# Tienda de música — TFG DAW

Tienda online de instrumentos y material de audio hecha con Laravel. Tiene una zona pública (catálogo), una zona para clientes (direcciones y pedidos) y un panel de gestión para el personal de la tienda.

## Tecnologías

| Parte | Versión |
| --- | --- |
| PHP | 8.3 o superior |
| Laravel | 13 |
| Autenticación | Jetstream con Livewire 3 (Fortify por debajo) y Sanctum para la API |
| Base de datos | MySQL 8 en desarrollo; SQLite en memoria para las pruebas |
| Pruebas | Pest |
| Estilos | Tailwind CSS con Vite |

Se necesita tener instalados PHP, Composer, Node.js (22 o superior) y MySQL. En Windows se ha usado Laravel Herd.

## Instalación desde cero

1. Clonar el repositorio y entrar en la carpeta.
2. Instalar las dependencias:
   ```
   composer install
   npm install
   npm run build
   ```
3. Crear el archivo de configuración y la clave:
   ```
   cp .env.example .env
   php artisan key:generate
   ```
4. Crear en MySQL una base de datos vacía llamada `proyectophp` y, si hace falta, cambiar usuario y contraseña en `.env` (`DB_USERNAME` y `DB_PASSWORD`).
5. Crear las tablas y cargar los datos de prueba:
   ```
   php artisan migrate:fresh --seed
   ```
6. Ejecutar las pruebas:
   ```
   php artisan test
   ```

Los datos de prueba se generan con una semilla fija (`fake()->seed(2026)` en `DatabaseSeeder`) y con `APP_FAKER_LOCALE=es_ES`, así que cada reconstrucción crea los mismos datos.

## Usuarios de prueba

Todos tienen la contraseña `password`.

| Email | Rol | Zona |
| --- | --- | --- |
| `admin@musicstore.test` | Administración | Panel (todo) |
| `catalog@musicstore.test` | Catálogo | Panel |
| `warehouse@musicstore.test` | Almacén | Panel: pedidos |
| `support@musicstore.test` | Atención al cliente | Panel: pedidos y clientes |
| `customer@musicstore.test` | Cliente | Mi cuenta |

## Datos de ejemplo

- 5 categorías, 12 subcategorías y 72 productos, cada uno con 3 imágenes.
- Productos en distintos estados: al menos 6 con oferta, 3 desactivados (no visibles en la tienda) y 2 retirados con borrado lógico.
- 21 clientes con 2 direcciones cada uno, carrito, lista de deseos y pedidos en distintos estados.
- El cliente de prueba tiene siempre un pedido pendiente, uno pagado y un producto en la lista de deseos con aviso de descuento.

## Comandos propios

| Comando | Qué hace |
| --- | --- |
| `php artisan orders:cancel-stale --hours=48` | Cancela los pedidos pendientes más antiguos que las horas indicadas |
| `php artisan catalog:refresh-featured --limit=8` | Marca como destacados los productos más vendidos |
| `php artisan products:low-stock --threshold=5` | Lista los productos con poco stock |
| `php artisan customers:top --min=500` | Lista los clientes que han gastado al menos esa cantidad |

Los dos primeros también se pueden lanzar desde el panel del administrador y están programados en `routes/console.php`.
