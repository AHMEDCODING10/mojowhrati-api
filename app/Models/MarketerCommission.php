<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketerCommission extends Model
{
    protected $table = 'marketer_commissions';

    protected $fillable = [
        'marketer_id', 'user_id', 'user_type',
        'amount', 'currency', 'status', 'notes',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function marketer()
    {
        return $this->belongsTo(Marketer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
