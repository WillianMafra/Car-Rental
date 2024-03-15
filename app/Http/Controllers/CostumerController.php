<?php

namespace App\Http\Controllers;

use App\Models\Costumer;
use App\Repositories\costumerRepository;
use Illuminate\Http\Request;

class costumerController extends Controller
{

    // To debug in POSTMAN, set in Header Accept => application/json
    public $costumer;
    
    public function __construct(Costumer $costumer)
    {
        $this->costumer = $costumer;   
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $costumerRepository = new costumerRepository($this->costumer);

        // If the user specified the columns
        if($request->has('columns') && $request->columns != '')
        {
            $columns = $request->columns;
            $costumerRepository->selectColumns($columns);
        }

        // Use filter=name:=:costumer;doors:=:4
        if($request->has('filter') && $request->filter != ''){
            $filters = explode(';',$request->filter);
            foreach($filters as $key => $value){
                $condition = explode(':', $value);
                $costumerRepository->filter($condition[0], $condition[1], $condition[2]);
            }
        }

        return $costumerRepository->getResults();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validating the data
        $request->validate($this->costumer->rules());

        // store the new costumer
        $newCostumer = new costumer();

        $newCostumer->fill($request->all());
        $newCostumer->save();

        return response()->json(['msg' => "Costumer $request->name created successfully"], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($costumerId)
    {
        $costumer = $this->costumer->find($costumerId);
        
        // If the resource was not found, return with 404 and a error msg
        if($costumer === null){
            return response()->json(['Error' =>'Costumer not found'], 404);
        }

        // If the resource was found, return the data with 200
        return response()->json($costumer, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $costumerId)
    {

        $costumer = $this->costumer->find($costumerId);

        // If the resource was not found, return with 404 and a error msg
        if($costumer === null){
            return response()->json(['Error' =>'Costumer not found'], 404);
        }
        // doing the full data validation
        $request->validate($this->costumer->rules($costumerId));

        $costumer->fill($request->all());

        // If the resource was found, updade the resource and return with 200
        $costumer->save();
        return response()->json(['msg' => "Costumer updated successfully"], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($costumerId)
    {
        $costumer = $this->costumer->find($costumerId);

         // If the resource was not found, return with 404 and a error msg
        if($costumer === null){
            return response()->json(['Error' =>'Costumer not found'], 404);
        }

        // If the resource was found,delete the resource and return with 200
        $this->costumer->destroy($costumerId);
        return response()->json(['msg' => "Costumer deleted successfully"], 200);
    }
}
