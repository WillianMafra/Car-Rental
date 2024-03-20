<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Repositories\carRepository;
use Illuminate\Http\Request;

class carController extends Controller
{

    // To debug in POSTMAN, set in Header Accept => application/json
    public $car;
    
    public function __construct(Car $car)
    {
        $this->car = $car;   
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $carRepository = new CarRepository($this->car);

        // Select columns in car_models table
        $carModelColumns = $request->filled('carmodel_columns') ? 'carModel:id,'.$request->carmodel_columns : 'CarModel.brand';
        $carRepository->selectRelationatedColumns($carModelColumns);
    
        // Select columns in cars table
        if ($request->filled('columns')) {
            $columns = $request->columns;
            $carRepository->selectColumns($columns);
        }
    
        // Filter on car_models table
        $relationatedFilters = ['doors', 'seats', 'abs', 'air_bag'];
        foreach ($relationatedFilters as $filter) {
            if ($request->filled($filter)) {
                $carRepository->filterRelationatedColumns('CarModel', $request->$filter, $filter);
            }
        }
        
        // Filter on cars table
        $filters = ['car_model_id', 'avaliable'];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $carRepository->filter($request->$filter, $filter);
            }
        }

        // Is pagination active?
        if ($request->filled('paginate')) {
            return $carRepository->getPaginatedResults($request->paginate);
        }
    
        return $carRepository->getResults();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validating the data
        $request->validate($this->car->rules());

        // store the new car
        $newCar = new Car();

        $newCar->fill($request->all());
        $newCar->save();

        return response()->json(['msg' => "Car $request->plate created successfully"], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($carId)
    {
        $car = $this->car->with('carModel')->find($carId);
        
        // If the resource was not found, return with 404 and a error msg
        if($car === null){
            return response()->json(['Error' =>'Car not found'], 404);
        }

        // If the resource was found, return the data with 200
        return response()->json($car, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $carId)
    {

        $car = $this->car->find($carId);

        // If the resource was not found, return with 404 and a error msg
        if($car === null){
            return response()->json(['Error' =>'Car not found'], 404);
        }
        // If the method is PATCH, we need to validete only the data was send
        if($request->method() === 'PATCH'){
            $dynamicRules = [];

            foreach($car->rules() as $input => $rule){
                if(array_key_exists($input, $request->all())){
                    $dynamicRules[$input] = $rule;
                }
            }
            $request->validate($this->car->dynamicrules($dynamicRules));
        } else {
            // doing the full data validation
            $request->validate($this->car->rules($carId));
        }

        $car->fill($request->all());

        // If the resource was found, updade the resource and return with 200
        $car->save();
        return response()->json(['msg' => "Car updated successfully"], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($carId)
    {
        $car = $this->car->find($carId);

         // If the resource was not found, return with 404 and a error msg
        if($car === null){
            return response()->json(['Error' =>'Car not found'], 404);
        }

        // If the resource was found,delete the resource and return with 200
        $this->car->destroy($carId);
        return response()->json(['msg' => "Car deleted successfully"], 200);
    }
}
