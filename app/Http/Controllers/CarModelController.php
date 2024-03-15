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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $carModelRepository = new carModelRepository($this->carModel);

        // If the user specified the brand columns
        if($request->has('brand_columns') && $request->brand_columns != ''){
            $columns = 'brand:id,'.$request->brand_columns;
            $carModelRepository->selectRelationatedColumns($columns);
        } else {
            $carModelRepository->selectRelationatedColumns('brand');                
        }

        // If the user specified the columns
        if($request->has('columns') && $request->columns != ''){
            $columns = 'id,brand_id,'.$request->columns;
            $carModelRepository->selectColumns($columns);  
        }
        
        // Use filter=name:=:carmodel;doors:=:4
        if($request->has('filter') && $request->filter != ''){
            $carModelRepository->filter($request->filter);
        }

        return $carModelRepository->getResults();
    }


    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $carModelId)
    {
        $carModel = $this->carModel->find($carModelId);

        // If the resource was not found, return with 404 and a error msg
        if($carModel === null){
            return response()->json(['Error' =>'Car Model not found'], 404);
        }
        // If the method is PATCH, we need to validate only the data that was sent
        if($request->method() === 'PATCH'){
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

        if($request->get('image')){
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
     * Remove the specified resource from storage.
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
