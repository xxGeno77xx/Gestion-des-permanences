<?php

namespace App\Models;

use App\Models\Departement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LaravelDaily\Invoices\Contracts\PartyContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Permanence extends Model implements PartyContract
{
    use HasFactory;

    protected $casts = [
        'users' => 'array',
    ];

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }
}
