<?php

use App\Http\Controllers\Account\ClaimController;
use App\Http\Controllers\Account\CompanyController;
use App\Http\Controllers\DirectoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DirectoryController::class, 'home'])->name('home');
Route::get('/mejores/', [DirectoryController::class, 'rankings'])->name('rankings.index');
Route::get('/mejores/{slug}/', [DirectoryController::class, 'ranking'])->name('rankings.show');
Route::get('/empresas/', [DirectoryController::class, 'companies'])->name('companies.index');
Route::get('/empresas/{slug}/', [DirectoryController::class, 'category'])->name('categories.show');
Route::get('/empresa/{slug}/', [DirectoryController::class, 'company'])->name('companies.show');
Route::get('/buscar/', [DirectoryController::class, 'search'])->name('search');
Route::get('/llms.txt', [DirectoryController::class, 'llms'])->name('llms');
Route::get('/sitemap.xml', [DirectoryController::class, 'sitemap'])->name('sitemap');

Route::view('/acceder/', 'auth.login')->middleware('guest')->name('login.spanish');
Route::view('/registro/', 'auth.register')->middleware('guest')->name('register.spanish');

Route::middleware(['auth'])->prefix('mi-cuenta')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('account.index');
    Route::get('/empresas/nueva/', [CompanyController::class, 'create'])->name('account.companies.create');
    Route::post('/empresas/', [CompanyController::class, 'store'])->name('account.companies.store');
    Route::get('/empresas/{uuid}/editar/', [CompanyController::class, 'edit'])->name('account.companies.edit');
    Route::patch('/empresas/{uuid}/', [CompanyController::class, 'update'])->name('account.companies.update');
    Route::get('/reclamar/{slug}/', [ClaimController::class, 'create'])->name('account.claims.create');
    Route::post('/reclamar/{slug}/', [ClaimController::class, 'store'])->name('account.claims.store');
});

foreach (['como-elaboramos-los-rankings', 'sobre-negocios-almeria', 'anunciate', 'anade-tu-empresa', 'contacto', 'aviso-legal', 'privacidad', 'cookies', 'condiciones'] as $slug) {
    Route::get("/{$slug}/", fn () => app(DirectoryController::class)->page($slug))->name("pages.{$slug}");
}
