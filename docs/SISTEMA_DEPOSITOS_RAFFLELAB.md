# 🏦 Sistema de Depósitos - RaffleLab
## Documentación Técnica Completa

**Proyecto:** RaffleLab - Superlative Lottery Platform  
**Versión:** CodeCanyon  
**Framework:** Laravel (PHP 8.3+)  
**Fecha de documentación:** Febrero 2026

---

## 📑 Índice

1. [Resumen General](#1-resumen-general)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Flujo de Depósito](#3-flujo-de-depósito)
4. [Componentes del Sistema](#4-componentes-del-sistema)
5. [Base de Datos](#5-base-de-datos)
6. [Formulario de Depósito (Frontend)](#6-formulario-de-depósito-frontend)
7. [Controlador Principal](#7-controlador-principal)
8. [Pasarelas de Pago](#8-pasarelas-de-pago)
9. [Estados del Depósito](#9-estados-del-depósito)
10. [Configuración y Personalización](#10-configuración-y-personalización)

---

## 1. Resumen General

El sistema de depósitos de RaffleLab permite a los usuarios agregar fondos a su cuenta mediante múltiples pasarelas de pago. El sistema soporta:

- **30+ pasarelas de pago automáticas** (PayPal, Stripe, Razorpay, etc.)
- **Pasarelas manuales** configurables por el administrador
- **Criptomonedas** (Bitcoin, etc.)
- **Comisiones configurables** (fijas y porcentuales)
- **Límites de depósito** por pasarela

### URL de Acceso
```
https://tu-sitio.com/deposit
```

---

## 2. Arquitectura del Sistema

### 2.1 Diagrama de Arquitectura

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              CAPA DE PRESENTACIÓN                               │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│   ┌─────────────────────┐    ┌─────────────────────┐    ┌──────────────────┐   │
│   │   deposit.blade.php │    │   manual.blade.php  │    │ redirect.blade   │   │
│   │   (Formulario)      │    │   (Pago manual)     │    │ (Redir externo)  │   │
│   └──────────┬──────────┘    └──────────┬──────────┘    └────────┬─────────┘   │
│              │                          │                         │             │
└──────────────┼──────────────────────────┼─────────────────────────┼─────────────┘
               │                          │                         │
               ▼                          ▼                         ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              CAPA DE CONTROLADORES                              │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│   ┌─────────────────────────────────────────────────────────────────────────┐   │
│   │                        PaymentController.php                            │   │
│   │  ┌─────────────┐ ┌───────────────┐ ┌────────────────┐ ┌──────────────┐  │   │
│   │  │  deposit()  │ │depositInsert()│ │depositConfirm()│ │manualUpdate()│  │   │
│   │  └─────────────┘ └───────────────┘ └────────────────┘ └──────────────┘  │   │
│   └─────────────────────────────────────────────────────────────────────────┘   │
│                                      │                                          │
│                                      ▼                                          │
│   ┌─────────────────────────────────────────────────────────────────────────┐   │
│   │                    ProcessController (por Gateway)                      │   │
│   │  ┌────────┐ ┌────────┐ ┌──────────┐ ┌─────────┐ ┌─────────┐ ┌───────┐  │   │
│   │  │ Paypal │ │ Stripe │ │ Razorpay │ │ Paystack│ │ Coinbase│ │  ...  │  │   │
│   │  └────────┘ └────────┘ └──────────┘ └─────────┘ └─────────┘ └───────┘  │   │
│   └─────────────────────────────────────────────────────────────────────────┘   │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
               │                          │                         │
               ▼                          ▼                         ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              CAPA DE MODELOS                                    │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│   ┌─────────────┐    ┌──────────────────┐    ┌─────────────┐    ┌───────────┐  │
│   │   Deposit   │◄───│  GatewayCurrency │◄───│   Gateway   │    │   User    │  │
│   │   Model     │    │      Model       │    │    Model    │    │   Model   │  │
│   └─────────────┘    └──────────────────┘    └─────────────┘    └───────────┘  │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
               │                          │                         │
               ▼                          ▼                         ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              BASE DE DATOS (MySQL)                              │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│   ┌─────────────┐    ┌──────────────────┐    ┌─────────────┐    ┌───────────┐  │
│   │  deposits   │    │gateway_currencies│    │   gateways  │    │   users   │  │
│   └─────────────┘    └──────────────────┘    └─────────────┘    └───────────┘  │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 Estructura de Archivos

```
core/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Gateway/
│   │           ├── PaymentController.php      ← Controlador principal
│   │           ├── Paypal/
│   │           │   └── ProcessController.php  ← Procesador PayPal
│   │           ├── Stripe/
│   │           │   └── ProcessController.php  ← Procesador Stripe
│   │           ├── Razorpay/
│   │           │   └── ProcessController.php
│   │           ├── Paystack/
│   │           │   └── ProcessController.php
│   │           └── ... (30+ gateways)
│   │
│   ├── Models/
│   │   ├── Deposit.php                        ← Modelo de depósitos
│   │   ├── Gateway.php                        ← Modelo de pasarelas
│   │   └── GatewayCurrency.php                ← Modelo de monedas
│   │
│   └── Constants/
│       └── Status.php                         ← Constantes de estado
│
├── resources/
│   └── views/
│       └── templates/
│           └── basic/
│               └── user/
│                   └── payment/
│                       ├── deposit.blade.php  ← Vista del formulario
│                       ├── manual.blade.php   ← Vista pago manual
│                       └── redirect.blade.php ← Redirección externa
│
└── routes/
    └── user.php                               ← Definición de rutas
```

---

## 3. Flujo de Depósito

### 3.1 Diagrama de Flujo Completo

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                         FLUJO DE DEPÓSITO COMPLETO                           │
└──────────────────────────────────────────────────────────────────────────────┘

     USUARIO                    SISTEMA                      GATEWAY EXTERNO
        │                          │                              │
        │  1. Accede a /deposit    │                              │
        │─────────────────────────▶│                              │
        │                          │                              │
        │  2. Muestra formulario   │                              │
        │◀─────────────────────────│                              │
        │                          │                              │
        │  3. Selecciona gateway   │                              │
        │     e ingresa monto      │                              │
        │─────────────────────────▶│                              │
        │                          │                              │
        │                          │  4. Valida datos             │
        │                          │  5. Calcula comisiones       │
        │                          │  6. Crea registro Deposit    │
        │                          │     (status=INITIATE)        │
        │                          │                              │
        │  7. Redirige a /confirm  │                              │
        │◀─────────────────────────│                              │
        │                          │                              │
        │                          │  8. Determina tipo gateway   │
        │                          │     ┌───────────────────┐    │
        │                          │     │ code < 1000?      │    │
        │                          │     │ → AUTOMÁTICO      │    │
        │                          │     │ code >= 1000?     │    │
        │                          │     │ → MANUAL          │    │
        │                          │     └───────────────────┘    │
        │                          │                              │
        ├──────────────────────────┼──────────────────────────────┤
        │      GATEWAY AUTOMÁTICO  │                              │
        ├──────────────────────────┼──────────────────────────────┤
        │                          │                              │
        │                          │  9. ProcessController        │
        │                          │     ::process($deposit)      │
        │                          │────────────────────────────▶ │
        │                          │                              │
        │  10. Formulario de pago  │                              │
        │      o redirección       │                              │
        │◀─────────────────────────│                              │
        │                          │                              │
        │  11. Usuario paga        │                              │
        │─────────────────────────────────────────────────────────▶
        │                          │                              │
        │                          │  12. IPN/Webhook callback    │
        │                          │◀─────────────────────────────│
        │                          │                              │
        │                          │  13. Verificar pago          │
        │                          │  14. userDataUpdate()        │
        │                          │      - status=SUCCESS        │
        │                          │      - user.balance += monto │
        │                          │      - crear Transaction     │
        │                          │      - notificar usuario     │
        │                          │                              │
        │  15. Confirmación        │                              │
        │◀─────────────────────────│                              │
        │                          │                              │
        ├──────────────────────────┼──────────────────────────────┤
        │      GATEWAY MANUAL      │                              │
        ├──────────────────────────┼──────────────────────────────┤
        │                          │                              │
        │  9. Vista manual.blade   │                              │
        │◀─────────────────────────│                              │
        │                          │                              │
        │  10. Usuario envía       │                              │
        │      comprobante         │                              │
        │─────────────────────────▶│                              │
        │                          │                              │
        │                          │  11. status=PENDING          │
        │                          │  12. Notifica admin          │
        │                          │                              │
        │  13. Confirmación        │                              │
        │◀─────────────────────────│                              │
        │                          │                              │
        │       ... ADMIN REVISA Y APRUEBA ...                    │
        │                          │                              │
        │  14. Notificación        │                              │
        │      de aprobación       │                              │
        │◀─────────────────────────│                              │
        │                          │                              │
        ▼                          ▼                              ▼
```

### 3.2 Flujo en Código

```
Paso 1: GET /deposit
    └── PaymentController@deposit()
        └── return view('deposit', compact('gatewayCurrency'))

Paso 2: POST /deposit/insert
    └── PaymentController@depositInsert()
        ├── Validar: amount, gateway, currency
        ├── Calcular: charge = fixed + (amount * percent / 100)
        ├── Calcular: payable = amount + charge
        ├── Calcular: finalAmount = payable * rate
        ├── Crear: new Deposit()
        ├── Guardar: session('Track', $trx)
        └── Redirect: /deposit/confirm

Paso 3: GET /deposit/confirm
    └── PaymentController@depositConfirm()
        ├── Si code < 1000 (automático):
        │   └── ProcessController::process($deposit)
        └── Si code >= 1000 (manual):
            └── Redirect: /deposit/manual/confirm

Paso 4: IPN/Callback
    └── Gateway\{Name}\ProcessController@ipn()
        ├── Verificar pago con gateway
        └── PaymentController::userDataUpdate($deposit)
            ├── deposit.status = SUCCESS
            ├── user.balance += amount
            ├── Crear Transaction
            ├── Procesar comisiones (opcional)
            └── Notificar usuario
```

---

## 4. Componentes del Sistema

### 4.1 Rutas (routes/user.php)

```php
// Payment Routes
Route::prefix('deposit')
    ->name('deposit.')
    ->controller('Gateway\PaymentController')
    ->group(function () {
    
        // GET /deposit - Mostrar formulario
        Route::any('/', 'deposit')->name('index');
        
        // POST /deposit/insert - Procesar formulario
        Route::post('insert', 'depositInsert')->name('insert');
        
        // GET /deposit/confirm - Confirmar con gateway
        Route::get('confirm', 'depositConfirm')->name('confirm');
        
        // GET /deposit/manual - Confirmación manual
        Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
        
        // POST /deposit/manual - Enviar comprobante
        Route::post('manual', 'manualDepositUpdate')->name('manual.update');
    });
```

### 4.2 Modelo Deposit

**Archivo:** `app/Models/Deposit.php`

```php
<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $casts = [
        'detail' => 'object'
    ];

    protected $hidden = ['detail'];

    // ═══════════════════════════════════════════════════════════
    // RELACIONES
    // ═══════════════════════════════════════════════════════════
    
    /**
     * Usuario propietario del depósito
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Gateway de pago utilizado
     */
    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }
    
    /**
     * Obtener la moneda del gateway
     */
    public function gatewayCurrency()
    {
        return GatewayCurrency::where('method_code', $this->method_code)
            ->where('currency', $this->method_currency)
            ->first();
    }

    // ═══════════════════════════════════════════════════════════
    // SCOPES (Consultas predefinidas)
    // ═══════════════════════════════════════════════════════════
    
    public function scopePending($query)
    {
        return $query->where('method_code', '>=', 1000)
                     ->where('status', Status::PAYMENT_PENDING);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', Status::PAYMENT_SUCCESS);
    }

    public function scopeInitiated($query)
    {
        return $query->where('status', Status::PAYMENT_INITIATE);
    }

    // ═══════════════════════════════════════════════════════════
    // ATRIBUTOS COMPUTADOS
    // ═══════════════════════════════════════════════════════════
    
    public function methodName()
    {
        if ($this->method_code < 5000) {
            return @$this->gatewayCurrency()->name;
        }
        return 'Google Pay';
    }

    /**
     * Badge HTML según el estado
     */
    public function statusBadge(): Attribute
    {
        return new Attribute(function() {
            switch($this->status) {
                case Status::PAYMENT_PENDING:
                    return '<span class="badge badge--warning">Pending</span>';
                case Status::PAYMENT_SUCCESS:
                    return '<span class="badge badge--success">Success</span>';
                case Status::PAYMENT_REJECT:
                    return '<span class="badge badge--danger">Rejected</span>';
                default:
                    return '<span class="badge badge--dark">Initiated</span>';
            }
        });
    }
}
```

### 4.3 Modelo Gateway

**Archivo:** `app/Models/Gateway.php`

```php
<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    use GlobalStatus;

    protected $hidden = [
        'gateway_parameters',
        'extra'
    ];

    protected $casts = [
        'code' => 'string',
        'extra' => 'object',
        'input_form' => 'object',
        'supported_currencies' => 'object'
    ];

    // ═══════════════════════════════════════════════════════════
    // RELACIONES
    // ═══════════════════════════════════════════════════════════
    
    /**
     * Monedas soportadas por este gateway
     */
    public function currencies()
    {
        return $this->hasMany(GatewayCurrency::class, 'method_code', 'code');
    }

    /**
     * Formulario personalizado (para gateways manuales)
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    // ═══════════════════════════════════════════════════════════
    // SCOPES
    // ═══════════════════════════════════════════════════════════
    
    /**
     * Gateways automáticos (código < 1000)
     */
    public function scopeAutomatic($query)
    {
        return $query->where('code', '<', 1000);
    }

    /**
     * Gateways manuales (código >= 1000)
     */
    public function scopeManual($query)
    {
        return $query->where('code', '>=', 1000);
    }

    /**
     * Tipo: crypto o fiat
     */
    public function scopeCrypto()
    {
        return $this->crypto == Status::ENABLE ? 'crypto' : 'fiat';
    }
}
```

### 4.4 Modelo GatewayCurrency

**Archivo:** `app/Models/GatewayCurrency.php`

```php
<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class GatewayCurrency extends Model
{
    protected $hidden = [
        'gateway_parameter'  // API keys, secrets, etc.
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // ═══════════════════════════════════════════════════════════
    // RELACIONES
    // ═══════════════════════════════════════════════════════════
    
    /**
     * Gateway padre
     */
    public function method()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    // ═══════════════════════════════════════════════════════════
    // MÉTODOS
    // ═══════════════════════════════════════════════════════════
    
    public function currencyIdentifier()
    {
        return $this->name ?? $this->method->name . ' ' . $this->currency;
    }

    public function scopeBaseCurrency()
    {
        return $this->method->crypto == Status::ENABLE ? 'USD' : $this->currency;
    }
}
```

### 4.5 Constantes de Estado

**Archivo:** `app/Constants/Status.php`

```php
<?php

namespace App\Constants;

class Status
{
    // ═══════════════════════════════════════════════════════════
    // ESTADOS GENERALES
    // ═══════════════════════════════════════════════════════════
    
    const ENABLE = 1;
    const DISABLE = 0;
    
    const YES = 1;
    const NO = 0;

    // ═══════════════════════════════════════════════════════════
    // ESTADOS DE PAGO/DEPÓSITO
    // ═══════════════════════════════════════════════════════════
    
    const PAYMENT_INITIATE = 0;  // Depósito iniciado
    const PAYMENT_SUCCESS = 1;   // Pago exitoso
    const PAYMENT_PENDING = 2;   // Pendiente (manual)
    const PAYMENT_REJECT = 3;    // Rechazado

    // ═══════════════════════════════════════════════════════════
    // CÓDIGOS ESPECIALES DE GATEWAY
    // ═══════════════════════════════════════════════════════════
    
    const GOOGLE_PAY = 5001;
    
    // Nota sobre códigos:
    // - code < 1000: Gateway automático
    // - code >= 1000 && < 5000: Gateway manual
    // - code >= 5000: Gateways especiales (Google Pay, etc.)
}
```

---

## 5. Base de Datos

### 5.1 Tabla `deposits`

```sql
CREATE TABLE `deposits` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    
    -- Relaciones
    `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
    `user_pick_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
    `method_code` int(10) UNSIGNED NOT NULL DEFAULT 0,
    
    -- Montos
    `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,        -- Monto original
    `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,        -- Comisión
    `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,          -- Tasa de cambio
    `final_amo` decimal(28,8) NOT NULL DEFAULT 0.00000000,     -- Monto final
    
    -- Moneda
    `method_currency` varchar(40) DEFAULT NULL,
    
    -- Bitcoin (si aplica)
    `btc_amo` varchar(255) DEFAULT NULL,
    `btc_wallet` varchar(255) DEFAULT NULL,
    
    -- Tracking
    `trx` varchar(40) DEFAULT NULL,                            -- Código único
    `payment_try` int(10) NOT NULL DEFAULT 0,
    
    -- Estado
    `status` tinyint(1) NOT NULL DEFAULT 0,                    -- 0=init, 1=success, 2=pending, 3=reject
    
    -- Detalles adicionales
    `detail` text DEFAULT NULL,                                -- JSON con info del pago
    `from_api` tinyint(1) NOT NULL DEFAULT 0,
    `admin_feedback` varchar(255) DEFAULT NULL,
    
    -- Timestamps
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `deposits_user_id_index` (`user_id`),
    KEY `deposits_method_code_index` (`method_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5.2 Tabla `gateways`

```sql
CREATE TABLE `gateways` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `form_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
    `code` int NOT NULL,                                       -- Código único del gateway
    `name` varchar(40) DEFAULT NULL,                           -- Nombre visible
    `alias` varchar(40) NOT NULL,                              -- Alias para carpeta
    `status` tinyint(1) NOT NULL DEFAULT 1,                    -- Activo/Inactivo
    `gateway_parameters` text DEFAULT NULL,                    -- JSON con credenciales
    `supported_currencies` text DEFAULT NULL,                  -- JSON con monedas
    `crypto` tinyint(1) NOT NULL DEFAULT 0,                    -- Es criptomoneda?
    `extra` text DEFAULT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `gateways_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5.3 Tabla `gateway_currencies`

```sql
CREATE TABLE `gateway_currencies` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(40) DEFAULT NULL,                           -- Nombre con moneda
    `currency` varchar(40) DEFAULT NULL,                       -- Código moneda (USD, EUR)
    `symbol` varchar(40) DEFAULT NULL,                         -- Símbolo ($, €)
    `method_code` int NOT NULL,                                -- FK a gateways.code
    
    -- Límites
    `min_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
    `max_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
    
    -- Comisiones
    `fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
    `percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
    
    -- Conversión
    `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
    
    -- Configuración del gateway
    `gateway_parameter` text DEFAULT NULL,                     -- JSON con API keys
    
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `gateway_currencies_method_code_index` (`method_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5.4 Diagrama Entidad-Relación

```
┌─────────────────┐       ┌─────────────────────┐       ┌─────────────────┐
│     users       │       │      deposits       │       │    gateways     │
├─────────────────┤       ├─────────────────────┤       ├─────────────────┤
│ PK id           │◄──────│ FK user_id          │       │ PK id           │
│    username     │       │ PK id               │       │ UK code         │◄──┐
│    email        │       │    method_code      │───────│    name         │   │
│    balance      │       │    amount           │       │    alias        │   │
│    ...          │       │    charge           │       │    status       │   │
└─────────────────┘       │    rate             │       │    crypto       │   │
                          │    final_amo        │       │    ...          │   │
                          │    status           │       └─────────────────┘   │
                          │    trx              │                             │
                          │    ...              │                             │
                          └─────────────────────┘                             │
                                                                              │
                          ┌─────────────────────┐                             │
                          │ gateway_currencies  │                             │
                          ├─────────────────────┤                             │
                          │ PK id               │                             │
                          │ FK method_code      │─────────────────────────────┘
                          │    currency         │
                          │    min_amount       │
                          │    max_amount       │
                          │    fixed_charge     │
                          │    percent_charge   │
                          │    rate             │
                          │    ...              │
                          └─────────────────────┘
```

---

## 6. Formulario de Depósito (Frontend)

### 6.1 Estructura Visual

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                                                                                 │
│   Purchase [MONEDA]                                          [ History → ]      │
│                                                                                 │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│   ┌─────────────────────────────────┐   ┌─────────────────────────────────────┐ │
│   │                                 │   │                                     │ │
│   │   MÉTODOS DE PAGO               │   │   INFORMACIÓN DEL DEPÓSITO          │ │
│   │   ═══════════════════════════   │   │   ═══════════════════════════════   │ │
│   │                                 │   │                                     │ │
│   │   ┌─────────────────────────┐   │   │   Amount                            │ │
│   │   │ ○ PayPal      [logo]    │   │   │   ┌─────────────────────────────┐   │ │
│   │   └─────────────────────────┘   │   │   │  $  │  [_______________]    │   │ │
│   │   ┌─────────────────────────┐   │   │   └─────────────────────────────┘   │ │
│   │   │ ● Stripe      [logo]    │   │   │                                     │ │
│   │   └─────────────────────────┘   │   │   ───────────────────────────────   │ │
│   │   ┌─────────────────────────┐   │   │                                     │ │
│   │   │ ○ Paystack    [logo]    │   │   │   Limit:              $10 - $5000   │ │
│   │   └─────────────────────────┘   │   │                                     │ │
│   │   ┌─────────────────────────┐   │   │   Processing Charge:  $2.50 USD     │ │
│   │   │ ○ Flutterwave [logo]    │   │   │   ℹ️ 2.5% + $0.50 fixed             │ │
│   │   └─────────────────────────┘   │   │                                     │ │
│   │   ┌─────────────────────────┐   │   │   ───────────────────────────────   │ │
│   │   │ ○ Razorpay    [logo]    │   │   │                                     │ │
│   │   └─────────────────────────┘   │   │   Total:              $102.50 USD   │ │
│   │                                 │   │                                     │ │
│   │   [  Show All Payment Options ] │   │   Conversion:   1 USD = 0.92 EUR    │ │
│   │                                 │   │   In EUR:             €94.30        │ │
│   │                                 │   │                                     │ │
│   └─────────────────────────────────┘   │   ┌─────────────────────────────┐   │ │
│                                         │   │      Purchase Confirm       │   │ │
│                                         │   └─────────────────────────────┘   │ │
│                                         │                                     │ │
│                                         │   ℹ️ Ensuring your funds grow       │ │
│                                         │   safely through our secure         │ │
│                                         │   deposit process...                │ │
│                                         │                                     │ │
│                                         └─────────────────────────────────────┘ │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
```

### 6.2 Código del Formulario (Blade)

**Archivo:** `resources/views/templates/basic/user/payment/deposit.blade.php`

```html
@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- HEADER -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">@lang('Purchase') {{ __(gs('cur_text')) }}</h4>
                <a class="btn btn--secondary" href="{{ route('user.deposit.history') }}">
                    @lang('History') <i class="las la-long-arrow-alt-right"></i>
                </a>
            </div>
            
            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- FORMULARIO PRINCIPAL -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <form action="{{ route('user.deposit.insert') }}" method="post" class="deposit-form">
                @csrf
                <input type="hidden" name="currency">
                
                <div class="gateway-card">
                    <div class="row justify-content-center gy-sm-4 gy-3">
                        
                        <!-- ═══════════════════════════════════════════ -->
                        <!-- COLUMNA IZQUIERDA: Lista de Gateways -->
                        <!-- ═══════════════════════════════════════════ -->
                        <div class="col-lg-6">
                            <div class="payment-system-list gateway-option-list">
                                
                                @foreach ($gatewayCurrency as $data)
                                <label for="{{ titleToKey($data->name) }}" 
                                       class="payment-item gateway-option 
                                              @if ($loop->index > 4) d-none @endif">
                                    
                                    <div class="payment-item__info">
                                        <span class="payment-item__check"></span>
                                        <span class="payment-item__name">
                                            {{ __($data->name) }}
                                        </span>
                                    </div>
                                    
                                    <div class="payment-item__thumb">
                                        <img src="{{ getImage(getFilePath('gateway').'/'.$data->method->image) }}" 
                                             alt="@lang('payment-thumb')">
                                    </div>
                                    
                                    <!-- Input Radio con data attributes -->
                                    <input class="payment-item__radio gateway-input" 
                                           id="{{ titleToKey($data->name) }}" 
                                           type="radio" 
                                           name="gateway" 
                                           value="{{ $data->method_code }}"
                                           hidden
                                           data-gateway='@json($data)'
                                           data-min-amount="{{ showAmount($data->min_amount) }}"
                                           data-max-amount="{{ showAmount($data->max_amount) }}"
                                           @checked($loop->first)>
                                </label>
                                @endforeach
                                
                                <!-- Botón para mostrar más opciones -->
                                @if ($gatewayCurrency->count() > 4)
                                <button type="button" class="payment-item__btn more-gateway-option">
                                    <p>@lang('Show All Payment Options')</p>
                                    <span><i class="fas fa-chevron-down"></i></span>
                                </button>
                                @endif
                                
                            </div>
                        </div>
                        
                        <!-- ═══════════════════════════════════════════ -->
                        <!-- COLUMNA DERECHA: Información del Depósito -->
                        <!-- ═══════════════════════════════════════════ -->
                        <div class="col-lg-6">
                            <div class="payment-system-list p-3">
                                
                                <!-- Campo de Monto -->
                                <div class="deposit-info">
                                    <p class="text mb-0">@lang('Amount')</p>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input type="text" 
                                               class="form-control amount" 
                                               name="amount"
                                               placeholder="@lang('00.00')" 
                                               value="{{ old('amount') }}" 
                                               autocomplete="off">
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <!-- Límites -->
                                <div class="deposit-info">
                                    <p class="text">@lang('Limit')</p>
                                    <p class="text"><span class="gateway-limit">@lang('0.00')</span></p>
                                </div>
                                
                                <!-- Comisión -->
                                <div class="deposit-info">
                                    <p class="text">@lang('Processing Charge')
                                        <span class="proccessing-fee-info" 
                                              data-bs-toggle="tooltip" 
                                              title="Info">
                                            <i class="las la-info-circle"></i>
                                        </span>
                                    </p>
                                    <p class="text">
                                        <span class="processing-fee">@lang('0.00')</span>
                                        {{ __(gs('cur_text')) }}
                                    </p>
                                </div>
                                
                                <!-- Total -->
                                <div class="deposit-info total-amount pt-3">
                                    <p class="text">@lang('Total')</p>
                                    <p class="text">
                                        <span class="final-amount">@lang('0.00')</span>
                                        {{ __(gs('cur_text')) }}
                                    </p>
                                </div>
                                
                                <!-- Conversión (si aplica) -->
                                <div class="deposit-info gateway-conversion d-none">
                                    <p class="text">@lang('Conversion')</p>
                                    <p class="text"></p>
                                </div>
                                
                                <div class="deposit-info conversion-currency d-none">
                                    <p class="text">
                                        @lang('In') <span class="gateway-currency"></span>
                                    </p>
                                    <p class="text"><span class="in-currency"></span></p>
                                </div>
                                
                                <!-- Botón Submit -->
                                <div class="mt-3">
                                    <button type="submit" class="btn btn--base w-100" disabled>
                                        @lang('Purchase Confirm')
                                    </button>
                                    <p class="text pt-3">
                                        @lang('Ensuring your funds grow safely...')
                                    </p>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection
```

### 6.3 JavaScript del Formulario

```javascript
"use strict";
(function($) {
    
    var amount = parseFloat($('.amount').val() || 0);
    var gateway, minAmount, maxAmount;
    
    // ═══════════════════════════════════════════════════════════════════
    // EVENT: Cambio en el monto
    // ═══════════════════════════════════════════════════════════════════
    $('.amount').on('input', function(e) {
        amount = parseFloat($(this).val()) || 0;
        calculation();
    });
    
    // ═══════════════════════════════════════════════════════════════════
    // EVENT: Cambio de gateway
    // ═══════════════════════════════════════════════════════════════════
    $('.gateway-input').on('change', function(e) {
        gatewayChange();
    });
    
    // ═══════════════════════════════════════════════════════════════════
    // FUNCIÓN: Procesar cambio de gateway
    // ═══════════════════════════════════════════════════════════════════
    function gatewayChange() {
        let gatewayElement = $('.gateway-input:checked');
        
        gateway = gatewayElement.data('gateway');
        minAmount = gatewayElement.data('min-amount');
        maxAmount = gatewayElement.data('max-amount');
        
        // Actualizar tooltip de comisiones
        let processingFeeInfo = `${gateway.percent_charge}% + ${gateway.fixed_charge} charge`;
        $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
        
        calculation();
    }
    
    // ═══════════════════════════════════════════════════════════════════
    // FUNCIÓN: Cálculo de montos
    // ═══════════════════════════════════════════════════════════════════
    function calculation() {
        if (!gateway) return;
        
        // Mostrar límites
        $(".gateway-limit").text(minAmount + " - " + maxAmount);
        
        // Calcular comisiones
        let percentCharge = parseFloat(gateway.percent_charge) || 0;
        let fixedCharge = parseFloat(gateway.fixed_charge) || 0;
        let totalPercentCharge = amount / 100 * percentCharge;
        let totalCharge = totalPercentCharge + fixedCharge;
        
        // Calcular total
        let totalAmount = amount + totalCharge;
        
        // Actualizar UI
        $(".final-amount").text(totalAmount.toFixed(2));
        $(".processing-fee").text(totalCharge.toFixed(2));
        $("input[name=currency]").val(gateway.currency);
        $(".gateway-currency").text(gateway.currency);
        
        // Validar límites y habilitar/deshabilitar botón
        if (amount < gateway.min_amount || amount > gateway.max_amount) {
            $(".deposit-form button[type=submit]").attr('disabled', true);
        } else {
            $(".deposit-form button[type=submit]").removeAttr('disabled');
        }
        
        // Mostrar conversión si es diferente moneda
        if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
            $(".gateway-conversion, .conversion-currency").removeClass('d-none');
            $(".gateway-conversion .text:last").html(
                `1 {{ gs('cur_text') }} = ${gateway.rate} ${gateway.currency}`
            );
            $('.in-currency').text((totalAmount * gateway.rate).toFixed(2));
        } else {
            $(".gateway-conversion, .conversion-currency").addClass('d-none');
        }
    }
    
    // ═══════════════════════════════════════════════════════════════════
    // EVENT: Mostrar más opciones de pago
    // ═══════════════════════════════════════════════════════════════════
    $(".more-gateway-option").on("click", function(e) {
        $(".gateway-option-list .gateway-option").removeClass("d-none");
        $(this).addClass('d-none');
    });
    
    // Inicializar
    gatewayChange();
    
})(jQuery);
```

---

## 7. Controlador Principal

### 7.1 PaymentController Completo

**Archivo:** `app/Http/Controllers/Gateway/PaymentController.php`

```php
<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // ═══════════════════════════════════════════════════════════════════════════
    // PASO 1: Mostrar formulario de depósito
    // ═══════════════════════════════════════════════════════════════════════════
    
    /**
     * Muestra el formulario de depósito con todas las pasarelas activas
     * 
     * @return \Illuminate\View\View
     */
    public function deposit()
    {
        // Obtener todas las monedas de gateways activos
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        
        $pageTitle = 'Purchase ' . gs('cur_text');

        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PASO 2: Procesar el formulario
    // ═══════════════════════════════════════════════════════════════════════════
    
    /**
     * Valida y crea el registro de depósito
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function depositInsert(Request $request)
    {
        // Validar datos del formulario
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'gateway' => 'required',
            'currency' => 'required',
        ]);

        $user = auth()->user();
        
        // Obtener el gateway seleccionado
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)
          ->where('currency', $request->currency)
          ->first();
        
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        // Validar límites
        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        // ═══════════════════════════════════════════════════════════════════
        // CÁLCULO DE COMISIONES
        // ═══════════════════════════════════════════════════════════════════
        
        // Comisión = fija + (monto × porcentaje / 100)
        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        
        // Monto a pagar = monto + comisión
        $payable = $request->amount + $charge;
        
        // Monto final en moneda del gateway = monto_a_pagar × tasa
        $finalAmount = $payable * $gate->rate;

        // ═══════════════════════════════════════════════════════════════════
        // CREAR REGISTRO DE DEPÓSITO
        // ═══════════════════════════════════════════════════════════════════
        
        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();  // Genera código único
        $data->success_url = urlPath('user.deposit.history');
        $data->failed_url = urlPath('user.deposit.history');
        $data->save();
        
        // Guardar código de seguimiento en sesión
        session()->put('Track', $data->trx);
        
        return to_route('user.deposit.confirm');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PASO 3: Confirmar depósito
    // ═══════════════════════════════════════════════════════════════════════════
    
    /**
     * Procesa el depósito con el gateway correspondiente
     * 
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function depositConfirm()
    {
        $track = session()->get('Track');
        
        // Obtener el depósito iniciado
        $deposit = Deposit::where('trx', $track)
            ->where('status', Status::PAYMENT_INITIATE)
            ->orderBy('id', 'DESC')
            ->with('gateway')
            ->firstOrFail();

        // Si es gateway manual (código >= 1000)
        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }

        // ═══════════════════════════════════════════════════════════════════
        // GATEWAY AUTOMÁTICO: Ejecutar ProcessController específico
        // ═══════════════════════════════════════════════════════════════════
        
        $dirName = $deposit->gateway->alias;  // Ej: "Paypal", "Stripe"
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        // Llamar al método process() del gateway
        $data = $new::process($deposit);
        $data = json_decode($data);

        // Manejar errores
        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        
        // Si requiere redirección externa
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // Para Stripe V3: guardar session ID
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PASO 4: Actualizar datos del usuario tras pago exitoso
    // ═══════════════════════════════════════════════════════════════════════════
    
    /**
     * Método estático llamado por los ProcessController de cada gateway
     * cuando el pago es exitoso
     * 
     * @param Deposit $deposit
     * @param bool $isManual
     */
    public static function userDataUpdate($deposit, $isManual = null)
    {
        // Solo procesar si está en estado INITIATE o PENDING
        if ($deposit->status == Status::PAYMENT_INITIATE || 
            $deposit->status == Status::PAYMENT_PENDING) {
            
            // ═══════════════════════════════════════════════════════════════
            // 1. Actualizar estado del depósito
            // ═══════════════════════════════════════════════════════════════
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            // ═══════════════════════════════════════════════════════════════
            // 2. Actualizar balance del usuario
            // ═══════════════════════════════════════════════════════════════
            $user = User::find($deposit->user_id);
            $user->balance += $deposit->amount;
            $user->save();

            // ═══════════════════════════════════════════════════════════════
            // 3. Crear registro de transacción
            // ═══════════════════════════════════════════════════════════════
            $methodName = $deposit->methodName();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Payment via ' . $methodName;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'payment';
            $transaction->save();

            // ═══════════════════════════════════════════════════════════════
            // 4. Procesar comisiones por referido (si está activo)
            // ═══════════════════════════════════════════════════════════════
            if (gs('deposit_commission')) {
                levelCommission($user, $deposit->amount, 'deposit_commission', $deposit->trx);
            }

            // ═══════════════════════════════════════════════════════════════
            // 5. Notificar al administrador
            // ═══════════════════════════════════════════════════════════════
            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Payment successful via ' . $methodName;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            // ═══════════════════════════════════════════════════════════════
            // 6. Notificar al usuario
            // ═══════════════════════════════════════════════════════════════
            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
                'amount' => showAmount($deposit->amount, currencyFormat: false),
                'charge' => showAmount($deposit->charge, currencyFormat: false),
                'rate' => showAmount($deposit->rate, currencyFormat: false),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // GATEWAY MANUAL: Confirmación
    // ═══════════════════════════════════════════════════════════════════════════
    
    /**
     * Muestra el formulario de pago manual
     */
    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')
            ->where('status', Status::PAYMENT_INITIATE)
            ->where('trx', $track)
            ->first();
        
        abort_if(!$data, 404);
        
        if ($data->method_code > 999) {
            $pageTitle = 'Payment Confirm';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            
            return view('Template::user.payment.manual', 
                compact('data', 'pageTitle', 'method', 'gateway'));
        }
        
        abort(404);
    }

    /**
     * Procesa el envío del comprobante manual
     */
    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')
            ->where('status', Status::PAYMENT_INITIATE)
            ->where('trx', $track)
            ->first();
        
        abort_if(!$data, 404);
        
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        // Procesar formulario dinámico
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        // Guardar datos y cambiar estado a PENDING
        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        // Notificar al administrador
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Payment request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        // Notificar al usuario
        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount, currencyFormat: false),
            'amount' => showAmount($data->amount, currencyFormat: false),
            'charge' => showAmount($data->charge, currencyFormat: false),
            'rate' => showAmount($data->rate, currencyFormat: false),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'Your payment request has been submitted'];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}
```

---

## 8. Pasarelas de Pago

### 8.1 Lista de Gateways Disponibles (30+)

| Código | Gateway | Tipo | Región Principal |
|--------|---------|------|------------------|
| 101 | PayPal | Automático | Global |
| 102 | Perfect Money | Automático | Global |
| 103 | Stripe Hosted | Automático | Global |
| 104 | Skrill | Automático | Europa |
| 105 | PayTM | Automático | India |
| 106 | Payeer | Automático | Rusia/CIS |
| 107 | Paystack | Automático | África |
| 108 | VoguePay | Automático | Nigeria |
| 109 | Flutterwave | Automático | África |
| 110 | Razorpay | Automático | India |
| 111 | Instamojo | Automático | India |
| 112 | Mollie | Automático | Europa |
| 113 | Authorize.net | Automático | USA |
| 114 | 2Checkout | Automático | Global |
| 115 | Stripe JS | Automático | Global |
| 116 | Aamarpay | Automático | Bangladesh |
| 117 | SSL Commerz | Automático | Bangladesh |
| 118 | Checkout.com | Automático | Global |
| 119 | NMI | Automático | USA |
| 120 | Stripe V3 | Automático | Global |
| 501 | Blockchain | Crypto | Global |
| 502 | Coinpayments | Crypto | Global |
| 503 | Coinpayments Fiat | Crypto | Global |
| 504 | Coingate | Crypto | Global |
| 505 | Coinbase Commerce | Crypto | Global |
| 506 | NowPayments Hosted | Crypto | Global |
| 507 | NowPayments Checkout | Crypto | Global |
| 508 | BTCPay | Crypto | Global |
| 509 | Binance | Crypto | Global |
| 1000+ | Manual | Manual | Configurable |

### 8.2 Ejemplo: ProcessController de Stripe

**Archivo:** `app/Http/Controllers/Gateway/Stripe/ProcessController.php`

```php
<?php

namespace App\Http\Controllers\Gateway\Stripe;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;

class ProcessController extends Controller
{
    /**
     * Prepara los datos para mostrar el formulario de tarjeta
     * 
     * @param Deposit $deposit
     * @return string JSON con configuración de la vista
     */
    public static function process($deposit)
    {
        $alias = $deposit->gateway->alias;

        $send['track'] = $deposit->trx;
        $send['view'] = 'user.payment.' . $alias;  // Vista del formulario de tarjeta
        $send['method'] = 'post';
        $send['url'] = route('ipn.' . $alias);     // URL para procesar el pago
        
        return json_encode($send);
    }

    /**
     * Procesa el pago con tarjeta
     * Llamado cuando el usuario envía el formulario con los datos de tarjeta
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ipn(Request $request)
    {
        $track = Session::get('Track');
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        
        // Verificar que no esté ya procesado
        if ($deposit->status == Status::PAYMENT_SUCCESS) {
            $notify[] = ['error', 'Invalid request.'];
            return redirect($deposit->failed_url)->withNotify($notify);
        }
        
        // Validar datos de la tarjeta
        $request->validate([
            'cardNumber' => 'required',
            'cardExpiry' => 'required',
            'cardCVC' => 'required',
        ]);

        // Extraer datos de la tarjeta
        $cc = $request->cardNumber;
        $exp = explode("/", $request->cardExpiry);
        $cvc = $request->cardCVC;
        
        $emo = trim($exp[0]);  // Mes
        $eyr = trim($exp[1]);  // Año
        
        // Convertir a centavos
        $cents = round($deposit->final_amount, 2) * 100;

        // Obtener credenciales de Stripe
        $stripeAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        Stripe::setApiKey($stripeAcc->secret_key);
        Stripe::setApiVersion("2020-03-02");

        try {
            // Crear token de tarjeta
            $token = Token::create([
                "card" => [
                    "number" => $cc,
                    "exp_month" => $emo,
                    "exp_year" => $eyr,
                    "cvc" => $cvc
                ]
            ]);
            
            // Realizar el cargo
            $charge = Charge::create([
                'card' => $token['id'],
                'currency' => $deposit->method_currency,
                'amount' => $cents,
                'description' => 'Payment to ' . gs('site_name'),
            ]);

            // Si el pago fue exitoso
            if ($charge['status'] == 'succeeded') {
                PaymentController::userDataUpdate($deposit);
                $notify[] = ['success', 'Payment captured successfully'];
                return redirect($deposit->success_url)->withNotify($notify);
            }
            
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
        }

        return back()->withNotify($notify);
    }
}
```

### 8.3 Ejemplo: ProcessController de PayPal

**Archivo:** `app/Http/Controllers/Gateway/Paypal/ProcessController.php`

```php
<?php

namespace App\Http\Controllers\Gateway\Paypal;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;

class ProcessController extends Controller
{
    /**
     * Prepara la redirección a PayPal
     */
    public static function process($deposit)
    {
        $general = gs();
        $paypalAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        
        // Parámetros para PayPal
        $val['cmd'] = '_xclick';
        $val['business'] = trim($paypalAcc->paypal_email);
        $val['cbt'] = $general->site_name;
        $val['currency_code'] = $deposit->method_currency;
        $val['quantity'] = 1;
        $val['item_name'] = "Payment To $general->site_name Account";
        $val['custom'] = $deposit->trx;  // ID para tracking
        $val['amount'] = round($deposit->final_amount, 2);
        $val['return'] = route('home') . $deposit->success_url;
        $val['cancel_return'] = route('home') . $deposit->failed_url;
        $val['notify_url'] = route('ipn.' . $deposit->gateway->alias);  // IPN URL
        
        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url'] = 'https://www.paypal.com/cgi-bin/webscr';
        
        return json_encode($send);
    }

    /**
     * IPN (Instant Payment Notification) de PayPal
     * PayPal llama a esta URL cuando el pago se completa
     */
    public function ipn()
    {
        // Leer datos del POST de PayPal
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        
        $myPost = [];
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }

        // Verificar con PayPal
        $req = 'cmd=_notify-validate';
        foreach ($myPost as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
            $details[$key] = $value;
        }

        $paypalURL = "https://ipnpb.paypal.com/cgi-bin/webscr?";
        $response = CurlRequest::curlContent($paypalURL . $req);

        // Si PayPal verifica el pago
        if ($response == "VERIFIED") {
            $deposit = Deposit::where('trx', $_POST['custom'])
                ->orderBy('id', 'DESC')
                ->first();
            
            $deposit->detail = $details;
            $deposit->save();

            // Verificar monto y estado
            if ($_POST['mc_gross'] == round($deposit->final_amount, 2) && 
                $deposit->status == Status::PAYMENT_INITIATE) {
                PaymentController::userDataUpdate($deposit);
            }
        }
    }
}
```

---

## 9. Estados del Depósito

### 9.1 Diagrama de Estados

```
                                    ┌─────────────────┐
                                    │     INICIO      │
                                    └────────┬────────┘
                                             │
                                             ▼
                               ┌─────────────────────────┐
                               │   PAYMENT_INITIATE (0)  │
                               │   "Depósito Iniciado"   │
                               └─────────────┬───────────┘
                                             │
                    ┌────────────────────────┼────────────────────────┐
                    │                        │                        │
                    ▼                        ▼                        ▼
        ┌───────────────────┐    ┌───────────────────┐    ┌───────────────────┐
        │ Gateway Automático│    │   Gateway Manual  │    │      Timeout      │
        │   pago exitoso    │    │  envía comprobante│    │    o abandono     │
        └─────────┬─────────┘    └─────────┬─────────┘    └─────────┬─────────┘
                  │                        │                        │
                  │                        ▼                        │
                  │            ┌───────────────────────┐            │
                  │            │  PAYMENT_PENDING (2)  │            │
                  │            │ "Pendiente Revisión"  │            │
                  │            └─────────────┬─────────┘            │
                  │                          │                      │
                  │               ┌──────────┴──────────┐           │
                  │               │                     │           │
                  │               ▼                     ▼           │
                  │     ┌─────────────────┐   ┌─────────────────┐   │
                  │     │ Admin Aprueba   │   │ Admin Rechaza   │   │
                  │     └────────┬────────┘   └────────┬────────┘   │
                  │              │                     │            │
                  ▼              ▼                     ▼            │
        ┌───────────────────────────┐       ┌───────────────────┐   │
        │   PAYMENT_SUCCESS (1)     │       │ PAYMENT_REJECT (3)│   │
        │   "Pago Exitoso"          │       │ "Pago Rechazado"  │   │
        │                           │       │                   │   │
        │ • user.balance += amount  │       │ • admin_feedback  │   │
        │ • crear Transaction       │       │ • notificar user  │   │
        │ • notificar usuario       │       │                   │   │
        │ • comisiones referidos    │       │                   │   │
        └───────────────────────────┘       └───────────────────┘   │
                                                                    │
                                            ┌───────────────────────┘
                                            │
                                            ▼
                                    (Registro permanece
                                     en INITIATE - sin acción)
```

### 9.2 Tabla de Estados

| Código | Constante | Descripción | Acción del Sistema |
|--------|-----------|-------------|-------------------|
| 0 | `PAYMENT_INITIATE` | Depósito creado, esperando pago | Ninguna |
| 1 | `PAYMENT_SUCCESS` | Pago completado exitosamente | Balance actualizado, transacción creada |
| 2 | `PAYMENT_PENDING` | Pago manual pendiente de revisión | Esperando aprobación admin |
| 3 | `PAYMENT_REJECT` | Pago rechazado por admin | Notificación al usuario |

---

## 10. Configuración y Personalización

### 10.1 Configurar un Gateway en el Admin Panel

```
Admin → Payment Gateway → Automatic Gateway → [Seleccionar Gateway]

┌─────────────────────────────────────────────────────────────────────┐
│                    STRIPE CONFIGURATION                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Status: [●] Enable  [○] Disable                                    │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ Publishable Key:                                             │   │
│  │ [pk_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx________________]      │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ Secret Key:                                                  │   │
│  │ [sk_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx________________]      │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════   │
│                                                                     │
│  Currency: [USD ▼]  [+ Add Currency]                               │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ USD Configuration                                            │   │
│  │                                                              │   │
│  │ Minimum Amount:     [10.00_____]                             │   │
│  │ Maximum Amount:     [5000.00___]                             │   │
│  │                                                              │   │
│  │ Fixed Charge:       [0.50______]                             │   │
│  │ Percent Charge:     [2.5_______] %                           │   │
│  │                                                              │   │
│  │ Exchange Rate:      [1.00______]                             │   │
│  │ (1 USD = X Site Currency)                                    │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  [              💾 Save Configuration              ]                │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 10.2 Fórmula de Cálculo de Comisiones

```
┌─────────────────────────────────────────────────────────────────┐
│                  CÁLCULO DE COMISIONES                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Datos de entrada:                                              │
│  ─────────────────                                              │
│  • amount          = Monto ingresado por usuario                │
│  • fixed_charge    = Comisión fija del gateway                  │
│  • percent_charge  = Comisión porcentual del gateway            │
│  • rate            = Tasa de cambio a moneda del gateway        │
│                                                                 │
│  Fórmulas:                                                      │
│  ─────────                                                      │
│                                                                 │
│  charge = fixed_charge + (amount × percent_charge / 100)        │
│                                                                 │
│  payable = amount + charge                                      │
│                                                                 │
│  final_amount = payable × rate                                  │
│                                                                 │
│  Ejemplo:                                                       │
│  ────────                                                       │
│  • amount = $100.00                                             │
│  • fixed_charge = $0.50                                         │
│  • percent_charge = 2.5%                                        │
│  • rate = 0.92 (USD → EUR)                                      │
│                                                                 │
│  charge = $0.50 + ($100.00 × 2.5 / 100)                         │
│         = $0.50 + $2.50                                         │
│         = $3.00                                                 │
│                                                                 │
│  payable = $100.00 + $3.00                                      │
│          = $103.00                                              │
│                                                                 │
│  final_amount = $103.00 × 0.92                                  │
│               = €94.76                                          │
│                                                                 │
│  ✅ Usuario paga: €94.76                                        │
│  ✅ Usuario recibe en balance: $100.00                          │
│  ✅ Comisión total: $3.00                                       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 10.3 Agregar un Nuevo Gateway Manual

```
Admin → Payment Gateway → Manual Gateway → Add New

┌─────────────────────────────────────────────────────────────────┐
│                   NEW MANUAL GATEWAY                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Gateway Name:    [Bank Transfer__________________________]     │
│                                                                 │
│  Currency:        [USD ▼]                                       │
│                                                                 │
│  Rate:            [1.00___] (1 USD = X Site Currency)           │
│                                                                 │
│  Minimum:         [50.00__]                                     │
│  Maximum:         [10000.0]                                     │
│                                                                 │
│  Fixed Charge:    [5.00___]                                     │
│  Percent Charge:  [0______] %                                   │
│                                                                 │
│  ─────────────────────────────────────────────────────────────  │
│                                                                 │
│  Instructions (shown to user):                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ Please transfer the exact amount to:                     │   │
│  │                                                          │   │
│  │ Bank: First National Bank                                │   │
│  │ Account: 1234567890                                      │   │
│  │ Routing: 021000021                                       │   │
│  │ Name: RaffleLab LLC                                      │   │
│  │                                                          │   │
│  │ Include your username in the reference.                  │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  ─────────────────────────────────────────────────────────────  │
│                                                                 │
│  Required Information from User:                                │
│                                                                 │
│  [+ Add Field]                                                  │
│                                                                 │
│  1. [Transaction ID    ] Type: [Text     ▼] Required: [✓]      │
│  2. [Screenshot        ] Type: [File     ▼] Required: [✓]      │
│  3. [Sender Name       ] Type: [Text     ▼] Required: [○]      │
│                                                                 │
│  [              💾 Create Gateway              ]                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Apéndice A: Helpers Utilizados

```php
/**
 * gs($key = null) - Obtener configuración general del sitio
 * Ejemplo: gs('cur_text') → 'USD'
 */

/**
 * getTrx() - Genera código de transacción único
 * Ejemplo: 'TRX123456789'
 */

/**
 * showAmount($amount, $currencyFormat = true) - Formatea montos
 * Ejemplo: showAmount(100.5) → '$100.50'
 */

/**
 * getFilePath($key) - Obtiene ruta de archivos
 * Ejemplo: getFilePath('gateway') → 'assets/images/gateway'
 */

/**
 * getImage($path) - Genera URL de imagen
 * Ejemplo: getImage('assets/images/gateway/paypal.png')
 */

/**
 * notify($user, $templateName, $data) - Envía notificación
 * Envía email/SMS basado en plantillas configuradas
 */

/**
 * urlPath($routeName, $params = []) - Genera path de URL
 * Ejemplo: urlPath('user.deposit.history') → '/user/deposit/history'
 */

/**
 * levelCommission($user, $amount, $type, $trx) - Procesa comisiones multinivel
 * Para sistemas de referidos
 */
```

---

## Apéndice B: Webhooks/IPN URLs

Cada gateway automático tiene su propia URL de callback (IPN):

```
PayPal:     https://tu-sitio.com/ipn/Paypal
Stripe:     https://tu-sitio.com/ipn/Stripe
Razorpay:   https://tu-sitio.com/ipn/Razorpay
Paystack:   https://tu-sitio.com/ipn/Paystack
...
```

Estas rutas se definen en `routes/ipn.php` y son llamadas por los gateways externos cuando un pago se completa.

---

## Apéndice C: Notificaciones

El sistema envía las siguientes notificaciones relacionadas con depósitos:

| Template | Evento | Destinatario |
|----------|--------|--------------|
| `DEPOSIT_COMPLETE` | Pago automático exitoso | Usuario |
| `DEPOSIT_REQUEST` | Solicitud de pago manual enviada | Usuario |
| `DEPOSIT_APPROVE` | Pago manual aprobado | Usuario |
| `DEPOSIT_REJECT` | Pago manual rechazado | Usuario |

---

**Documento generado para:** RaffleLab - Sistema de Depósitos  
**Versión del documento:** 1.0  
**Última actualización:** Febrero 2026
