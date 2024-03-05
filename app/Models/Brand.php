<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image'];

    public function rules($brandId = 0){
         // Setting rules for validation   
        return [
            'name' => 
                ['required',
                Rule::unique('brands')->ignore($brandId)],
            'image' => 'required',
        ];
    }
}
