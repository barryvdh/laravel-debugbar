<?php

namespace Barryvdh\Debugbar\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $guarded = [];
}
