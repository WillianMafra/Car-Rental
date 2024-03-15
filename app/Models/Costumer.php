<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Lease;

class Costumer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function rules(){
        return  [
            'name' => 'required|min:3'
        ];
    }

    public function lease(): HasMany{
        return $this->hasMany(Lease::class);
    }
 
}
