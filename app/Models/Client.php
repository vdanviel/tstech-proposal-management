<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('client')]
class Client extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'document',
        'password',
    ];
}
