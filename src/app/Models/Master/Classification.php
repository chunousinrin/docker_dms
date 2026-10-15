<?php
// app/Models/Classification.php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Accounting\Invoice;

class Classification extends Model
{
    protected $fillable = ['name', 'description'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'invoice_category_id');
    }
}
