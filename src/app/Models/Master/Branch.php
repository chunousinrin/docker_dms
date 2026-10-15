<?php
// app/Models/Branch.php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

use App\Models\Accounting\Invoice;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'representative',
        'seal_path',
        'postal_code',
        'address',
        'phone',
        'fax'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'requester_id');
    }
}
