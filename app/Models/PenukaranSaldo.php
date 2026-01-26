<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenukaranSaldo extends Model
{
    protected $table = 'withdraw_requests'; 

    protected $fillable = [
        'user_id',
        'amount',
        'bank_name',
        'account_number',
        'account_name',
        'status',
        'processed_by',
        'note',
        'approved_at',
        'success_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'approved_at' => 'datetime',
        'success_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
