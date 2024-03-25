<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Repositories\leaseRepository;
use Illuminate\Http\Request;
use App\Models\Car;
use Tymon\JWTAuth\Facades\JWTAuth;

class LeaseController extends Controller
{

    // To debug in POSTMAN, set in Header Accept => application/json
    public $lease;

    public function __construct(Lease $lease)
    {
        $this->lease = $lease;   
    }
    
    /**
        * @OA\Get(
        *     path="/api/lease",
        *     summary="Get list of leases",
        *     tags={"Leases"},
        *     @OA\Parameter(
        *         name="carmodel_columns",
        *         in="query",
        *         description="(optional)Columns to select from realationated tables. ex: /api/car?car_model_id==:1",
        *         @OA\Schema(
        *             type="object",
        *             @OA\Property(property="costumer_name", type="text", example="ilike:%user%"),
        *             @OA\Property(property="car_model_id", type="text", example="=:5"),
        *             @OA\Property(property="car_plate", type="text", example="=:MGQ3058"),
        *         )
        *     ),
        *     @OA\Parameter(
        *         name="filters",
        *         in="query",
        *         description="Filters for leases",
        *         @OA\Schema(
        *             type="object",
        *             @OA\Property(property="start_date", type="text", example="=:2024-01-01"),
        *             @OA\Property(property="expected_end_date", type="text", example="=:2024-12-31"),
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
        $leaseRepository = new leaseRepository($this->lease);

        // Select columns in car and user table
        $relationatedTables = ['user', 'car', 'car.carModel'];
        $leaseRepository->selectRelationatedColumns($relationatedTables);
        
        // Filter on lease table
        $filters = ['initial_date', 'final_date'];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                if($filter == 'initial_date'){
                    $column = 'start_date';
                } else if ($filter == 'final_date'){
                    $column = 'expected_end_date';
                }
                $leaseRepository->filter($request->$filter, $column);
            }
        }

        // Filter on relationated tables (car_models, cars, costumer)
        $relationatedFilters = ['car_model_id', 'costumer_name', 'car_plate'];
        foreach ($relationatedFilters as $filter) {
             if ($request->filled($filter)) {

                switch ($filter){
                    case 'car_model_id':
                        $column = 'id';
                        $relationatedTable = 'car.carModel';
                        break;
                    case 'costumer_name':
                        $column = 'name';
                        $relationatedTable = 'user';
                        break;
                    case 'car_plate':
                        $column = 'plate';
                        $relationatedTable = 'car';
                        break;
                    default:
                    return http_response_code(400);
                }
                $leaseRepository->filterRelationatedColumns($relationatedTable, $request->$filter, $column);
             }
         }

        if($request->has('paginate') && $request->paginate != ''){
           return $leaseRepository->getPaginatedResults($request->paginate);
        }

        return $leaseRepository->getResults();
    }

    /**
         * @OA\Post(
         *     path="/api/lease",
         *     summary="Create a new lease",
         *     tags={"Leases"},
         *     @OA\RequestBody(
         *         required=true,
         *         description="Lease data to be created",
         *         @OA\MediaType(
         *             mediaType="multipart/form-data",
         *             @OA\Schema(
         *                 type="object",
         *                 @OA\Property(
         *                     property="id",
         *                     type="integer",
         *                     description="Car ID",
         *                     example="1"
         *                 ),
         *                 @OA\Property(
         *                     property="start_date",
         *                     type="string",
         *                     description="Lease Start Date",
         *                     example="2024-01-01 00:00:00"
         *                 ),
         *                 @OA\Property(
         *                     property="expected_end_date",
         *                     type="string",
         *                     description="Lease Expected End Date",
         *                     example="2024-01-01 12:00:00"
         *                 ),
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Lease created successfully",
         *     ),
         *     @OA\Response(
         *         response=422,
         *         description="Validation error"
         *     )
         * )
    */
    public function store(Request $request)
    {
        // store the new lease
        $newLease = new lease();
        $user = JWTAuth::toUser();

        $car = Car::findOrFail($request->get('id'));
        $newLease->user_id = $user->id;
        $newLease->car_id = $request->get('id');
        $newLease->start_date = $request->get('start_date');
        $newLease->expected_end_date = $request->get('expected_end_date');
        $newLease->initial_km = $car->km;
        $newLease->save();
        
        if(isset($newLease->id)){
            $car->avaliable = 0;
            $car->save();
        }
        return response()->json([$newLease], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/lease/{leaseId}",
     *     summary="Get details of a specific lease",
     *     tags={"Leases"},
     *     @OA\Parameter(
     *         name="leaseId",
     *         in="path",
     *         description="ID of the lease",
     *         required=true,
     *         example="3",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A json with lease data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lease not found"
     *     )
     * )
     */
    public function show($leaseId)
    {
        $lease = $this->lease->with(['car','user'])->find($leaseId);
        
        // If the resource was not found, return with 404 and a error msg
        if($lease === null){
            return response()->json(['Error' =>'Lease not found'], 404);
        }

        // If the resource was found, return the data with 200
        return response()->json($lease, 200);
    }

    /**
         * @OA\post(
         *     path="/api/lease/{leaseId}",
         *     summary="Update a lease",
         *     tags={"Leases"},
         *     @OA\Parameter(
         *         name="leaseId",
         *         in="path",
         *         description="ID of the lease",
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
         *                     property="_method",
         *                     type="string",
         *                     description="method for the request",
         *                     example="patch"
         *                 ),
         *                 @OA\Property(
         *                     property="final_km",
         *                     type="integer",
         *                     description="Final KM of the car",
         *                     example="20000"
         *                 ),
         *                 @OA\Property(
         *                     property="actual_end_date",
         *                     type="text",
         *                     description="Actual end date of the lease",
         *                     example="2024-01-01 00:00:00"
         *                 ),
         *              )
         *          )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Successful operation",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="msg", type="string", example="Lease updated successfully")
         *         )
         *     ),
         *     @OA\Response(
         *         response=404,
         *         description="Lease not found"
         *     )
         * )
    */    
    public function update(Request $request, $leaseId)
    {

        $lease = $this->lease->find($leaseId);

        // If the resource was not found, return with 404 and a error msg
        if($lease === null){
            return response()->json(['Error' =>'Lease not found'], 404);
        }
        // If the method is PATCH, we need to validete only the data was send
        if($request->method() === 'PATCH'){
            $dynamicRules = [];

            foreach($lease->rules() as $input => $rule){
                if(array_key_exists($input, $request->all())){
                    $dynamicRules[$input] = $rule;
                }
            }
            $request->validate($this->lease->dynamicrules($dynamicRules, $leaseId));
        } else {
            // doing the full data validation
            $request->validate($this->lease->rules($leaseId));
        }

        $lease->fill($request->all());

        // If the resource was found, updade the resource and return with 200
        $lease->save();
        return response()->json(['msg' => "Lease updated successfully"], 200);
    }

    /**
         * @OA\Delete(
         *     path="/api/lease/{leaseId}",
         *     summary="Delete a lease",
         *     tags={"Leases"},
         *     @OA\Parameter(
         *         name="leaseId",
         *         in="path",
         *         description="ID of the lease",
         *         required=true,
         *         example="1",
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Successful operation",
         *         @OA\JsonContent(
         *             type="object",
         *             @OA\Property(property="msg", type="string", example="Lease deleted successfully")
         *         )
         *     ),
         *     @OA\Response(
         *         response=404,
         *         description="Lease not found"
         *     )
         * )
    */

    public function destroy($leaseId)
    {
        $lease = $this->lease->find($leaseId);

         // If the resource was not found, return with 404 and a error msg
        if($lease === null){
            return response()->json(['Error' =>'Lease not found'], 404);
        }

        // If the resource was found,delete the resource and return with 200
        $this->lease->destroy($leaseId);
        return response()->json(['msg' => "Lease deleted successfully"], 200);
    }

    public function userLeases(Request $request)
    {
        $leaseRepository = new leaseRepository($this->lease);
        
        // Select columns in car and user table
        $relationatedTables = ['user', 'car', 'car.carModel'];
        $leaseRepository->selectRelationatedColumns($relationatedTables);

        
        // Filter on lease table
        $filters = ['start_date', 'expected_end_date'];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $leaseRepository->filter($request->$filter, $filter);
            }
        }
        $userId = '=:'.JWTAuth::toUser()->id;
        $leaseRepository->filter($userId, 'user_id');

        if($request->has('paginate') && $request->paginate != ''){
            return $leaseRepository->getPaginatedResults($request->paginate);
         }

    }
}
