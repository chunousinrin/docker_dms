<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Master\User;


class InventoryAudit extends Model
{
    use HasFactory;

    protected $fillable = ['audited_on', 'auditor_id', 'memo'];

    protected $casts = ['audited_on' => 'date'];

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InventoryAuditLine::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(StampTransaction::class, 'audit_id');
    }
}
