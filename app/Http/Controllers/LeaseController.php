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

        if($request->has('car_columns') && $request->car_columns != '')
        {
            $columns = 'car:id,'.$request->car_columns;
            $leaseRepository->selectRelationatedColumns($columns); 
        } else 
        {
            $leaseRepository->selectRelationatedColumns('car');
        }

        if($request->has('costumer_columns') && $request->costumer_columns != '')
        {
            $columns = 'costumer:id,'.$request->costumer_columns;
            $leaseRepository->selectRelationatedColumns($columns); 
        } else 
        {
            $leaseRepository->selectRelationatedColumns('costumer');
        }

        // If the user specified the columns
        if($request->has('columns') && $request->columns != '')
        {
            $columns = $request->columns;
            $leaseRepository->selectColumns($columns);
        }

        // Use filter=name:=:lease;doors:=:4
        if($request->has('filter') && $request->filter != ''){
            $filters = explode(';',$request->filter);
            foreach($filters as $key => $value){
                $condition = explode(':', $value);
                $leaseRepository->filter($condition[0], $condition[1], $condition[2]);
            }
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
