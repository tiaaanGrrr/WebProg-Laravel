<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = ['reference', 'checkout_token', 'customer_name', 'phone', 'address', 'notes', 'total'];

    public const STATUS_LABELS = [
        'baru' => 'Baru',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public function nextStatuses(): array
    {
        $statuses = match ($this->status) {
            'baru' => ['diproses', 'dibatalkan'],
            'diproses' => ['selesai', 'dibatalkan'],
            default => [],
        };

        return array_intersect_key(self::STATUS_LABELS, array_flip($statuses));
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
