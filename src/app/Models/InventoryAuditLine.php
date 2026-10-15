<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\InventoryAudit;
use App\Models\StampItem;

class InventoryAuditLine extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'inventory_audit_id',
        'stamp_item_id',
        'book_qty',
        'counted_qty',
        'diff',
        'face_value',
        'note',
    ];

    protected $casts = [
        'book_qty'    => 'integer',
        'counted_qty' => 'integer',
        'diff'        => 'integer',
        'face_value'  => 'integer',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(InventoryAudit::class, 'inventory_audit_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StampItem::class, 'stamp_item_id');
    }

    /** 差異金額（円） */
    public function getDiffAmountAttribute(): int
    {
        return $this->diff * $this->face_value;
    }
}
