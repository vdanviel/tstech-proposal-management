<?php

namespace App\Models;

use App\Traits\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table("proposal")]
class Proposal extends Model
{
    use HasOptimisticLocking;
    use SoftDeletes;

    protected string $versionColumnName = 'version';

    protected $fillable = [
        'client_id',
        'product',
        'monthly_value',
        'status',
        'origin',
    ];

}
