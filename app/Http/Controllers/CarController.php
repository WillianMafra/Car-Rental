<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Repositories\carRepository;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CarController extends Controller
{

    // To debug in POSTMAN, set in Header Accept => application/json
    public $car;
    
    public function __construct(Car $car)
    {
        $this->car = $car;   
    }

    /**
         * @OA\Get(
         *     path="/api/car",
         *     summary="Get list of cars",
         *     tags={"Cars"},
         *     @OA\Parameter(
         *         name="carmodel_columns",
         *         in="query",
         *         description="(optional)Columns to select from car models table. ex: /api/car?carmodel_columns=name,image",
         *     @OA\Schema(
         *         type="string",
         *         example="brand_id",
         *         description="List of columns separated by comma"
         *     )
         *     ),
         *     @OA\Parameter(
         *         name="columns",
         *         in="query",
         *         description="Columns for cars table. ex:",
         *         @OA\Schema(
         *             type="object",
         *             @OA\Property(property="plate", type="text", example="ilike:%CNY5292%"),
         *             @OA\Property(property="car_model_id", type="text", example="=:5"),
         *             @OA\Property(property="km", type="text", example="=:27805"),
         *             @OA\Property(property="avaliable", type="text", example="=:true"),
         *             @OA\Property(property="daily_rate", type="text", example="=:38.06")
         *         )
         *     ),
         *     @OA\Parameter(
         *         name="filters",
         *         in="query",
         *         description="Filters for cars",
         *         @OA\Schema(
         *             type="object",
         *             @OA\Property(property="doors", type="text", example="=:4"),
         *             @OA\Property(property="seats", type="text", example="=:5"),
         *             @OA\Property(property="abs", type="text", example="=:true"),
         *             @OA\Property(property="air_bag", type="text", example="=:true")
         *         )
         *     ),
         *     @OA\Parameter(
         *         name="paginate",
         *         in="query",
         *         example="2",
         *         description="Number of items per page for pagination",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Successful operation",
         *     ),
         *     @OA\Response(
         *         response=400,
         *         description="Bad request"
         *     )
         * )
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
        * @OA\Post(
        *     path="/api/car",
        *     summary="Create a new car",
        *     tags={"Cars"},
        *     @OA\RequestBody(
        *         required=true,
        *         description="Car data to be created",
        *         @OA\MediaType(
        *             mediaType="multipart/form-data",
        *             @OA\Schema(
        *                 type="object",
        *                 @OA\Property(
        *                     property="plate",
        *                     type="string",
        *                     description="Car Plate",
        *                     example="MGQ3055"
        *                 ),
        *                 @OA\Property(
        *                     property="daily_rate",
        *                     type="float",
        *                     description="Daily Rate",
        *                     example="30.58"
        *                 ),
        *                 @OA\Property(
        *                     property="car_model_id",
        *                     type="integer",
        *                     description="Car Model id",
        *                     example="1"
        *                 ),
        *                 @OA\Property(
        *                     property="km",
        *                     type="integer",
        *                     description="Car's km",
        *                     example="1000"
        *                 ),
        *                 @OA\Property(
        *                     property="avaliable",
        *                     type="boolean",
        *                     description="Is the car avaliable?",
        *                     example="true"
        *                 ),
        *             )
        *         )
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Car created successfully",
        *         @OA\JsonContent(
        *             type="object",
        *             @OA\Property(property="msg", type="string", example="Car xPlatex created successfully")
        *         )
        *     ),
        *     @OA\Response(
        *         response=422,
        *         description="Validation error"
        *     )
        * )
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
     * @OA\Get(
     *     path="/api/car/{carId}",
     *     summary="Get details of a specific car",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="carId",
     *         in="path",
     *         example="2",
     *         description="ID of the car",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A json with car data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found"
     *     )
     * )
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
     * @OA\post(
     *     path="/api/car/{carId}",
     *     summary="Update a car",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="carId",
     *         in="path",
     *         description="ID of the car",
     *         required=true,
     *         example="2",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="plate",
     *                     type="string",
     *                     description="Plate of the car",
     *                     example="mgq5588"
     *                 ),
     *                 @OA\Property(
     *                     property="_method",
     *                     type="string",
     *                     description="method for the request",
     *                     example="put"
     *                 ),
     *                 @OA\Property(
     *                     property="km",
     *                     type="integer",
     *                     description="KM of the car",
     *                     example="20000"
     *                 ),
     *                 @OA\Property(
     *                     property="car_model_id",
     *                     type="integer",
     *                     description="Car model id",
     *                     example="1"
     *                 ),
     *                 @OA\Property(
     *                     property="daily_rate",
     *                     type="float",
     *                     description="Car Daily Rate",
     *                     example="12.5"
     *                 ),
     *                 @OA\Property(
     *                     property="avaliable",
     *                     type="boolean",
     *                     description="Is the car avaliable?",
     *                     example="true"
     *                 )
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="msg", type="string", example="Brand updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Brand not found"
     *     )
     * )
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
         * @OA\Delete(
         *     path="/api/car/{carId}",
         *     summary="Delete a car",
         *     tags={"Cars"},
         *     @OA\Parameter(
         *         name="carId",
         *         in="path",
         *         description="ID of the brand",
         *         required=true,
         *         example="1",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Successful operation",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="msg", type="string", example="Car deleted successfully")
         *         )
         *     ),
         *     @OA\Response(
         *         response=404,
         *         description="Car not found"
         *     )
         * )
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
