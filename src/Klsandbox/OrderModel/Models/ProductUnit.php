<?php namespace Klsandbox\OrderModel\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $table = 'product_units';

    protected $fillable = ['name', 'description'];
}