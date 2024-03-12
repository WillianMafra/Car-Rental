<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;
use App\Models\Brand;


class carModel extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'brand_id',
        'name',
        'image',
        'doors',
        'seats',
        'air_bag',
        'abs'
    ];

    public function rules($carModelId = 0){
       return  
       [
        'brand_id' => 'exists:brands,id',
        'name' => 
            [
                'required',
                Rule::unique('car_models')->ignore($carModelId),
                'min:3'
            ],
        'image' => 'required|file|mimes:png,jpg,jpeg',
        'doors' => 'integer|required|digits-between:1,5',
        'seats' => 'integer|required|digits-between:1,20',
        'abs' => 'boolean|required',
        'air_bag' => 'boolean|required'
        ];
    }

    public function dynamicrules($rulesArray, $carModelId = 0){
        // Setting dynamic rules for validation   
       if(isset($rulesArray['name'])){
           $rulesArray['name'] = ['required',
           Rule::unique('car_models')->ignore($carModelId)];
       }
       return $rulesArray;
   }

    public function brand(): BelongsTo {
        return $this->belongsTo(Brand::class);
    }
}
