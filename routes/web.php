<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

// ── Redirect root to dashboard ──────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('dashboard'));

// ── Auth required ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'active.user'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('can:view dashboard')
        ->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // POS
    Route::get('/pos', fn() => view('pos.index'))
        ->middleware('can:create sales')
        ->name('pos.index');

    // Sales
    Route::middleware('can:view sales')->group(function () {
        Route::get('/ventas', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/ventas/{sale}', [SaleController::class, 'show'])->name('sales.show');
    });

    // Invoices / Printing
    Route::get('/factura/{sale}/ver',      [InvoiceController::class, 'show'])->name('sales.invoice');
    Route::get('/factura/{sale}/pdf',      [InvoiceController::class, 'pdf'])->name('sales.invoice.pdf');
    Route::get('/factura/{sale}/tirilla',  [InvoiceController::class, 'thermal'])->name('sales.invoice.thermal');

    // Products / Inventory
    Route::get('/inventario', fn() => view('products.index'))
        ->middleware('can:view products')
        ->name('products.index');

    // Categories
    Route::get('/categorias', fn() => view('categories.index'))
        ->middleware('can:view categories')
        ->name('categories.index');

    // Customers
    Route::get('/clientes', fn() => view('customers.index'))
        ->middleware('can:view customers')
        ->name('customers.index');

    // Employees
    Route::get('/empleados', fn() => view('employees.index'))
        ->middleware('can:view employees')
        ->name('employees.index');

    // Workshop
    Route::get('/taller', fn() => view('workshop.index'))
        ->middleware('can:view workshop')
        ->name('workshop.index');

    // Reports
    Route::middleware('can:view reports')->group(function () {
        Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reportes/exportar', [ReportController::class, 'exportExcel'])->name('reports.export');
    });

    // Users
    Route::get('/usuarios', fn() => view('users.index'))
        ->middleware('can:view users')
        ->name('users.index');

    // Settings
    Route::get('/configuracion', fn() => view('settings.index'))
        ->middleware('can:view settings')
        ->name('settings.index');
});

require __DIR__ . '/auth.php';
