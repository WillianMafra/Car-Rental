<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\carModel;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_model_id',
        'plate',
        'avaliable',
        'km',
        'daily_rate'

    ];

    public function rules(){
        return  [
            'car_model_id' => 'exists:car_models,id',
            'plate' => 'required',
            'avaliable' => 'required',
            'km' => 'required|',
            'daily_rate' => 'required|numeric'

        ];
    }

    public function dynamicrules($rulesArray){
        // Setting dynamic rules for validation   
        return $rulesArray;
   }

    public function carModel(): BelongsTo {
        return $this->belongsTo(carModel::class);
    }
 
}
