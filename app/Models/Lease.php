<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Car;
use App\Models\User;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_id',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'daily_rate',
        'initial_km',
        'final_km',
    ];

    public function rules(){
        return  [
            'user_id' => 'exists:user,id',
            'car_id' => 'exists:cars,id|integer',
            'start_date' => 'required|date',
            'expected_end_date' => 'required|date',
            'actual_end_date' => 'required|date',
            'daily_rate' => 'required|numeric',
            'initial_km' => 'required|integer',
            'final_km' => 'required|integer'
        ];
    }

    public function dynamicrules($rulesArray){
        // Setting dynamic rules for validation   
        return $rulesArray;
   }

    public function car(): BelongsTo {
        return $this->belongsTo(Car::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
 
}
