<?php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.

use App\Models\Posts;
use App\Models\Tags;
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Posts
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Posts', route('home'));
});

// Posts > Create
Breadcrumbs::for('create', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Create', route('home'));
});

// Posts > Edit
Breadcrumbs::for('edit', function (BreadcrumbTrail $trail, Posts $post) {
    $trail->parent('home');
    $trail->push('Edit - '. $post->title, route('edit', $post));
});

// Home > View
Breadcrumbs::for('view', function (BreadcrumbTrail $trail, Posts $post) {
    $trail->parent('home');
    $trail->push('View - ' . $post->title, route('view', $post));
});

Breadcrumbs::for('tag', function (BreadcrumbTrail $trail, Tags $tag) {
    $trail->parent('home');
    $trail->push($tag->name, route('tags', $tag));
});
