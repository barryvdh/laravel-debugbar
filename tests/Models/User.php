<?php

namespace Barryvdh\Debugbar\Tests\Models;

use Illuminate\Foundation\Auth\User as Model;

class User extends Model
{
    protected $table = 'users';
    protected $guarded = [];
}
