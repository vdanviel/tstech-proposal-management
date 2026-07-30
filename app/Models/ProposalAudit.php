<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table('proposal_audit')]
class ProposalAudit extends Model
{

    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'proposal_id',
        'actor',
        'event',
        'payload',
        'created_at',
    ];
}
