<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\Models;

use Illuminate\Foundation\Auth\User as Model;

class User extends Model
{
    protected $table = 'users';
    protected $guarded = [];
}
