<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    use HasFactory;


    protected $table = 'items';

    protected $fillable = [
        'name',
        'category_id',
        'item_type_id',
        'item_group_id',
        'exprire_date',
    ];


    public function category()
    {

        return $this->belongsTo(Category::class, 'category_id');

    }


    public function itemType()
    {

        return $this->belongsTo(ItemType::class, 'item_type_id');

    }


    public function itemGroup()
    {

        return $this->belongsTo(ItemGroup::class, 'item_group_id');

    }


    public function barcodes()
    {

        return $this->belongsToMany(Barcode::class, 'item_barcodes', 'item_id', 'barcode_id')

            ->withTimestamps();

    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'item_units', 'item_id', 'selling_unit_id');
    }


    public function itemUnits()
    {
        return $this->hasMany(ItemUnit::class, );
    }

    public function itemUnit()
    {
        return $this->hasOne(ItemUnit::class, 'item_id');
    }

    // public function stores()
    // {
    //     return $this->belongsToMany(Store::class, 'item_stores')
    //         ->with(['stocks', 'itemCosts', 'taxes']);
    // }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'item_stores', 'item_id', 'store_id');
    }

    // In Item.php model
    public function itemCosts()
    {
        return $this->hasMany(ItemCost::class);
    }

    public function itemPrices()
    {
        return $this->hasMany(ItemPrice::class);
    }

    public function itemStocks()
    {
        return $this->hasMany(ItemStock::class);
    }


    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'item_taxes', 'item_id', 'tax_id')
            ->withTimestamps();
    }


    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'item_stocks', 'item_id', 'stock_id');
    }

    public function itemTaxes()
    {
        return $this->hasMany(ItemTax::class);
    }

    public function itemBarcode()
    {
        return $this->hasOne(ItemBarcode::class, 'item_id');
    }

    public function itemBrand()
    {
        return $this->hasOne(BrandApplicableItem::class, 'item_id');
    }

}
