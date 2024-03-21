<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Repositories\leaseRepository;
use Illuminate\Http\Request;

class leaseController extends Controller
{

    // To debug in POSTMAN, set in Header Accept => application/json
    public $lease;

    public function __construct(Lease $lease)
    {
        $this->lease = $lease;   
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $leaseRepository = new leaseRepository($this->lease);

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
        $relationatedFilters = ['car_model_name', 'costumer_name', 'car_plate'];
        foreach ($relationatedFilters as $filter) {
             if ($request->filled($filter)) {

                switch ($filter){
                    case 'car_model_name':
                        $column = 'name';
                        $relationatedTable = 'car_models';
                        break;
                    case 'costumer_name':
                        $column = 'name';
                        $relationatedTable = 'user';
                        break;
                    case 'car_plate':
                        $column = 'plate';
                        $relationatedTable = 'cars';
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // store the new lease
        $newLease = new lease();

        $newLease->fill($request->all());
        $newLease->save();

        return response()->json([$newLease], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($leaseId)
    {
        $lease = $this->lease->with('car,costumer')->find($leaseId);
        
        // If the resource was not found, return with 404 and a error msg
        if($lease === null){
            return response()->json(['Error' =>'Lease not found'], 404);
        }

        // If the resource was found, return the data with 200
        return response()->json($lease, 200);
    }


    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
}
