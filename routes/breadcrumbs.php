<?php

use App\Models\Source;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('dashboard'));
});

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard'));
});

Breadcrumbs::for('management.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Management', route('management.users.index'));
});

Breadcrumbs::for('management.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('management.index');
    $trail->push('Users', route('management.users.index'));
});

Breadcrumbs::for('management.users.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('management.users.index');
    $trail->push(ucwords($user->name), route('management.users.show', $user));
});

Breadcrumbs::for('management.sources.index', function (BreadcrumbTrail $trail) {
    $trail->parent('management.index');
    $trail->push('Sources', route('management.sources.index'));
});

Breadcrumbs::for('management.sources.show', function (BreadcrumbTrail $trail, Source $source) {
    $trail->parent('management.sources.index');
    $trail->push($source->url, route('management.sources.show', $source));
});
