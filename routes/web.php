<?php

use App\Livewire\Announcement\FormAnnouncement;
use App\Livewire\Announcement\ListAnnouncement;
use App\Livewire\Area\FormArea;
use App\Livewire\Area\ListArea;
use App\Livewire\Company\FormCompany;
use App\Livewire\Company\ListCompany;
use App\Livewire\Profesion\Profesions;
use App\Livewire\Report\ListClient;
use App\Livewire\User\CreateUser;
use App\Livewire\User\CustomUser;
use App\Livewire\User\ListUser;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', fn() => view('welcome'))->name('welcome');

// Search
Route::get('/busqueda/{title?}', fn($title = null) => view('search', ['title' => $title]))->name('search');

// Announcement
Route::get('/convocatoria/{id?}', fn($id = null) => view('result', ['id' => $id]))->name('result');

// Purchase
Route::get('/pro', fn() => view('purchase'))->name('purchase');

// All access logged
Route::group(['middleware' => ['role:FREE_CLIENT|PRO_CLIENT|USER|ADMIN']], function () {
    Route::get('/panel', fn() => view('dashboard'))->name('dashboard');
});

// Only users and admin access
Route::group(['middleware' => ['role:USER|ADMIN']], function () {
    //Announcements
    Route::get('/admin/convocatoria', ListAnnouncement::class)->name('announcement');
    Route::get('/admin/nueva-convocatoria/{id?}', FormAnnouncement::class)->name('new-announcement');

    // Companies
    Route::get('/admin/empresa', ListCompany::class)->name('company');
    Route::get('/admin/nueva-empresa/{id?}', FormCompany::class)->name('new-company');

    // Profesions
    Route::get('/admin/profesiones', Profesions::class)->name('profesions');
});

Route::group(['middleware' => ['role:ADMIN']], function () {
    // Areas
    Route::get('/admin/area', ListArea::class)->name('area');
    Route::get('/admin/nueva-area/{id?}', FormArea::class)->name('new-area');

    // Users
    Route::get('/admin/usuario', ListUser::class)->name('user');
    Route::get('/admin/ajuste-usuario/{id}', CustomUser::class)->name('config-user');
    Route::get('/admin/crear-usuario/{id?}', CreateUser::class)->name('create-user');

    // Reports
    Route::get('/admin/report', ListClient::class)->name('report');
});
