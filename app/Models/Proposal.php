<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $table = 'proposal';
    protected $fillable = [
        'proposal_name',
        'proposal_objective',
        'proposal_realization',
        'proposal_budget',
        'proposal_file',
        'proposal_status',
        'proposal_approver_id',
        'proposal_initiator_id',
    ];

    public function approver()
    {
        return $this->belongsTo(User::class, 'proposal_approver_id', 'id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'proposal_initiator_id', 'id');
    }

    public function isPending()
    {
        return $this->proposal_status === 'PENDING';
    }

    public function isApproved()
    {
        return $this->proposal_status === 'APPROVED';
    }

    public function isRejected()
    {
        return $this->proposal_status === 'REJECTED';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($proposal) {
            $proposal->proposal_status = 'PENDING';
            $proposal->proposal_initiator_id = auth()->id();
        });
    }

}

