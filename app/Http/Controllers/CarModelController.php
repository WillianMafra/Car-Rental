<?php

namespace App\Http\Controllers;

use App\Models\carModel;
use App\Repositories\carModelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class CarModelController extends Controller
{
      // To debug in POSTMAN, set in Header Accept => application/json
      public $carModel;
      public function __construct(carModel $carModel)
      {
          $this->carModel = $carModel;   
      }
    /**
         * @OA\Get(
         *     path="/api/car-model",
         *     summary="Get list of car models",
         *     tags={"Car Models"},
         *     @OA\Parameter(
         *         name="columns",
         *         in="query",
         *         description="(optional)Columns to select from car models table. ex: /api/car?columns=brand_id,image,doors,air_bag,abs",
         *     @OA\Schema(
         *         type="string",
         *         example="brand_id,name,id",
         *         description="List of columns separated by comma"
         *     )
         *     ),
         *     @OA\Parameter(
         *         name="filter",
         *         in="query",
         *         description="Filter for brands table",
         *         @OA\Schema(
         *             type="object",
         *             @OA\Property(property="brand_name", type="text", example="ilike:%ford%"),
         *     ),
         *     @OA\Parameter(
         *         name="filters",
         *         in="query",
         *         description="Filters for car_models table",
         *         @OA\Schema(
         *             type="object",
         *             @OA\Property(property="id", type="text", example="=:3"),
         *             @OA\Property(property="doors", type="text", example="=:4"),
         *             @OA\Property(property="seats", type="text", example="=:5"),
         *             @OA\Property(property="abs", type="text", example="=:true"),
         *             @OA\Property(property="air_bag", type="text", example="=:true"),
         *             @OA\Property(property="name", type="text", example="ilike:%hb20%")
         *         )
         *      )    
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

        $carModelRepository = new carModelRepository($this->carModel);

        // If the user specified the columns
        if ($request->filled('columns')) {
            $columns = $request->columns;
            $carModelRepository->selectColumns($columns);  
        }

        // Filter on car_models table
        $filters = ['abs', 'id', 'doors', 'seats', 'air_bag', 'name'];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $carModelRepository->filter($request->$filter, $filter);
            }
        }

        // Filter on brands table
        $relationatedFilters = ['brand_name'];
        foreach ($relationatedFilters as $filter) {
            if ($request->filled($filter)) {
                if($filter == 'brand_name'){
                    $carModelRepository->filterRelationatedColumns('brand', $request->$filter, 'name');
                } else {
                    $carModelRepository->filterRelationatedColumns('brand', $request->$filter, $filter);
                }
            }
        }

        if($request->has('paginate') && $request->paginate != ''){
            return $carModelRepository->getPaginatedResults($request->paginate);
        }

        return $carModelRepository->getResults();
    }


    /**
         * @OA\Post(
         *     path="/api/car-model",
         *     summary="Store a newcar model",
         *     tags={"Car Models"},
         *     @OA\RequestBody(
         *         required=true,
         *         description="Car model data",
         *         @OA\MediaType(
         *             mediaType="multipart/form-data",
         *             @OA\Schema(
         *                 type="object",
         *                 @OA\Property(
         *                     property="name",
         *                     type="string",
         *                     description="Car Model Name",
         *                     example="320i"
         *                 ),
         *                 @OA\Property(
         *                     property="brand_id",
         *                     type="integer",
         *                     description="Brand Id",
         *                     example="2"
         *                 ),
         *                 @OA\Property(
         *                     property="doors",
         *                     type="integer",
         *                     description="Number of doors",
         *                     example="4"
         *                 ),
         *                 @OA\Property(
         *                     property="seats",
         *                     type="integer",
         *                     description="Number of seats",
         *                     example="5"
         *                 ),
         *                 @OA\Property(
         *                     property="abs",
         *                     type="integer",
         *                     description="The car have abs? (1 to true and 0 to false)",
         *                     example="0"
         *                 ),
         *                 @OA\Property(
         *                     property="air_bag",
         *                     type="integer",
         *                     description="The car have air bag? (1 to true and 0 to false)",
         *                     example="1"
         *                 ),
        *                 @OA\Property(
        *                     property="image",
        *                     type="string",
        *                     format="binary",
        *                     description="Image file of the car model"
        *                 )
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Json with car model data",
        *         @OA\JsonContent(
        *             type="object",
        *             @OA\Property(property="msg", type="string", example="Car Model xNamex created successfully")
        *         )
         *     ),
         *     @OA\Response(
         *         response=400,
         *         description="Bad request"
         *     )
         * )
    */
    public function store(Request $request)
    {
        // Validating the data
        $request->validate($this->carModel->rules());

        // Saving the file
        $image = $request->file('image');
        $imageUrn = $image->store('images', 'public');

        $data = [
            'name' => $request->get('name'),
            'image' => $imageUrn,
            'brand_id' => $request->get('brand_id'),
            'doors' => $request->get('doors'),
            'seats' => $request->get('seats'),
            'abs' => $request->get('abs'),
            'air_bag' => $request->get('air_bag')
        ];

        // store the new brand
        $newCarModel = new carModel();
        $newCarModel->fill($data);
        $newCarModel->save();

        return response()->json(['msg' => "Car model $request->name created successfully"], 200);
    }

    /**
         * @OA\Get(
         *     path="/api/car-model/{carModelId}",
         *     summary="Get details of a specific car model",
         *     tags={"Car Models"},
         *     @OA\Parameter(
         *         name="carModelId",
         *         in="path",
         *         description="ID of the car model",
         *         required=true,
         *         example="1",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Successful operation"
         *     ),
         *     @OA\Response(
         *         response=404,
         *         description="Car Model not found"
         *     )
         * )
    */

    public function show($carModelId)
    {
        $carModel = $this->carModel->with('brand')->find($carModelId);
        
        // If the resource was not found, return with 404 and a error msg
        if($carModel === null){
            return response()->json(['Error' =>'Car Model not found'], 404);
        }

        // If the resource was found, return the data with 200
        return response()->json($carModel, 200);
    }

    /**
        * @OA\POST(
        *     path="/api/car-model/{carModelId}",
        *     summary="Update a car model",
        *     tags={"Car Models"},
        *     @OA\Parameter(
        *         name="carModelId",
        *         in="path",
        *         description="ID of the car model",
        *         required=true,
        *         example="1",
        *         @OA\Schema(type="integer")
        *     ),
        *     @OA\RequestBody(
        *         required=true,
        *         description="Car model data",
        *         @OA\MediaType(
        *             mediaType="multipart/form-data",
        *             @OA\Schema(
        *                 type="object",
        *                     @OA\Property(
        *                     property="_method",
        *                     type="string",
        *                     description="method for the request",
        *                     example="patch"
        *                 ),
        *                 @OA\Property(
        *                     property="name",
        *                     type="string",
        *                     description="Car Model Name",
        *                     example="i30"
        *                 ),
        *                 @OA\Property(
        *                     property="brand_id",
        *                     type="integer",
        *                     description="Brand Id",
        *                     example="2"
        *                 ),
        *                 @OA\Property(
        *                     property="doors",
        *                     type="integer",
        *                     description="Number of doors",
        *                     example="4"
        *                 ),
        *                 @OA\Property(
        *                     property="seats",
        *                     type="integer",
        *                     description="Number of seats",
        *                     example="5"
        *                 ),
        *                 @OA\Property(
        *                     property="abs",
        *                     type="integer",
        *                     description="The car have abs? (1 to true and 0 to false)",
        *                     example="0"
        *                 ),
        *                 @OA\Property(
        *                     property="air_bag",
        *                     type="integer",
        *                     description="The car have air bag? (1 to true and 0 to false)",
        *                     example="1"
        *                 ),
        *                 @OA\Property(
        *                     property="image",
        *                     type="string",
        *                     format="binary",
        *                     description="Image file of the car model"
        *                 )
        *               )
        *            )
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

    public function update(Request $request, $carModelId)
    {
        $carModel = $this->carModel->find($carModelId);
        // If the resource was not found, return with 404 and a error msg
        if($carModel === null){
            return response()->json(['Error' =>'Car Model not found'], 404);
        }
        // If the method is PATCH, we need to validate only the data that was sent
        if($request->method() == 'PATCH'){
            $dynamicRules = [];
            foreach($carModel->rules() as $input => $rule){
                if(array_key_exists($input, $request->all())){
                    $dynamicRules[$input] = $rule;
                }
            }
            $request->validate($this->carModel->dynamicrules($dynamicRules, $carModelId));
        } else {
            // doing the full data validation
            $request->validate($this->carModel->rules($carModelId));
        }

        $carModel->fill($request->all());

        if($request->file('image')){
        // Saving the file
            $image = $request->file('image');
            $imageUrn = $image->store('images', 'public');

            if($imageUrn != ''){
                Storage::disk('public')->delete($carModel->image);
            }

            $carModel->image = $imageUrn;
        }

        // If the resource was found, updade the resource and return with 200
        $carModel->save();
        return response()->json(['msg' => "Car Model updated successfully"], 200);
    }

    /**
        * @OA\Delete(
        *     path="/api/car-model/{carModelId}",
        *     summary="Delete a car model",
        *     tags={"Car Models"},
        *     @OA\Parameter(
        *         name="carModelId",
        *         in="path",
        *         description="ID of the car model",
        *         required=true,
        *         example="4",
        *         @OA\Schema(type="integer")
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Successful operation",
        *         @OA\JsonContent(
        *             type="object",
        *             @OA\Property(property="msg", type="string", example="Car Model deleted successfully")
        *         )
        *     ),
        *     @OA\Response(
        *         response=404,
        *         description="Car Model not found"
        *     )
        * )
    */
    public function destroy($carModelId)
    {
        $carModel = $this->carModel->find($carModelId);

        // If the resource was not found, return with 404 and a error msg
       if($carModel === null){
           return response()->json(['Error' =>'Car Model not found'], 404);
       }

       Storage::disk('public')->delete($carModel->image);

       // If the resource was found,delete the resource and return with 200
       $this->carModel->destroy($carModelId);
       return response()->json(['msg' => "Car Model deleted successfully"], 200);
    }
}
