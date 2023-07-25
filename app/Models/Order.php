<?php

namespace App\Models;

use App\ClientStatusEnum;
use App\PrintHouseStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'order_id'];

    protected $casts = [

        'product_info' => 'array',
        'address' => 'array',
        'barcodes' => 'array',
        'print_house_status' => PrintHouseStatusEnum::class,
        'client_status' => ClientStatusEnum::class,

    ];

    public function documents()
    {
        $documents = collect([]);
        foreach ($this->barcodes as $barcode) {
            $barcode_extract = explode('-', $barcode['barcode_number']);
            $documents->push(Document::find($barcode_extract[2]));
        }

        return $documents;
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
