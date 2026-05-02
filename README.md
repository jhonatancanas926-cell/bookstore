# 📚 BookStore — Plataforma de Venta de Libros en Laravel 12

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql)](https://mysql.com)
[![Redis](https://img.shields.io/badge/Redis-7.x-DC382D?logo=redis)](https://redis.io)
[![Tests](https://img.shields.io/badge/Tests-PHPUnit%2011-6E4C7E?logo=php)](https://phpunit.de)
[![Stripe](https://img.shields.io/badge/Pagos-Stripe-635BFF?logo=stripe)](https://stripe.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

Aplicación web completa de venta de libros físicos y ebooks construida con **Laravel 12** y **PHP 8.2**, diseñada para escalar a **10.000+ títulos** y **2.000+ usuarios activos mensuales**. Implementa arquitectura limpia con repositorios y servicios desacoplados, caché Redis, seguridad PCI DSS vía Stripe, frontend en Blade + Tailwind CSS y cobertura de pruebas ≥ 70%.

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Arquitectura](#-arquitectura-del-sistema)
- [Stack Tecnológico](#-stack-tecnológico)
- [Requisitos](#-requisitos)
- [Instalación paso a paso](#-instalación-paso-a-paso)
- [Configuración de entorno (.env)](#-configuración-de-entorno-env)
- [Migraciones y Seeders](#-migraciones-y-seeders)
- [Ejecutar Pruebas](#-ejecutar-pruebas)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Frontend — Vistas Blade](#-frontend--vistas-blade)
- [API Reference](#-api-reference)
- [Seguridad](#-seguridad)
- [Escalabilidad](#-estrategias-de-escalabilidad)
- [Decisiones Técnicas](#-decisiones-técnicas)
- [Solución de Problemas Comunes](#-solución-de-problemas-comunes)
- [Checklist Final](#-checklist-final)

---

## ✨ Características

| Módulo | Descripción |
|--------|-------------|
| 🔐 **Autenticación MFA** | Login con 2FA (TOTP / Google Authenticator) vía Laravel Fortify |
| 📚 **Catálogo optimizado** | 10.000+ libros con búsqueda, filtros y paginación en < 3 s |
| 🛒 **Carrito persistente** | Funciona para usuarios guest y autenticados; se fusiona al hacer login |
| 💳 **Checkout 3 pasos** | Carrito → Facturación → Pago (sin fricción innecesaria) |
| 💰 **Pagos seguros** | Stripe Elements — nunca almacenamos datos de tarjeta (PCI DSS) |
| 📥 **Descarga segura** | Tokens únicos por compra, límite de 5 descargas, TTL de 72 h |
| 👨‍💼 **Panel Admin** | Gestión de inventario, pedidos, usuarios y métricas |
| ⚡ **Rendimiento** | Redis para caché, eager loading, índices DB optimizados |
| 🎨 **Frontend Blade** | Tailwind CSS + Alpine.js, diseño elegante con tema editorial |
| 🧪 **Testing** | PHPUnit/Pest con cobertura ≥ 70 % en módulos críticos |

---

## 🏗️ Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────────┐
│                    CLIENTE (Navegador)                          │
│         Blade + Tailwind CSS + Alpine.js / Fetch API            │
└───────────────────────────┬─────────────────────────────────────┘
                            │ HTTP / HTTPS
┌───────────────────────────▼─────────────────────────────────────┐
│                    LARAVEL 12 (PHP 8.2)                         │
│                                                                  │
│  Middleware ──► Controller ──► Service ──► Repository ──► Model │
│  (Auth/Role/                (Thin,       (Lógica de   (Cache +  │
│   Security/                  solo        negocio)     Eloquent) │
│   RateLimit)                coordina)                           │
│                                                                  │
│  Form Requests (validación) ──► Policies (autorización)         │
│  Observers (caché/rating)   ──► Events/Listeners (async)        │
└─────────────────┬──────────────────────────────────────────────┘
                  │
     ┌────────────┼──────────────┐
     │            │              │
┌────▼─────┐ ┌───▼────┐ ┌──────▼──────┐
│ MySQL 8  │ │ Redis  │ │  Storage    │
│ (datos)  │ │ (caché │ │  Privado    │
│ Índices  │ │  sess) │ │  (ebooks)   │
│ FULLTEXT │ └────────┘ └─────────────┘
└──────────┘
     │
┌────▼──────────────────────────────────┐
│          SERVICIOS EXTERNOS           │
│  Stripe · Mailgun · AWS S3 (opcional) │
└───────────────────────────────────────┘
```

### Flujo de una petición

```
Request
  → Middleware (Auth, Role, SecurityHeaders, RateLimit)
  → Form Request (validación y sanitización)
  → Controller (coordinación, sin lógica de negocio)
  → Service (CartService / OrderService / DownloadService)
  → Repository (BookRepository — Eloquent + Redis)
  → Model (scopes, accessors, relaciones)
  → Response (View Blade o JSON)
```

### Patrón de capas aplicado

| Capa | Responsabilidad | Ejemplo |
|------|----------------|---------|
| **Controller** | Coordinar, responder | `BookController`, `CartController` |
| **Service** | Lógica de negocio | `CartService`, `OrderService` |
| **Repository** | Acceso a datos + caché | `BookRepository` |
| **Model** | Relaciones, scopes, accessors | `Book`, `Order`, `Cart` |
| **Observer** | Efectos secundarios | `BookObserver` invalida caché |

---

## 🛠️ Stack Tecnológico

| Capa | Tecnología | Versión | Propósito |
|------|-----------|---------|-----------|
| **Lenguaje** | PHP | 8.2+ | Backend principal |
| **Framework** | Laravel | 12.x | MVC, ORM, Auth, Queue |
| **Autenticación** | Laravel Fortify | ^1.25 | 2FA TOTP, login, registro |
| **Pagos** | Stripe + Laravel Cashier | ^15.0 | PCI DSS compliant |
| **Base de datos** | MySQL | 8.0+ | Datos principales |
| **Caché / Sesiones** | Redis + Predis | ^2.3 | Rendimiento y escalabilidad |
| **2FA** | pragmarx/google2fa-laravel | ^2.2 | TOTP compatible con Authy/GA |
| **QR para 2FA** | bacon/bacon-qr-code | ^3.0 | Código QR de configuración |
| **Frontend** | Blade + Tailwind CSS CDN | 3.x | UI sin compilación en dev |
| **JS** | Alpine.js / Vanilla JS | — | Interactividad ligera |
| **Pagos JS** | Stripe.js / Elements | v3 | Formulario de tarjeta seguro |
| **Testing** | PHPUnit + Pest | ^11.5 / ^3.7 | Tests unitarios y feature |
| **Mocking** | Mockery | ^1.6 | Mocks en tests |
| **Servidor dev** | XAMPP (PHP-CLI) | — | Desarrollo en Windows |
| **Servidor prod** | Nginx + PHP-FPM | — | Producción Linux |

---

## 📋 Requisitos

### Desarrollo en Windows (XAMPP)

```
XAMPP con PHP 8.2+
Composer 2.6+
MySQL 8.0+ (incluido en XAMPP)
Node.js 20+ (solo si compilas assets)
Git
```

### Extensiones PHP requeridas

Verifica que estas líneas estén **sin** el `;` inicial en `C:\xampp\php\php.ini`:

```ini
extension=bcmath
extension=ctype
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=tokenizer
extension=curl
extension=gd
```

> **Nota:** `ext-pcntl` (requerida por Laravel Horizon) **no existe en Windows**.  
> Por eso `laravel/horizon` y `laravel/sail` fueron excluidos del `composer.json`.

### Verificar versión de PHP

```bash
php -v
# PHP 8.2.x (cli)
```

---

## 🚀 Instalación Paso a Paso

### 1. Crear el proyecto base de Laravel

```bash
cd C:\xampp\htdocs\laravel
composer create-project laravel/laravel bookstore-nuevo
cd bookstore-nuevo
```

> ⚠️ Siempre crear con `create-project` primero. Copiando solo archivos faltarán los archivos base de Laravel (`bootstrap/`, `config/`, `public/`, etc.)

### 2. Reemplazar el composer.json

Copia el `composer.json` del proyecto en la raíz y elimina el lock file:

```bash
del composer.lock
```

### 3. Instalar dependencias del proyecto

```bash
composer install
```

Si aparece conflicto entre Pest y PHPUnit, asegúrate de que el `composer.json` tenga:

```json
"phpunit/phpunit": "^11.5.3",
"pestphp/pest": "^3.7.2"
```

Luego vuelve a ejecutar `composer install`.

### 4. Instalar paquetes adicionales

```bash
composer require laravel/fortify laravel/cashier laravel/sanctum predis/predis stripe/stripe-php pragmarx/google2fa-laravel bacon/bacon-qr-code
```

### 5. Copiar los archivos del proyecto

Copia desde el ZIP descargado las siguientes carpetas sobre el proyecto:

```
app/
database/
routes/
tests/
resources/views/
```

### 6. Configurar el entorno

```bash
copy .env.example .env
php artisan key:generate
```

Editar `.env` con tus valores (ver sección siguiente).

### 7. Crear la base de datos

**Opción A — phpMyAdmin:**
1. Ir a `http://localhost/phpmyadmin`
2. Clic en **Nueva**
3. Nombre: `bookstore`, cotejación: `utf8mb4_unicode_ci`
4. Clic en **Crear**

**Opción B — Línea de comandos:**

```bash
cd C:\xampp\mysql\bin
mysql -u root -p
```
```sql
CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 8. Ejecutar migraciones

```bash
php artisan migrate:fresh --seed
```

> Usa `migrate:fresh` para empezar limpio. En producción usa `migrate` sin `fresh`.

### 9. Configurar almacenamiento

```bash
php artisan storage:link
```

> En Windows, si da error de permisos, ejecuta PowerShell **como Administrador**.

### 10. Crear directorios privados para ebooks

```powershell
mkdir storage\app\private\books\pdf
mkdir storage\app\private\books\epub
```

> El comando `chmod` del README original es solo para Linux; en Windows no aplica.

### 11. Iniciar el servidor

```bash
php artisan serve
```

Abrir en el navegador: **http://localhost:8000**

---

## ⚙️ Configuración de Entorno (.env)

```env
# ─── APLICACIÓN ────────────────────────────────────────────────
APP_NAME="BookStore"
APP_ENV=local               # local | production
APP_KEY=                    # Generada con: php artisan key:generate
APP_DEBUG=true              # false en producción
APP_URL=http://localhost:8000
APP_TIMEZONE=America/Bogota
APP_LOCALE=es

# ─── BASE DE DATOS ──────────────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookstore
DB_USERNAME=root
DB_PASSWORD=                # Vacío en XAMPP por defecto

# ─── CACHÉ Y SESIONES ───────────────────────────────────────────
# Sin Redis en desarrollo, usa file/database
CACHE_STORE=file            # redis en producción
SESSION_DRIVER=file         # redis en producción
QUEUE_CONNECTION=sync       # redis en producción

# Con Redis activo en Windows (opcional):
# CACHE_STORE=redis
# SESSION_DRIVER=redis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# ─── EMAIL ─────────────────────────────────────────────────────
MAIL_MAILER=log             # log en dev (guarda en storage/logs)
MAIL_FROM_ADDRESS=noreply@bookstore.com
MAIL_FROM_NAME="BookStore"

# ─── STRIPE ────────────────────────────────────────────────────
# Usar claves de TEST (pk_test_ / sk_test_) en desarrollo
# Nunca usar claves de producción en local
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# ─── STORAGE ────────────────────────────────────────────────────
FILESYSTEM_DISK=local       # s3 en producción

# ─── ADMINISTRACIÓN ─────────────────────────────────────────────
ADMIN_PASSWORD=Admin123!@#  # Cambiar en producción

# ─── LOGS ───────────────────────────────────────────────────────
LOG_CHANNEL=daily
LOG_LEVEL=debug             # error en producción
```

> ⚠️ **NUNCA** subas el `.env` al repositorio. Ya está en `.gitignore`.

---

## 🗄️ Migraciones y Seeders

### Primera vez (entorno limpio)

```bash
php artisan migrate:fresh --seed
```

### Solo migrar (sin datos)

```bash
php artisan migrate
```

### Solo seeders (BD ya migrada)

```bash
php artisan db:seed
```

### Seeders individuales

```bash
php artisan db:seed --class=GenreSeeder     # 25 géneros
php artisan db:seed --class=AdminSeeder     # Usuario admin + demo
php artisan db:seed --class=BookSeeder      # 10.000 libros (~60 s)
php artisan db:seed --class=CouponSeeder    # Cupones de prueba
```

### Tablas generadas

```
users                  — Usuarios con soporte 2FA y Stripe
password_reset_tokens  — Tokens de reseteo de contraseña
sessions               — Sesiones (driver: database o redis)
genres                 — 25 géneros/categorías
authors                — Autores de libros
books                  — Catálogo principal (10.000 registros)
book_genre             — Relación libro ↔ género
book_author            — Relación libro ↔ autor
reviews                — Reseñas de usuarios
carts                  — Carritos (guest y autenticados)
cart_items             — Ítems del carrito
orders                 — Pedidos con estados
order_items            — Ítems de cada pedido
download_tokens        — Tokens seguros de descarga
coupons                — Cupones de descuento
```

### Credenciales por defecto

| Rol | Email | Password |
|-----|-------|----------|
| Admin | admin@bookstore.com | Admin123!@# |
| Demo | demo@bookstore.com | demo123456 |

> ⚠️ Cambiar las contraseñas inmediatamente en producción.

---

## 🧪 Ejecutar Pruebas

### Configurar entorno de testing

En `.env.testing`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array
STRIPE_KEY=pk_test_fake
STRIPE_SECRET=sk_test_fake
```

### Ejecutar todos los tests

```bash
php artisan test
```

### Con cobertura mínima del 70 %

```bash
php artisan test --coverage --min=70
```

### Por módulo

```bash
php artisan test tests/Feature/Auth/        # Autenticación y 2FA
php artisan test tests/Feature/Book/        # Catálogo y búsqueda
php artisan test tests/Feature/Cart/        # Carrito y cupones
php artisan test tests/Feature/Order/       # Checkout y pedidos
php artisan test tests/Unit/                # Servicios y repositorios
```

### Salida esperada

```
Tests:  47 passed (156 assertions)
Time:   8.42 s
Coverage: 73.4 % ✅ (≥ 70 %)
```

---

## 📁 Estructura del Proyecto

```
bookstore/
├── app/
│   ├── Events/
│   │   └── OrderPaid.php                    # Evento post-pago
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── AdminDashboardController.php
│   │   │   ├── BookController.php           # Catálogo + búsqueda
│   │   │   ├── CartController.php           # Carrito persistente
│   │   │   ├── CheckoutController.php       # 3 pasos de checkout
│   │   │   ├── DownloadController.php       # Descarga segura ebooks
│   │   │   ├── HomeController.php           # Página de inicio
│   │   │   ├── OrderController.php          # Pedidos del usuario
│   │   │   └── StripeWebhookController.php  # Webhooks de Stripe
│   │   ├── Middleware/
│   │   │   ├── EnsureUserHasRole.php        # RBAC: role:admin
│   │   │   └── SecurityHeaders.php          # CSP, HSTS, X-Frame
│   │   └── Requests/
│   │       ├── Book/BookFilterRequest.php
│   │       ├── Cart/AddToCartRequest.php
│   │       └── Order/CheckoutAddressRequest.php
│   ├── Models/
│   │   ├── Author.php
│   │   ├── Book.php                         # Scopes, accessors, helpers
│   │   ├── Cart.php                         # Merge guest → user
│   │   ├── CartItem.php
│   │   ├── Coupon.php
│   │   ├── DownloadToken.php                # Token seguro de descarga
│   │   ├── Genre.php
│   │   ├── Order.php                        # Estados del pedido
│   │   ├── OrderItem.php
│   │   ├── Review.php
│   │   └── User.php                         # 2FA + Stripe Cashier
│   ├── Observers/
│   │   ├── BookObserver.php                 # Invalida caché al modificar
│   │   └── ReviewObserver.php               # Recalcula rating promedio
│   ├── Policies/
│   │   └── OrderPolicy.php                  # Solo el dueño ve sus pedidos
│   ├── Providers/
│   │   └── AppServiceProvider.php           # DI bindings + Rate limiters
│   ├── Repositories/
│   │   ├── Interfaces/
│   │   │   └── BookRepositoryInterface.php
│   │   └── BookRepository.php               # Eloquent + Redis
│   └── Services/
│       ├── CartService.php                  # Lógica del carrito
│       ├── DownloadService.php              # Tokens y stream ebooks
│       └── OrderService.php                # Checkout + Stripe + DB
│
├── bootstrap/
│   └── app.php                              # Arranque Laravel 12 (nuevo formato)
│
├── database/
│   ├── factories/
│   │   ├── BookFactory.php                  # Estados: withPdf, physical, etc.
│   │   ├── CartFactory.php
│   │   ├── CouponFactory.php
│   │   ├── OrderFactory.php                 # Estados: paid, completed
│   │   └── UserFactory.php                  # Estados: admin, withTwoFactor
│   ├── migrations/
│   │   ├── ..._create_users_table.php       # Con 2FA y Stripe fields
│   │   ├── ..._create_books_table.php       # Con índices compuestos
│   │   └── ..._create_cart_orders_table.php # Carrito, pedidos, descargas
│   └── seeders/
│       ├── AdminSeeder.php
│       ├── BookSeeder.php                   # 10.000 libros en chunks
│       ├── CouponSeeder.php
│       ├── DatabaseSeeder.php
│       └── GenreSeeder.php                  # 25 géneros
│
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php                   # Layout principal con navbar
│   ├── home.blade.php                       # Hero + géneros + destacados
│   ├── books/
│   │   ├── index.blade.php                  # Catálogo con sidebar de filtros
│   │   └── show.blade.php                   # Detalle del libro
│   ├── cart/
│   │   └── index.blade.php                  # Carrito con cupones
│   ├── checkout/
│   │   ├── address.blade.php                # Paso 2: facturación
│   │   ├── payment.blade.php                # Paso 3: Stripe Elements
│   │   └── success.blade.php                # Confirmación de pedido
│   ├── orders/
│   │   └── index.blade.php                  # Historial de pedidos
│   ├── downloads/
│   │   └── index.blade.php                  # Ebooks disponibles
│   ├── admin/
│   │   └── dashboard.blade.php              # Métricas y pedidos recientes
│   └── components/
│       ├── book-card.blade.php              # Tarjeta de libro reutilizable
│       └── pagination.blade.php             # Paginación personalizada
│
├── routes/
│   └── web.php                              # Todas las rutas del proyecto
│
├── tests/
│   ├── Feature/
│   │   ├── Auth/AuthenticationTest.php      # Login, registro, 2FA
│   │   ├── Book/BookCatalogTest.php         # Catálogo, filtros, caché
│   │   ├── Cart/CartTest.php                # Carrito, cupones, merge
│   │   └── Order/CheckoutTest.php           # Checkout, pedidos
│   └── Unit/
│       └── Services/CartServiceTest.php     # Lógica de negocio del carrito
│
├── artisan                                  # CLI de Laravel
├── composer.json                            # Dependencias PHP (sin Horizon/Sail)
└── README.md                                # Este archivo
```

---

## 🎨 Frontend — Vistas Blade

El frontend usa **Blade + Tailwind CSS CDN** (sin compilación en desarrollo) con un tema editorial elegante en colores crema e ink (marrón oscuro).

### Páginas implementadas

| Ruta | Vista | Descripción |
|------|-------|-------------|
| `/` | `home.blade.php` | Hero, géneros, libros destacados, bestsellers |
| `/books` | `books/index.blade.php` | Catálogo con filtros laterales y paginación |
| `/books/{slug}` | `books/show.blade.php` | Detalle, formatos, reseñas, libros similares |
| `/cart` | `cart/index.blade.php` | Carrito con cupones y resumen |
| `/checkout` | `checkout/address.blade.php` | Paso 2: datos de facturación |
| `/checkout/payment` | `checkout/payment.blade.php` | Paso 3: Stripe Elements |
| `/checkout/success/{order}` | `checkout/success.blade.php` | Confirmación |
| `/orders` | `orders/index.blade.php` | Historial de pedidos del usuario |
| `/downloads` | `downloads/index.blade.php` | Ebooks disponibles para descargar |
| `/admin` | `admin/dashboard.blade.php` | Panel con métricas y pedidos recientes |

### Características del diseño

- **Sin compilación:** Tailwind CDN → funciona directamente con `php artisan serve`
- **Tipografía editorial:** Playfair Display (títulos) + DM Sans (cuerpo)
- **Paleta ink/cream:** colores cálidos que evocan papel y tinta
- **Responsive:** Grid adaptable de 2 a 5 columnas según pantalla
- **Interactividad:** búsqueda con autocomplete AJAX, carrito sin recarga, toasts de notificación
- **Componentes reutilizables:** `book-card`, `pagination`

---

## 🌐 API Reference

### Catálogo de Libros

```http
GET /books
GET /api/books
```

**Parámetros:**

| Parámetro | Tipo | Descripción | Ejemplo |
|-----------|------|-------------|---------|
| `search` | string | Título, descripción o autor | `?search=García Márquez` |
| `genre` | string | Slug del género | `?genre=ficcion` |
| `format` | string | `pdf`, `epub`, `physical` | `?format=epub` |
| `min_price` | float | Precio mínimo USD | `?min_price=5` |
| `max_price` | float | Precio máximo USD | `?max_price=30` |
| `language` | string | Código de idioma | `?language=es` |
| `sort` | string | `relevance`, `price_asc`, `price_desc`, `newest`, `rating`, `bestseller` | `?sort=bestseller` |
| `per_page` | int | Resultados por página (máx. 48) | `?per_page=24` |

**Respuesta JSON:**

```json
{
  "data": [
    {
      "id": 1,
      "title": "Cien Años de Soledad",
      "slug": "cien-anos-de-soledad",
      "price_cents": 1499,
      "price": "$14.99",
      "rating_avg": 4.8,
      "has_pdf": true,
      "has_epub": true,
      "authors": [{ "name": "Gabriel García Márquez" }],
      "genres":  [{ "name": "Ficción", "slug": "ficcion" }],
      "cover_url": "https://..."
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 417,
    "per_page": 24,
    "total": 10000,
    "next_page_url": "...",
    "prev_page_url": null
  }
}
```

### Endpoints principales

```http
GET    /books                          # Catálogo HTML
GET    /books/{slug}                   # Detalle HTML
GET    /books/search?q={term}          # Autocomplete JSON (máx. 8)
POST   /cart/items                     # Agregar al carrito
DELETE /cart/items/{bookId}/{format}   # Eliminar del carrito
PATCH  /cart/items/{bookId}/{format}   # Actualizar cantidad
POST   /cart/coupon                    # Aplicar cupón
POST   /checkout/intent                # Crear Stripe PaymentIntent (auth)
POST   /checkout/confirm               # Confirmar pedido (auth)
POST   /webhooks/stripe                # Webhook Stripe (firma verificada)
```

---

## 🔐 Seguridad

### 1. Autenticación y Autorización

- **MFA/2FA** vía Laravel Fortify — TOTP compatible con Google Authenticator y Authy
- **RBAC** vía middleware `role:admin` (`EnsureUserHasRole`)
- **Rate limiting** en login: 5 intentos/min → bloqueo temporal (anti brute-force)
- **Email verification** obligatoria antes de hacer checkout

### 2. Pagos — PCI DSS

```
✅ Stripe Elements: datos de tarjeta NUNCA tocan nuestros servidores
✅ Solo almacenamos: stripe_payment_intent_id y tipo de pago
✅ Webhooks verificados con firma HMAC (Stripe-Signature header)
✅ HTTPS obligatorio en producción con HSTS configurado
❌ Números de tarjeta: NUNCA almacenados
❌ CVV: NUNCA almacenado ni solicitado directamente
```

### 3. Protección XSS / CSRF / SQL Injection

- **CSRF:** Token en todos los formularios Blade (`@csrf`)
- **XSS:** Blade escapa `{{ }}` automáticamente. CSP en headers HTTP
- **SQL Injection:** Eloquent usa prepared statements en todas las queries
- **Input Validation:** Form Requests con reglas estrictas y sanitización

### 4. Descargas Seguras de Ebooks

```
✅ Archivos en storage privado (fuera de /public)
✅ Tokens de 64 caracteres criptográficamente aleatorios
✅ Token ligado a usuario + libro + formato (no transferible)
✅ TTL de 72 horas
✅ Límite de 5 descargas por token
✅ Registro de IP en cada descarga (auditoría)
✅ Paths de archivos encriptados en BD (Laravel encrypt())
✅ Headers anti-MIME sniffing en la respuesta del stream
```

### 5. Headers HTTP de Seguridad

```http
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
Content-Security-Policy: default-src 'self'; script-src 'self' https://js.stripe.com; ...
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
```

### 6. Otras medidas

- **Soft Deletes** en usuarios y pedidos (auditoría, sin pérdida de datos)
- **Passwords** hasheados con Bcrypt (`'password' => 'hashed'` en cast)
- **Variables de entorno** para todas las credenciales (nunca hardcodeadas)
- **Logs** de eventos de seguridad (login fallido, descarga anómala, disputes Stripe)

---

## 📈 Estrategias de Escalabilidad

### Métricas objetivo

| Métrica | Objetivo | Estrategia implementada |
|---------|----------|------------------------|
| Carga catálogo | < 3 seg | Redis caché + índices DB + eager loading |
| Usuarios concurrentes | 2.000+ | Redis sessions + horizontal scaling |
| Disponibilidad | ≥ 99 % | Load balancer + health check `/up` |
| Abandono carrito | < 15 % | Checkout 3 pasos + carrito persistente |
| Cobertura tests | ≥ 70 % | PHPUnit + Pest |

### Estrategia de caché Redis

```
Catálogo paginado   → TTL 5 min  (alta lectura)
Detalle de libro    → TTL 30 min (muy estable)
Listas curadas      → TTL 15 min (featured, bestsellers)
Invalidación        → BookObserver al crear/modificar/eliminar libro
```

### Optimización de queries

```php
// Eager loading — evita el problema N+1
Book::with(['authors:id,name,slug', 'genres:id,name,slug'])->paginate(24);

// Selección de columnas — solo las necesarias
Book::select(['id','title','slug','price_cents','cover_image'])->get();

// Índices compuestos declarados en migraciones
$table->index(['is_active', 'is_featured']);   // catálogo principal
$table->index(['price_cents']);                 // filtro precio
$table->index(['rating_avg', 'sales_count']);   // ordenamiento
$table->index(['user_id', 'status']);           // pedidos por usuario
```

### Roadmap de escalabilidad (10× más tráfico)

```
1. Read replicas MySQL     → separar lecturas/escrituras
2. Elasticsearch           → búsqueda full-text avanzada
3. CDN (CloudFront)        → assets estáticos y portadas
4. Queue Workers           → más workers para emails y descargas
5. Horizontal scaling      → múltiples instancias + load balancer
6. Laravel Octane          → servidor persistente (Swoole/RoadRunner)
7. Cache tags Redis        → invalidación granular por grupos
```

---

## 💡 Decisiones Técnicas

### ¿Por qué Repository Pattern?

Permite cambiar la implementación de acceso a datos sin tocar los servicios. Hoy es Eloquent + MySQL; mañana puede ser Elasticsearch para búsqueda, sin refactorizar nada más.

```php
// El controller depende de la INTERFAZ, no de la implementación
public function __construct(
    private readonly BookRepositoryInterface $bookRepo  // ← interfaz
) {}
```

### ¿Por qué precios en centavos (integers)?

```php
// ❌ Float — imprecisión financiera garantizada
$total = 14.99 + 9.99; // = 24.979999999... ← INCORRECTO

// ✅ Centavos — aritmética exacta con enteros
$total = 1499 + 999;   // = 2498 centavos = $24.98 ← CORRECTO
```

### ¿Por qué Stripe en lugar de implementar pagos propios?

- PCI DSS Compliance Level 1 sin certificación propia costosa
- Stripe Elements maneja el formulario en su dominio seguro
- Soporte nativo para 3D Secure, pagos internacionales y disputes
- Ahorra meses de auditoría de seguridad

### ¿Por qué Blade + Tailwind en lugar de Vue/React SPA?

- SEO nativo sin necesidad de SSR
- Menor complejidad operacional (no hay API separada)
- Tiempos de carga inicial más rápidos (no hay bundle JS pesado)
- Tailwind CDN permite trabajar en Windows sin Node.js/npm

### ¿Por qué Redis para sesiones y caché?

- En entorno multi-servidor, las sesiones en archivos no escalan
- Redis permite `Cache::tags()` para invalidación granular
- TTL automático sin cron jobs de limpieza
- Mismo servicio para caché, sesiones y colas de jobs

### ¿Por qué excluir Horizon y Sail en Windows?

- `laravel/horizon` requiere `ext-pcntl` que **no existe en Windows**
- `laravel/sail` es exclusivo de Docker en Linux/Mac
- En desarrollo Windows se usan `php artisan queue:work` y XAMPP directamente

---

## 🔧 Solución de Problemas Comunes

### Error: `Call to a member function make() on int` en artisan

**Causa:** Falta el archivo `bootstrap/app.php` o el proyecto no fue creado con `create-project`.

**Solución:**
```bash
composer create-project laravel/laravel bookstore-nuevo
```
Luego copia los archivos del proyecto sobre el proyecto recién creado.

---

### Error: `pestphp/pest conflicts with phpunit/phpunit`

**Causa:** Versiones incompatibles entre Pest y PHPUnit en el `composer.lock`.

**Solución:**
```bash
del composer.lock
# Asegúrate de tener en composer.json:
# "phpunit/phpunit": "^11.5.3"
# "pestphp/pest": "^3.7.2"
composer install
```

---

### Error: `laravel/horizon requires ext-pcntl`

**Causa:** `ext-pcntl` no existe en Windows.

**Solución:** Horizon ya fue eliminado del `composer.json`. Si aparece, verifica que no esté en `require`.

---

### Error al ejecutar `storage:link` — permisos

**Causa:** PowerShell sin permisos de administrador.

**Solución:** Abrir PowerShell **como Administrador** y volver a ejecutar:
```bash
php artisan storage:link
```

---

### Error: `mkdir -p` con llaves `{}` en PowerShell

**Causa:** Sintaxis de bash no compatible con PowerShell.

**Solución:**
```powershell
mkdir storage\app\private\books\pdf
mkdir storage\app\private\books\epub
```

---

### La página muestra `Nothing to migrate`

**Causa:** Las migraciones del proyecto no fueron copiadas a `database/migrations/`.

**Solución:**
1. Copia los 3 archivos de migración al proyecto
2. Elimina la migración original de usuarios de Laravel (`0001_01_01_000000_create_users_table.php`)
3. Ejecuta: `php artisan migrate:fresh --seed`

---

### El catálogo no muestra libros

**Causa:** El seeder aún no se ejecutó.

**Solución:**
```bash
php artisan db:seed --class=BookSeeder
```
> Toma aproximadamente 60 segundos para 10.000 libros.

---

## ✅ Checklist Final

```
✅ Autenticación con MFA (2FA vía Fortify + TOTP)
✅ Catálogo optimizado (Redis, índices DB, eager loading, < 3 s)
✅ Búsqueda por título, autor, género, precio, formato e idioma
✅ Checkout simplificado (máximo 3 pasos)
✅ Seguridad PCI DSS (Stripe Elements, sin almacenar tarjetas)
✅ Descarga segura de ebooks (tokens únicos, TTL 72h, límite 5)
✅ Panel de administración (inventario, pedidos, usuarios, métricas)
✅ Tests automatizados ≥ 70 % (Auth, Catálogo, Carrito, Checkout)
✅ Código desacoplado (Controller → Service → Repository → Model)
✅ Protección XSS, CSRF, SQL Injection
✅ Headers de seguridad HTTP (CSP, HSTS, X-Frame-Options)
✅ Rate limiting en login, API y checkout
✅ Soft deletes para auditoría de usuarios y pedidos
✅ Carrito persistente (guest + autenticado, merge al login)
✅ Caché Redis con invalidación automática por Observer
✅ Precios en centavos (precisión financiera sin floats)
✅ Frontend Blade + Tailwind sin compilación en desarrollo
✅ Compatible con PHP 8.2 y Windows/XAMPP
✅ README completo con instalación, arquitectura y troubleshooting
```

---

## 🤝 Contribución

1. Haz fork del repositorio
2. Crea una rama: `git checkout -b feature/nueva-funcionalidad`
3. Escribe tests para tu cambio
4. Verifica que pasen: `php artisan test`
5. Commit: `git commit -m 'feat: descripción del cambio'`
6. Push y abre un Pull Request

### Convenciones de commits

```
feat:     Nueva funcionalidad
fix:      Corrección de bug
refactor: Refactorización sin cambio de comportamiento
test:     Agregar o corregir tests
docs:     Documentación
chore:    Mantenimiento y configuración
```

---

## 📄 Licencia

MIT License — ver [LICENSE](LICENSE) para detalles.

---

<div align="center">
  <p>Construido con ❤️ usando <strong>Laravel 12</strong> y <strong>PHP 8.2</strong></p>
  <p>
    <a href="https://laravel.com">Laravel</a> ·
    <a href="https://stripe.com">Stripe</a> ·
    <a href="https://tailwindcss.com">Tailwind CSS</a>
  </p>
</div>
