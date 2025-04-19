<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = ['name', 'address', 'owner_id'];

    public function foods()
    {
        return $this->hasMany(Food::class);
    }

    public function beverages()
    {
        return $this->hasMany(Beverage::class);
    }
}
