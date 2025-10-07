<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'isbn',
        'title',
        'genre',
        'price',
        'book_type',
        'description',
        'status',
        'admin_notes',
        'rev_book_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function revMapping()
    {
        return $this->hasOne(RevMapping::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeStocked($query)
    {
        return $query->where('status', 'stocked');
    }

    // Helper methods
    public function getTotalSales()
    {
        return $this->walletTransactions()
            ->where('type', 'sale')
            ->sum('amount');
    }

    public function getSalesCount()
    {
        return $this->walletTransactions()
            ->where('type', 'sale')
            ->count();
    }
}