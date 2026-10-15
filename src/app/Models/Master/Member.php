<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Shared\NewsletterShipment;
use App\Models\Shared\ForestAreaTransaction;
use App\Models\Shared\CapitalTransaction;
use App\Models\Shared\DepositReceipt;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_number',
        'furigana',
        'name1',
        'name2',
        'postal_code',
        'address1',
        'address2',
        'occupation',
        'gender',
        'birth_date',
        'phone_number',
        'member_type',
        'withholding_tax_type',
        'joined_date',
        'withdrawn_date',
        'district_id',
        'section_id',
        'selection',
        'remarks',
        'honorific',
        'newsletter_delivery',
        'last_updated_date',
        'is_soudai',
        'election_district',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'joined_date' => 'date',
        'withdrawn_date' => 'date',
        'last_updated_date' => 'date',
        'newsletter_delivery' => 'boolean',
        'is_soudai' => 'boolean',
    ];

    // ---- 関連 ----

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function capitalTransactions()
    {
        return $this->hasMany(CapitalTransaction::class);
    }

    public function forestAreaTransactions()
    {
        return $this->hasMany(ForestAreaTransaction::class);
    }

    public function depositReceipts()
    {
        return $this->hasMany(DepositReceipt::class);
    }

    public function newsletterShipments()
    {
        return $this->hasMany(NewsletterShipment::class);
    }

    // ---- アクセサ ----

    /** 現在の出資口数・金額（最新の残高） */
    public function getCurrentCapitalAttribute(): array
    {
        $latest = $this->capitalTransactions()->latest('transaction_date')->latest('id')->first();
        return [
            'units' => $latest->balance_units_after ?? 0,
            'amount' => $latest->balance_amount_after ?? 0,
        ];
    }

    /** 現在の所有山林面積（最新の残高） */
    public function getCurrentForestAreaAttribute(): ?float
    {
        $latest = $this->forestAreaTransactions()->latest('transaction_date')->latest('id')->first();
        return $latest->balance_area_ha_after ?? null;
    }

    /** 出資預り書の発行回数 */
    public function getReceiptIssueCountAttribute(): int
    {
        return $this->depositReceipts()->count();
    }

    /** 在籍中かどうか */
    public function getIsActiveAttribute(): bool
    {
        return is_null($this->withdrawn_date);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('withdrawn_date');
    }

    public function scopeInDistrict($query, $districtId)
    {
        return $query->where('district_id', $districtId);
    }
}
