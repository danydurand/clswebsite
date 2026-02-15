# 📊 Sistema de Transacciones - RaffleLab
## Documentación Técnica Completa

**Proyecto:** RaffleLab - Superlative Lottery Platform  
**Versión:** CodeCanyon  
**Framework:** Laravel (PHP 8.3+)  
**Fecha de documentación:** Febrero 2026

---

## 📑 Índice

1. [Resumen General](#1-resumen-general)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Flujo de Transacciones](#3-flujo-de-transacciones)
4. [Componentes del Sistema](#4-componentes-del-sistema)
5. [Base de Datos](#5-base-de-datos)
6. [Tipos de Transacciones](#6-tipos-de-transacciones)
7. [Controladores](#7-controladores)
8. [Vistas de Transacciones](#8-vistas-de-transacciones)
9. [Funciones Auxiliares](#9-funciones-auxiliares)
10. [Filtros y Búsquedas](#10-filtros-y-búsquedas)

---

## 1. Resumen General

El sistema de transacciones de RaffleLab es el **registro histórico centralizado** de todos los movimientos financieros del usuario. A diferencia de los depósitos y retiros (que son procesos activos), las transacciones son **registros pasivos** que se crean automáticamente cuando ocurre cualquier operación que afecte el balance del usuario.

### Características Principales

- **Registro automático** de todas las operaciones financieras
- **Trazabilidad completa** con códigos de transacción únicos (TRX)
- **Balance post-transacción** almacenado en cada registro
- **Clasificación por tipo** (crédito/débito) y por remark (categoría)
- **Filtros avanzados** para búsqueda y análisis

### URL de Acceso

```
Usuario: https://tu-sitio.com/transactions
Admin:   https://tu-sitio.com/admin/report/transaction
```

---

## 2. Arquitectura del Sistema

### 2.1 Diagrama de Arquitectura

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                        EVENTOS QUE GENERAN TRANSACCIONES                        │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐   │
│   │   Depósito   │  │   Retiro     │  │   Compra     │  │   Premio de      │   │
│   │   Exitoso    │  │   Solicitado │  │   Lotería    │  │   Lotería        │   │
│   └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  └────────┬─────────┘   │
│          │                 │                 │                    │             │
│          ▼                 ▼                 ▼                    ▼             │
│   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐   │
│   │  remark:     │  │  remark:     │  │  remark:     │  │   remark:        │   │
│   │  payment     │  │  withdraw    │  │  payment     │  │   prize_money    │   │
│   │  trx_type:+  │  │  trx_type:-  │  │  trx_type:-  │  │   trx_type:+     │   │
│   └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  └────────┬─────────┘   │
│          │                 │                 │                    │             │
│   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐   │
│   │   Admin      │  │   Rechazo    │  │  Solicitud   │  │   Comisión       │   │
│   │   Add/Sub    │  │   Retiro     │  │   Monedas    │  │   Referidos      │   │
│   └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  └────────┬─────────┘   │
│          │                 │                 │                    │             │
│          ▼                 ▼                 ▼                    ▼             │
│   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐   │
│   │  remark:     │  │  remark:     │  │  remark:     │  │   remark:        │   │
│   │  balance_add │  │  withdraw_   │  │  coin_added  │  │   referral_      │   │
│   │  /subtract   │  │  reject      │  │              │  │   commission     │   │
│   └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  └────────┬─────────┘   │
│          │                 │                 │                    │             │
└──────────┼─────────────────┼─────────────────┼────────────────────┼─────────────┘
           │                 │                 │                    │
           └─────────────────┴─────────────────┴────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              CAPA DE MODELOS                                    │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│   ┌─────────────────────────────────────────────────────────────────────────┐   │
│   │                           Transaction Model                             │   │
│   │                                                                         │   │
│   │   • user_id         → FK a users                                        │   │
│   │   • amount          → Monto de la transacción                           │   │
│   │   • charge          → Comisión aplicada                                 │   │
│   │   • post_balance    → Balance después de la operación                   │   │
│   │   • trx_type        → '+' (crédito) / '-' (débito)                      │   │
│   │   • trx             → Código único de transacción                       │   │
│   │   • details         → Descripción de la operación                       │   │
│   │   • remark          → Categoría/tipo de transacción                     │   │
│   │                                                                         │   │
│   └─────────────────────────────────────────────────────────────────────────┘   │
│                                      │                                          │
│                                      ▼                                          │
│   ┌─────────────────────────────────────────────────────────────────────────┐   │
│   │                             User Model                                  │   │
│   │                                                                         │   │
│   │   public function transactions()                                        │   │
│   │   {                                                                     │   │
│   │       return $this->hasMany(Transaction::class)->orderBy('id','desc');  │   │
│   │   }                                                                     │   │
│   │                                                                         │   │
│   └─────────────────────────────────────────────────────────────────────────┘   │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              BASE DE DATOS (MySQL)                              │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│   ┌─────────────────────────────────────────────────────────────────────────┐   │
│   │                          transactions table                             │   │
│   └─────────────────────────────────────────────────────────────────────────┘   │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 Estructura de Archivos

```
core/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── User/
│   │       │   └── UserController.php           ← transactions() para usuarios
│   │       ├── Admin/
│   │       │   ├── ReportController.php         ← transaction() para admin
│   │       │   ├── ManageUsersController.php    ← addSubBalance() - crear trx
│   │       │   ├── WithdrawalController.php     ← reject() - crear trx
│   │       │   ├── DrawController.php           ← createTransaction() - premios
│   │       │   └── CoinRequestController.php    ← approve() - crear trx
│   │       └── Gateway/
│   │           └── PaymentController.php        ← userDataUpdate() - crear trx
│   │
│   ├── Models/
│   │   ├── Transaction.php                      ← Modelo de transacciones
│   │   └── User.php                             ← Relación transactions()
│   │
│   └── Http/Helpers/
│       └── helpers.php                          ← getTrx(), levelCommission()
│
├── resources/
│   └── views/
│       ├── templates/
│       │   └── basic/
│       │       └── user/
│       │           └── transactions.blade.php   ← Vista del usuario
│       └── admin/
│           └── reports/
│               └── transactions.blade.php       ← Vista del admin
│
└── routes/
    ├── user.php                                 ← Route user.transactions
    └── admin.php                                ← Route admin.report.transaction
```

---

## 3. Flujo de Transacciones

### 3.1 Diagrama de Flujo - Creación de Transacciones

```
┌──────────────────────────────────────────────────────────────────────────────┐
│              FLUJO DE CREACIÓN DE TRANSACCIONES (AUTOMÁTICO)                 │
└──────────────────────────────────────────────────────────────────────────────┘

   EVENTO ORIGEN                 PROCESO                    RESULTADO
        │                           │                           │
        ▼                           ▼                           ▼

┌─────────────────┐    ┌─────────────────────────┐    ┌─────────────────────┐
│  DEPÓSITO       │───▶│  PaymentController      │───▶│  Transaction        │
│  EXITOSO        │    │  ::userDataUpdate()     │    │  remark: payment    │
│                 │    │                         │    │  trx_type: +        │
│  Pago gateway   │    │  $user->balance +=      │    │  amount: deposited  │
│  confirmado     │    │  $deposit->amount       │    │                     │
└─────────────────┘    └─────────────────────────┘    └─────────────────────┘

┌─────────────────┐    ┌─────────────────────────┐    ┌─────────────────────┐
│  COMPRA TICKET  │───▶│  LotteryController      │───▶│  Transaction        │
│  CON BALANCE    │    │  ::pick()               │    │  remark: payment    │
│                 │    │                         │    │  trx_type: -        │
│  Usuario usa    │    │  $user->balance -=      │    │  amount: ticket     │
│  su saldo       │    │  $totalAmount           │    │                     │
└─────────────────┘    └─────────────────────────┘    └─────────────────────┘

┌─────────────────┐    ┌─────────────────────────┐    ┌─────────────────────┐
│  SOLICITUD      │───▶│  WithdrawController     │───▶│  Transaction        │
│  DE RETIRO      │    │  ::withdrawSubmit()     │    │  remark: withdraw   │
│                 │    │                         │    │  trx_type: -        │
│  Usuario envía  │    │  $user->balance -=      │    │  amount: withdraw   │
│  formulario     │    │  $withdraw->amount      │    │  charge: fee        │
└─────────────────┘    └─────────────────────────┘    └─────────────────────┘

┌─────────────────┐    ┌─────────────────────────┐    ┌─────────────────────┐
│  RECHAZO        │───▶│  WithdrawalController   │───▶│  Transaction        │
│  DE RETIRO      │    │  (Admin)::reject()      │    │  remark: withdraw_  │
│                 │    │                         │    │          reject     │
│  Admin rechaza  │    │  $user->balance +=      │    │  trx_type: +        │
│  solicitud      │    │  $withdraw->amount      │    │  amount: refunded   │
└─────────────────┘    └─────────────────────────┘    └─────────────────────┘

┌─────────────────┐    ┌─────────────────────────┐    ┌─────────────────────┐
│  GANADOR        │───▶│  DrawController         │───▶│  Transaction        │
│  DE LOTERÍA     │    │  ::createTransaction()  │    │  remark: prize_money│
│                 │    │                         │    │  trx_type: +        │
│  Números        │    │  $user->balance +=      │    │  amount: prize      │
│  coinciden      │    │  $winner->prize_money   │    │                     │
└─────────────────┘    └─────────────────────────┘    └─────────────────────┘

┌─────────────────┐    ┌─────────────────────────┐    ┌─────────────────────┐
│  AJUSTE ADMIN   │───▶│  ManageUsersController  │───▶│  Transaction        │
│  DE BALANCE     │    │  ::addSubBalance()      │    │  remark: balance_   │
│                 │    │                         │    │          add/sub    │
│  Admin agrega   │    │  $user->balance +=/-=   │    │  trx_type: +/-      │
│  o resta saldo  │    │  $amount                │    │  amount: adjusted   │
└─────────────────┘    └─────────────────────────┘    └─────────────────────┘

┌─────────────────┐    ┌─────────────────────────┐    ┌─────────────────────┐
│  SOLICITUD      │───▶│  CoinRequestController  │───▶│  Transaction        │
│  DE MONEDAS     │    │  (Admin)::approve()     │    │  remark: coin_added │
│                 │    │                         │    │  trx_type: +        │
│  Admin aprueba  │    │  $user->balance +=      │    │  amount: coins      │
│  la solicitud   │    │  $coinRequest->amount   │    │                     │
└─────────────────┘    └─────────────────────────┘    └─────────────────────┘

┌─────────────────┐    ┌─────────────────────────┐    ┌─────────────────────┐
│  COMISIÓN       │───▶│  helpers.php            │───▶│  Transaction        │
│  DE REFERIDOS   │    │  levelCommission()      │    │  remark: referral_  │
│                 │    │                         │    │          commission │
│  Referido hace  │    │  $refer->balance +=     │    │  trx_type: +        │
│  depósito/etc   │    │  $com                   │    │  amount: commission │
└─────────────────┘    └─────────────────────────┘    └─────────────────────┘
```

### 3.2 Flujo de Consulta de Transacciones

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                    FLUJO DE CONSULTA DE TRANSACCIONES                        │
└──────────────────────────────────────────────────────────────────────────────┘

     USUARIO/ADMIN                 SISTEMA                      RESPUESTA
          │                           │                              │
          │  1. GET /transactions     │                              │
          │─────────────────────────▶ │                              │
          │                           │                              │
          │                           │  2. UserController           │
          │                           │     @transactions()          │
          │                           │                              │
          │                           │  3. Query:                   │
          │                           │     Transaction::where(      │
          │                           │       'user_id', auth()->id  │
          │                           │     )                        │
          │                           │     ->searchable(['trx'])    │
          │                           │     ->filter(['trx_type',    │
          │                           │               'remark'])     │
          │                           │     ->orderBy('id','desc')   │
          │                           │     ->paginate()             │
          │                           │                              │
          │  4. Lista de              │                              │
          │     transacciones         │                              │
          │◀──────────────────────────│                              │
          │                           │                              │
          │                           │                              │
          │  FILTROS DISPONIBLES:     │                              │
          │  ┌─────────────────────┐  │                              │
          │  │ • search (TRX)      │  │                              │
          │  │ • trx_type (+/-)    │  │                              │
          │  │ • remark (tipo)     │  │                              │
          │  │ • date (rango)      │  │                              │
          │  └─────────────────────┘  │                              │
          │                           │                              │
          ▼                           ▼                              ▼
```

---

## 4. Componentes del Sistema

### 4.1 Rutas

**Usuario (routes/user.php):**

```php
Route::controller('UserController')->group(function () {
    // ...
    Route::get('transactions', 'transactions')->name('transactions');
    // ...
});
```

**Admin (routes/admin.php):**

```php
// Report
Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
    Route::get('transaction/{user_id?}', 'transaction')->name('transaction');
    // ...
});
```

### 4.2 Modelo Transaction

**Archivo:** `app/Models/Transaction.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * Relación: Usuario propietario de la transacción
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 4.3 Relación en Modelo User

**Archivo:** `app/Models/User.php`

```php
<?php

namespace App\Models;

class User extends Authenticatable
{
    // ...
    
    /**
     * Transacciones del usuario
     * Ordenadas de más reciente a más antigua
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }
    
    // ...
}
```

---

## 5. Base de Datos

### 5.1 Tabla `transactions`

```sql
CREATE TABLE `transactions` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    
    -- Relación con usuario
    `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
    
    -- Montos
    `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,        -- Monto de la operación
    `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,        -- Comisión (si aplica)
    `post_balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,  -- Balance después de operación
    
    -- Tipo de transacción
    `trx_type` varchar(40) DEFAULT NULL,    -- '+' = crédito, '-' = débito
    
    -- Tracking
    `trx` varchar(40) DEFAULT NULL,         -- Código único de transacción
    
    -- Descripción
    `details` varchar(255) DEFAULT NULL,    -- Descripción legible de la operación
    `remark` varchar(40) DEFAULT NULL,      -- Categoría/tipo de transacción
    
    -- Timestamps
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `transactions_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5.2 Descripción de Campos

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Identificador único auto-incremental |
| `user_id` | int | FK al usuario propietario |
| `amount` | decimal(28,8) | Monto de la transacción |
| `charge` | decimal(28,8) | Comisión aplicada (puede ser 0) |
| `post_balance` | decimal(28,8) | Balance del usuario después de la transacción |
| `trx_type` | varchar(40) | Tipo: `+` (crédito/ingreso), `-` (débito/egreso) |
| `trx` | varchar(40) | Código único de seguimiento (ej: `ABC123XYZ456`) |
| `details` | varchar(255) | Descripción legible de la transacción |
| `remark` | varchar(40) | Categoría de la transacción (ver sección 6) |
| `created_at` | timestamp | Fecha/hora de creación |
| `updated_at` | timestamp | Fecha/hora de última actualización |

### 5.3 Diagrama Entidad-Relación

```
┌─────────────────┐                         ┌─────────────────────┐
│     users       │                         │    transactions     │
├─────────────────┤                         ├─────────────────────┤
│ PK id           │◄────────────────────────│ FK user_id          │
│    username     │                         │ PK id               │
│    email        │                         │    amount           │
│    balance      │                         │    charge           │
│    ...          │                         │    post_balance     │
└─────────────────┘                         │    trx_type         │
                                            │    trx              │
                                            │    details          │
                                            │    remark           │
                                            │    created_at       │
                                            │    updated_at       │
                                            └─────────────────────┘
```

---

## 6. Tipos de Transacciones

### 6.1 Tabla de Remarks (Categorías)

| Remark | Tipo | Descripción | Origen |
|--------|------|-------------|--------|
| `payment` | + | Depósito de fondos via gateway | PaymentController::userDataUpdate() |
| `payment` | - | Compra de tickets con balance | LotteryController::pick() |
| `prize_money` | + | Premio por ganar lotería | DrawController::createTransaction() |
| `withdraw` | - | Solicitud de retiro | WithdrawController::withdrawSubmit() |
| `withdraw_reject` | + | Reembolso por retiro rechazado | WithdrawalController::reject() |
| `balance_add` | + | Balance añadido por admin | ManageUsersController::addSubBalance() |
| `balance_subtract` | - | Balance restado por admin | ManageUsersController::addSubBalance() |
| `coin_added` | + | Monedas añadidas por solicitud | CoinRequestController::approve() |
| `referral_commission` | + | Comisión por referidos | levelCommission() helper |

### 6.2 Diagrama de Flujo por Tipo

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                        TRANSACCIONES DE CRÉDITO (+)                          │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐         │
│   │    payment      │    │   prize_money   │    │  withdraw_reject│         │
│   │    (+)          │    │    (+)          │    │     (+)         │         │
│   ├─────────────────┤    ├─────────────────┤    ├─────────────────┤         │
│   │ Depósito de     │    │ Ganancia de     │    │ Reembolso por   │         │
│   │ fondos via      │    │ lotería al      │    │ retiro          │         │
│   │ gateway         │    │ coincidir       │    │ rechazado       │         │
│   │                 │    │ números         │    │                 │         │
│   └─────────────────┘    └─────────────────┘    └─────────────────┘         │
│                                                                              │
│   ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐         │
│   │   balance_add   │    │   coin_added    │    │    referral_    │         │
│   │      (+)        │    │     (+)         │    │   commission    │         │
│   ├─────────────────┤    ├─────────────────┤    ├─────────────────┤         │
│   │ Admin añade     │    │ Admin aprueba   │    │ Comisión por    │         │
│   │ balance         │    │ solicitud de    │    │ referidos       │         │
│   │ manualmente     │    │ monedas         │    │ (multinivel)    │         │
│   └─────────────────┘    └─────────────────┘    └─────────────────┘         │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────┐
│                        TRANSACCIONES DE DÉBITO (-)                           │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐         │
│   │    payment      │    │    withdraw     │    │balance_subtract │         │
│   │    (-)          │    │     (-)         │    │     (-)         │         │
│   ├─────────────────┤    ├─────────────────┤    ├─────────────────┤         │
│   │ Compra de       │    │ Solicitud de    │    │ Admin resta     │         │
│   │ tickets de      │    │ retiro de       │    │ balance         │         │
│   │ lotería         │    │ fondos          │    │ manualmente     │         │
│   └─────────────────┘    └─────────────────┘    └─────────────────┘         │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## 7. Controladores

### 7.1 UserController - Listado de Transacciones (Usuario)

**Archivo:** `app/Http/Controllers/User/UserController.php`

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class UserController extends Controller
{
    /**
     * Muestra el historial de transacciones del usuario autenticado
     * 
     * @return \Illuminate\View\View
     */
    public function transactions()
    {
        $pageTitle = 'Transactions';
        
        // Obtener todos los remarks únicos para el filtro
        $remarks = Transaction::distinct('remark')
            ->orderBy('remark')
            ->get('remark');

        // Consulta de transacciones con filtros
        $transactions = Transaction::where('user_id', auth()->id())
            ->searchable(['trx'])                    // Búsqueda por código TRX
            ->filter(['trx_type', 'remark'])         // Filtros por tipo y categoría
            ->orderBy('id', 'desc')                  // Más recientes primero
            ->paginate(getPaginate());

        return view('Template::user.transactions', compact(
            'pageTitle', 
            'transactions', 
            'remarks'
        ));
    }
}
```

### 7.2 ReportController - Reportes de Transacciones (Admin)

**Archivo:** `app/Http/Controllers/Admin/ReportController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Muestra el reporte de transacciones para el admin
     * Opcionalmente filtrado por usuario específico
     * 
     * @param Request $request
     * @param int|null $userId - ID del usuario (opcional)
     * @return \Illuminate\View\View
     */
    public function transaction(Request $request, $userId = null)
    {
        $pageTitle = 'Transaction Logs';
        
        // Obtener remarks únicos para el filtro
        $remarks = Transaction::distinct('remark')
            ->orderBy('remark')
            ->get('remark');
        
        // Query base con filtros
        $transactions = Transaction::searchable(['trx', 'user:username'])
            ->filter(['trx_type', 'remark'])
            ->dateFilter()                           // Filtro por rango de fechas
            ->orderBy('id', 'desc')
            ->with('user');                          // Eager load del usuario
        
        // Filtrar por usuario específico si se proporciona
        if ($userId) {
            $transactions = $transactions->where('user_id', $userId);
        }
        
        $transactions = $transactions->paginate(getPaginate());

        return view('admin.reports.transactions', compact(
            'pageTitle', 
            'transactions', 
            'remarks'
        ));
    }
}
```

### 7.3 Creación de Transacciones - Ejemplos

#### 7.3.1 PaymentController (Depósitos)

```php
// En PaymentController::userDataUpdate()
$transaction = new Transaction();
$transaction->user_id = $deposit->user_id;
$transaction->amount = $deposit->amount;
$transaction->post_balance = $user->balance;
$transaction->charge = $deposit->charge;
$transaction->trx_type = '+';
$transaction->details = 'Payment for purchase coin via payment gateway ' . $methodName;
$transaction->trx = $deposit->trx;
$transaction->remark = 'payment';
$transaction->save();
```

#### 7.3.2 LotteryController (Compra de Tickets)

```php
// En LotteryController::pick()
$transaction = new Transaction();
$transaction->user_id = $user->id;
$transaction->amount = $totalAmount;
$transaction->post_balance = $user->balance;
$transaction->charge = 0;
$transaction->trx_type = '-';
$transaction->details = 'Payment for purchase ticket';
$transaction->trx = getTrx();
$transaction->remark = 'payment';
$transaction->save();
```

#### 7.3.3 DrawController (Premios de Lotería)

```php
// En DrawController::createTransaction()
$transaction = new Transaction();
$transaction->user_id = $user->id;
$transaction->amount = $winner->prize_money;
$transaction->charge = 0;
$transaction->post_balance = $user->balance;
$transaction->trx_type = '+';
$transaction->trx = getTrx();
$transaction->details = 'Prize money for winning the lottery';
$transaction->remark = 'prize_money';
$transaction->save();
```

#### 7.3.4 WithdrawController (Solicitud de Retiro)

```php
// En WithdrawController::withdrawSubmit()
$transaction = new Transaction();
$transaction->user_id = $withdraw->user_id;
$transaction->amount = $withdraw->amount;
$transaction->post_balance = $user->balance;
$transaction->charge = $withdraw->charge;
$transaction->trx_type = '-';
$transaction->details = 'Withdraw request via ' . $withdraw->method->name;
$transaction->trx = $withdraw->trx;
$transaction->remark = 'withdraw';
$transaction->save();
```

#### 7.3.5 WithdrawalController - Admin (Rechazo de Retiro)

```php
// En WithdrawalController::reject()
$transaction = new Transaction();
$transaction->user_id = $withdraw->user_id;
$transaction->amount = $withdraw->amount;
$transaction->post_balance = $user->balance;
$transaction->charge = 0;
$transaction->trx_type = '+';
$transaction->remark = 'withdraw_reject';
$transaction->details = 'Refunded for withdrawal rejection';
$transaction->trx = $withdraw->trx;
$transaction->save();
```

#### 7.3.6 ManageUsersController (Ajuste de Balance por Admin)

```php
// En ManageUsersController::addSubBalance()
$transaction = new Transaction();

if ($request->act == 'add') {
    $user->balance += $amount;
    $transaction->trx_type = '+';
    $transaction->remark = 'balance_add';
} else {
    $user->balance -= $amount;
    $transaction->trx_type = '-';
    $transaction->remark = 'balance_subtract';
}

$transaction->user_id = $user->id;
$transaction->amount = $amount;
$transaction->post_balance = $user->balance;
$transaction->charge = 0;
$transaction->trx = getTrx();
$transaction->details = $request->remark;  // Razón proporcionada por admin
$transaction->save();
```

#### 7.3.7 CoinRequestController (Solicitud de Monedas)

```php
// En CoinRequestController::approve()
$transaction = new Transaction();
$transaction->user_id = $user->id;
$transaction->amount = $coinRequest->amount;
$transaction->post_balance = $user->balance;
$transaction->charge = 0;
$transaction->trx = getTrx();
$transaction->trx_type = "+";
$transaction->details = showAmount($coinRequest->amount) . " coin added";
$transaction->remark = 'coin_added';
$transaction->save();
```

---

## 8. Vistas de Transacciones

### 8.1 Vista del Usuario

**Archivo:** `resources/views/templates/basic/user/transactions.blade.php`

#### Estructura Visual

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                                                                                 │
│   My Transactions History                                                       │
│                                                                                 │
├─────────────────────────────────────────────────────────────────────────────────┤
│                            ÁREA DE FILTROS                                      │
│   ┌───────────────────┐  ┌───────────────────┐  ┌───────────────────┐          │
│   │ Transaction Number│  │      Type         │  │      Remark       │          │
│   │ [________🔍]      │  │ [All         ▼]   │  │ [Any          ▼]  │          │
│   │                   │  │   • Plus (+)      │  │   • Payment       │          │
│   │                   │  │   • Minus (-)     │  │   • Withdraw      │          │
│   │                   │  │                   │  │   • Prize Money   │          │
│   │                   │  │                   │  │   • etc...        │          │
│   └───────────────────┘  └───────────────────┘  └───────────────────┘          │
│                                                                                 │
├─────────────────────────────────────────────────────────────────────────────────┤
│                         LISTA DE TRANSACCIONES (Acordeón)                       │
│                                                                                 │
│   ┌─────────────────────────────────────────────────────────────────────────┐   │
│   │ [✓] Payment                          #ABC123XYZ456        $100.00 COIN  │   │
│   │     Feb 10 2026 @10:30am                                 Balance: $500  │   │
│   │ ┌───────────────────────────────────────────────────────────────────┐   │   │
│   │ │ Charge:        $2.50 COIN                                         │   │   │
│   │ │ Post Balance:  $500.00 COIN                                       │   │   │
│   │ │ Details:       Payment for purchase coin via payment gateway...   │   │   │
│   │ └───────────────────────────────────────────────────────────────────┘   │   │
│   └─────────────────────────────────────────────────────────────────────────┘   │
│                                                                                 │
│   ┌─────────────────────────────────────────────────────────────────────────┐   │
│   │ [✗] Withdraw                         #DEF456GHI789        -$50.00 COIN  │   │
│   │     Feb 09 2026 @3:45pm                                  Balance: $400  │   │
│   └─────────────────────────────────────────────────────────────────────────┘   │
│                                                                                 │
│   ┌─────────────────────────────────────────────────────────────────────────┐   │
│   │ [✓] Prize Money                      #GHI789JKL012       $1000.00 COIN  │   │
│   │     Feb 08 2026 @6:00pm                                 Balance: $1450  │   │
│   └─────────────────────────────────────────────────────────────────────────┘   │
│                                                                                 │
│   [← Previous]  [1] [2] [3] ... [10]  [Next →]                                  │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
```

#### Código Blade (Simplificado)

```html
@extends($activeTemplate.'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h4>@lang('My Transactions History')</h4>
    </div>
    
    <!-- ÁREA DE FILTROS -->
    <div class="filter-area mb-3">
        <div class="d-flex flex-wrap gap-4">
            <!-- Búsqueda por TRX -->
            <div class="flex-grow-1">
                <form action="{{ route('user.transactions') }}">
                    <div class="custom-input-box trx-search">
                        <label>@lang('Transaction Number')</label>
                        <input type="text" name="search" value="{{ request()->search }}" 
                               placeholder="@lang('Transaction Number')">
                        <button type="submit" class="icon-area">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Filtro por Tipo (+/-) -->
            <div class="flex-grow-1">
                <div class="custom-input-box">
                    <label>@lang('Type')</label>
                    <select name="trx_type" onChange="window.location.href=this.value">
                        <option value="{{queryBuild('trx_type','')}}">@lang('All')</option>
                        <option value="{{queryBuild('trx_type','%2B')}}" 
                                @selected(request()->trx_type == '+')>@lang('Plus')</option>
                        <option value="{{queryBuild('trx_type','-')}}" 
                                @selected(request()->trx_type == '-')>@lang('Minus')</option>
                    </select>
                </div>
            </div>
            
            <!-- Filtro por Remark -->
            <div class="flex-grow-1">
                <div class="custom-input-box">
                    <label>@lang('Remark')</label>
                    <select name="remark" onChange="window.location.href=this.value">
                        <option value="{{ queryBuild('remark','') }}">@lang('Any')</option>
                        @foreach($remarks as $remark)
                        <option value="{{ queryBuild('remark',$remark->remark) }}" 
                                @selected(request()->remark == $remark->remark)>
                            {{ __(keyToTitle($remark->remark)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- LISTA DE TRANSACCIONES (ACORDEÓN) -->
    <div class="accordion table--acordion" id="transactionAccordion">
        @forelse($transactions as $transaction)
            <div class="accordion-item transaction-item">
                <h2 class="accordion-header" id="h-{{$loop->iteration}}">
                    <button class="accordion-button collapsed" type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#c-{{$loop->iteration}}">
                        
                        <!-- Icono y Tipo -->
                        <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                            <div class="left">
                                <div class="icon tr-icon 
                                     @if($transaction->trx_type == '+') icon-success 
                                     @else icon-danger @endif">
                                    <i class="las la-long-arrow-alt-right"></i>
                                </div>
                                <div class="content">
                                    <h6 class="trans-title">
                                        {{ __(keyToTitle($transaction->remark)) }}
                                    </h6>
                                    <span class="text-muted font-size--14px mt-2">
                                        {{ showDateTime($transaction->created_at,'M d Y @g:i:a') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Código TRX -->
                        <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3">
                            <p class="text-muted font-size--14px">
                                <b>#{{ $transaction->trx }}</b>
                            </p>
                        </div>
                        
                        <!-- Monto -->
                        <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end">
                            <p>
                                <b>{{ showAmount($transaction->amount) }} {{ __(gs()->cur_text) }}</b>
                                <br>
                                <small class="fw-bold text-muted">
                                    @lang('Balance'): {{ showAmount($transaction->post_balance) }}
                                </small>
                            </p>
                        </div>
                    </button>
                </h2>
                
                <!-- Detalles expandibles -->
                <div id="c-{{$loop->iteration}}" class="accordion-collapse collapse" 
                     data-bs-parent="#transactionAccordion">
                    <div class="accordion-body">
                        <ul class="caption-list">
                            <li>
                                <span class="caption">@lang('Charge')</span>
                                <span class="value">
                                    {{ showAmount($transaction->charge) }} {{ __(gs()->cur_text) }}
                                </span>
                            </li>
                            <li>
                                <span class="caption">@lang('Post Balance')</span>
                                <span class="value">
                                    {{ showAmount($transaction->post_balance) }} {{ __(gs()->cur_text) }}
                                </span>
                            </li>
                            <li>
                                <span class="caption">@lang('Details')</span>
                                <span class="value">{{ __($transaction->details) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="accordion-body text-center">
                <h4 class="text--muted">
                    <i class="far fa-frown"></i> {{ __($emptyMessage) }}
                </h4>
            </div>
        @endforelse
    </div>

    <!-- PAGINACIÓN -->
    @if($transactions->hasPages())
        <div class="mt-4">
            {{ paginateLinks($transactions) }}
        </div>
    @endif
</div>
@endsection
```

### 8.2 Vista del Admin

**Archivo:** `resources/views/admin/reports/transactions.blade.php`

#### Estructura Visual

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                                                                                 │
│   Transaction Logs                                            [ 🔍 Filter ]     │
│                                                                                 │
├─────────────────────────────────────────────────────────────────────────────────┤
│                            ÁREA DE FILTROS                                      │
│   ┌────────────────┐ ┌──────────────┐ ┌──────────────┐ ┌────────────────────┐   │
│   │ TRX/Username   │ │    Type      │ │   Remark     │ │       Date         │   │
│   │ [___________]  │ │ [All     ▼]  │ │ [All     ▼]  │ │ [Jan 01 - Feb 10]  │   │
│   └────────────────┘ └──────────────┘ └──────────────┘ └────────────────────┘   │
│                                                                                 │
│                                                            [ 🔍 Filter ]        │
│                                                                                 │
├─────────────────────────────────────────────────────────────────────────────────┤
│                              TABLA DE TRANSACCIONES                             │
│                                                                                 │
│   ┌─────────┬────────────────┬─────────────────┬──────────┬──────────┬───────┐  │
│   │  User   │      TRX       │   Transacted    │  Amount  │  Post    │Details│  │
│   │         │                │                 │          │ Balance  │       │  │
│   ├─────────┼────────────────┼─────────────────┼──────────┼──────────┼───────┤  │
│   │ John D  │ ABC123XYZ456   │ Feb 10, 2026    │ +$100.00 │ $500.00  │ Pay.. │  │
│   │ @john   │                │ 2 hours ago     │          │          │       │  │
│   ├─────────┼────────────────┼─────────────────┼──────────┼──────────┼───────┤  │
│   │ Jane S  │ DEF456GHI789   │ Feb 09, 2026    │ -$50.00  │ $400.00  │ With..│  │
│   │ @jane   │                │ 1 day ago       │          │          │       │  │
│   ├─────────┼────────────────┼─────────────────┼──────────┼──────────┼───────┤  │
│   │ Mike B  │ GHI789JKL012   │ Feb 08, 2026    │ +$1000   │ $1450.00 │ Prize.│  │
│   │ @mike   │                │ 2 days ago      │          │          │       │  │
│   └─────────┴────────────────┴─────────────────┴──────────┴──────────┴───────┘  │
│                                                                                 │
│   [← Previous]  [1] [2] [3] ... [10]  [Next →]                                  │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
```

#### Código Blade (Simplificado)

```html
@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        
        <!-- Botón para mostrar filtros -->
        <div class="show-filter mb-3 text-end">
            <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm">
                <i class="las la-filter"></i> @lang('Filter')
            </button>
        </div>
        
        <!-- ÁREA DE FILTROS -->
        <div class="card responsive-filter-card mb-4">
            <div class="card-body">
                <form>
                    <div class="d-flex flex-wrap gap-4">
                        <!-- Búsqueda TRX/Username -->
                        <div class="flex-grow-1">
                            <label>@lang('TRX/Username')</label>
                            <input type="search" name="search" 
                                   value="{{ request()->search }}" class="form-control">
                        </div>
                        
                        <!-- Filtro por Tipo -->
                        <div class="flex-grow-1">
                            <label>@lang('Type')</label>
                            <select name="trx_type" class="form-control select2">
                                <option value="">@lang('All')</option>
                                <option value="+" @selected(request()->trx_type == '+')>
                                    @lang('Plus')
                                </option>
                                <option value="-" @selected(request()->trx_type == '-')>
                                    @lang('Minus')
                                </option>
                            </select>
                        </div>
                        
                        <!-- Filtro por Remark -->
                        <div class="flex-grow-1">
                            <label>@lang('Remark')</label>
                            <select class="form-control select2" name="remark">
                                <option value="">@lang('All')</option>
                                @foreach($remarks as $remark)
                                <option value="{{ $remark->remark }}" 
                                        @selected(request()->remark == $remark->remark)>
                                    {{ __(keyToTitle($remark->remark)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Filtro por Fecha -->
                        <div class="flex-grow-1">
                            <label>@lang('Date')</label>
                            <input name="date" type="search" 
                                   class="datepicker-here form-control date-range" 
                                   placeholder="@lang('Start Date - End Date')" 
                                   value="{{ request()->date }}">
                        </div>
                        
                        <!-- Botón Filtrar -->
                        <div class="flex-grow-1 align-self-end">
                            <button class="btn btn--primary w-100 h-45">
                                <i class="fas fa-filter"></i> @lang('Filter')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- TABLA DE TRANSACCIONES -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('TRX')</th>
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Post Balance')</th>
                                <th>@lang('Details')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <!-- Usuario -->
                                    <td>
                                        <span class="fw-bold">{{ $trx->user->fullname }}</span>
                                        <br>
                                        <span class="small">
                                            <a href="{{ appendQuery('search',$trx->user->username) }}">
                                                <span>@</span>{{ $trx->user->username }}
                                            </a>
                                        </span>
                                    </td>

                                    <!-- Código TRX -->
                                    <td>
                                        <strong>{{ $trx->trx }}</strong>
                                    </td>

                                    <!-- Fecha -->
                                    <td>
                                        {{ showDateTime($trx->created_at) }}
                                        <br>
                                        {{ diffForHumans($trx->created_at) }}
                                    </td>

                                    <!-- Monto (con color según tipo) -->
                                    <td class="budget">
                                        <span class="fw-bold 
                                            @if($trx->trx_type == '+') text--success 
                                            @else text--danger @endif">
                                            {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                        </span>
                                    </td>

                                    <!-- Post Balance -->
                                    <td class="budget">
                                        {{ showAmount($trx->post_balance) }}
                                    </td>

                                    <!-- Detalles -->
                                    <td>{{ __($trx->details) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">
                                        {{ __($emptyMessage) }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Paginación -->
            @if($transactions->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($transactions) }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
```

---

## 9. Funciones Auxiliares

### 9.1 getTrx() - Generar Código de Transacción

**Archivo:** `app/Http/Helpers/helpers.php`

```php
/**
 * Genera un código único de transacción
 * 
 * @param int $length Longitud del código (default: 12)
 * @return string Código alfanumérico único
 * 
 * Ejemplo de salida: "ABC123XYZ456"
 */
function getTrx($length = 12)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}
```

### 9.2 levelCommission() - Comisiones de Referidos

**Archivo:** `app/Http/Helpers/helpers.php`

```php
/**
 * Procesa las comisiones de referidos por niveles
 * Crea transacciones automáticamente para cada nivel
 * 
 * @param User $user Usuario que originó la acción
 * @param float $amount Monto base para calcular comisión
 * @param string $commissionType Tipo de comisión (deposit_commission, 
 *                               lottery_purchase_commission, lottery_win_commission)
 * @param string $trx Código de transacción original
 */
function levelCommission($user, $amount, $commissionType, $trx)
{
    $meUser = $user;
    $i = 1;
    $level = Referral::where('commission_type', $commissionType)->count();
    $transactions = [];
    
    // Iterar por cada nivel de referidos
    while ($i <= $level) {
        $me = $meUser;
        $refer = $me->referrer;  // Obtener el referidor
        
        if ($refer == "") {
            break;  // No hay más referidores
        }

        // Obtener configuración de comisión para este nivel
        $commission = Referral::where('commission_type', $commissionType)
            ->where('level', $i)
            ->first();
            
        if (!$commission) {
            break;
        }

        // Calcular y aplicar comisión
        $com = ($amount * $commission->percent) / 100;
        $refer->balance += $com;
        $refer->save();

        // Preparar registro de transacción
        $transactions[] = [
            'user_id'      => $refer->id,
            'amount'       => $com,
            'post_balance' => $refer->balance,
            'charge'       => 0,
            'trx_type'     => '+',
            'details'      => 'level ' . $i . ' Referral Commission From ' . $user->username,
            'trx'          => $trx,
            'remark'       => 'referral_commission',
            'created_at'   => now(),
        ];

        // Determinar tipo de comisión para notificación
        if ($commissionType == 'deposit_commission') {
            $comType = 'Deposit';
        } elseif ($commissionType == 'lottery_purchase_commission') {
            $comType = 'Lottery Purchase';
        } else {
            $comType = 'Lottery Win';
        }

        // Notificar al referidor
        notify($refer, 'REFERRAL_COMMISSION', [
            'amount'       => showAmount($com, currencyFormat: false),
            'post_balance' => showAmount($refer->balance, currencyFormat: false),
            'trx'          => $trx,
            'level'        => ordinal($i),
            'type'         => $comType,
        ]);

        $meUser = $refer;
        $i++;
    }

    // Insertar todas las transacciones de una vez (bulk insert)
    if (!empty($transactions)) {
        Transaction::insert($transactions);
    }
}
```

### 9.3 keyToTitle() - Convertir Remark a Título Legible

```php
/**
 * Convierte un remark (snake_case) a título legible
 * 
 * @param string $text El remark a convertir
 * @return string Título formateado
 * 
 * Ejemplos:
 *   'referral_commission' → 'Referral Commission'
 *   'prize_money'         → 'Prize Money'
 *   'withdraw_reject'     → 'Withdraw Reject'
 */
function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}
```

### 9.4 queryBuild() - Construir URL con Filtros

```php
/**
 * Construye URL con parámetros de query para filtros
 * 
 * @param string $key Nombre del parámetro
 * @param string $value Valor del parámetro
 * @return string URL con el parámetro agregado/actualizado
 */
function queryBuild($key, $value)
{
    $queries = request()->query();
    
    if (@$queries['search']) {
        $route = route('user.transactions');
        unset($queries['search']);
    } else {
        $route = request()->getRequestUri();
    }
    
    if (count($queries) > 0) {
        $delimiter = '&';
    } else {
        $delimiter = '?';
    }
    
    if (request()->has($key)) {
        $url = request()->getRequestUri();
        $pattern = "\?$key";
        $match = preg_match("/$pattern/", $url);
        
        if ($match != 0) {
            return preg_replace('~(\?|&)' . $key . '[^&]*~', "\?$key=$value", $url);
        }
        
        $filteredURL = preg_replace('~(\?|&)' . $key . '[^&]*~', '', $url);
        return $filteredURL . $delimiter . "$key=$value";
    }
    
    return $route . $delimiter . "$key=$value";
}
```

---

## 10. Filtros y Búsquedas

### 10.1 Traits de Filtrado

El sistema utiliza traits de Laravel para implementar búsquedas y filtros. Los traits relevantes son:

#### Searchable Trait

Permite búsqueda por campos específicos:

```php
// Ejemplo de uso
Transaction::searchable(['trx', 'user:username'])  // Busca en trx y username del usuario
```

#### Filter Trait

Permite filtrar por campos específicos:

```php
// Ejemplo de uso
Transaction::filter(['trx_type', 'remark'])  // Filtra por tipo y categoría
```

#### DateFilter Trait

Permite filtrar por rango de fechas:

```php
// Ejemplo de uso
Transaction::dateFilter()  // Aplica filtro de fecha desde request
```

### 10.2 Parámetros de Filtro Disponibles

| Parámetro | Descripción | Valores | Ejemplo |
|-----------|-------------|---------|---------|
| `search` | Búsqueda por TRX o username | Texto libre | `?search=ABC123` |
| `trx_type` | Tipo de transacción | `+`, `-` | `?trx_type=+` |
| `remark` | Categoría de transacción | Ver sección 6 | `?remark=payment` |
| `date` | Rango de fechas | Fecha inicio - Fecha fin | `?date=Jan 01 - Feb 10` |

### 10.3 Ejemplos de URLs con Filtros

```
# Buscar por código de transacción
/transactions?search=ABC123XYZ456

# Filtrar solo créditos (+)
/transactions?trx_type=%2B

# Filtrar solo débitos (-)
/transactions?trx_type=-

# Filtrar por tipo de transacción
/transactions?remark=payment
/transactions?remark=prize_money
/transactions?remark=withdraw

# Combinar filtros
/transactions?trx_type=%2B&remark=payment

# Admin: filtrar por usuario específico
/admin/report/transaction/5

# Admin: filtrar por rango de fechas
/admin/report/transaction?date=January%2001,%202026%20-%20February%2010,%202026
```

---

## Apéndice A: Resumen de Integración

### A.1 Checklist para Implementar Sistema de Transacciones

1. **Base de Datos**
   - [ ] Crear tabla `transactions` con estructura especificada
   - [ ] Crear índice en `user_id`

2. **Modelos**
   - [ ] Crear modelo `Transaction` con relación a `User`
   - [ ] Agregar relación `transactions()` en modelo `User`

3. **Controladores**
   - [ ] Implementar método para listar transacciones del usuario
   - [ ] Implementar método para reportes de admin
   - [ ] Agregar creación de transacción en cada operación financiera

4. **Vistas**
   - [ ] Crear vista de historial para usuarios
   - [ ] Crear vista de reportes para admin
   - [ ] Implementar filtros y paginación

5. **Helpers**
   - [ ] Implementar `getTrx()` para generar códigos únicos
   - [ ] Implementar `levelCommission()` si se usan referidos
   - [ ] Implementar helpers de formato (`keyToTitle`, `showAmount`, etc.)

6. **Rutas**
   - [ ] Agregar ruta de transacciones para usuarios
   - [ ] Agregar ruta de reportes para admin

### A.2 Puntos de Integración con Otros Módulos

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                      PUNTOS DE CREACIÓN DE TRANSACCIONES                        │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│   MÓDULO DEPÓSITOS                                                              │
│   └── PaymentController::userDataUpdate() → remark: 'payment', trx_type: '+'    │
│                                                                                 │
│   MÓDULO RETIROS                                                                │
│   ├── WithdrawController::withdrawSubmit() → remark: 'withdraw', trx_type: '-'  │
│   └── WithdrawalController::reject() → remark: 'withdraw_reject', trx_type: '+' │
│                                                                                 │
│   MÓDULO LOTERÍA                                                                │
│   ├── LotteryController::pick() → remark: 'payment', trx_type: '-'              │
│   └── DrawController::createTransaction() → remark: 'prize_money', trx_type: '+'│
│                                                                                 │
│   MÓDULO USUARIOS (Admin)                                                       │
│   └── ManageUsersController::addSubBalance()                                    │
│       ├── Add → remark: 'balance_add', trx_type: '+'                            │
│       └── Sub → remark: 'balance_subtract', trx_type: '-'                       │
│                                                                                 │
│   MÓDULO MONEDAS                                                                │
│   └── CoinRequestController::approve() → remark: 'coin_added', trx_type: '+'    │
│                                                                                 │
│   MÓDULO REFERIDOS                                                              │
│   └── levelCommission() → remark: 'referral_commission', trx_type: '+'          │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

**Fin de la documentación**
