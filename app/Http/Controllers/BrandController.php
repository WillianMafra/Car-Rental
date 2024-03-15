<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Repositories\brandRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{

    // To debug in POSTMAN, set in Header Accept => application/json
    public $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;   
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $brandRepository = new brandRepository($this->brand);

        if($request->has('carmodel_columns') && $request->carmodel_columns != '')
        {
            $columns = 'carModel:id,brand_id,'.$request->carmodel_columns;
            $brandRepository->selectRelationatedColumns($columns); 
        } else 
        {
            $brandRepository->selectRelationatedColumns('CarModel');
        }

        // If the user specified the columns
        if($request->has('columns') && $request->columns != '')
        {
            $columns = $request->columns;
            $brandRepository->selectColumns($columns);
        }

        // Use filter=name:=:brand;doors:=:4
        if($request->has('filter') && $request->filter != ''){
            $filters = explode(';',$request->filter);
            foreach($filters as $key => $value){
                $condition = explode(':', $value);
                $brandRepository->filter($condition[0], $condition[1], $condition[2]);
            }
        }

        return $brandRepository->getResults();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validating the data
        $request->validate($this->brand->rules());

        // Saving the file
        $image = $request->file('image');
        $imageUrn = $image->store('images', 'public');

        $data = [
            'name' => $request->get('name'),
            'image' => $imageUrn
        ];

        // store the new brand
        $newBrand = new Brand();

        $newBrand->fill($data);
        $newBrand->save();

        return response()->json(['msg' => "Brand $request->name created successfully"], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($brandId)
    {
        $brand = $this->brand->with('carModel')->find($brandId);
        
        // If the resource was not found, return with 404 and a error msg
        if($brand === null){
            return response()->json(['Error' =>'Brand not found'], 404);
        }

        // If the resource was found, return the data with 200
        return response()->json($brand, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $brandId)
    {

        $brand = $this->brand->find($brandId);

        // If the resource was not found, return with 404 and a error msg
        if($brand === null){
            return response()->json(['Error' =>'Brand not found'], 404);
        }
        // If the method is PATCH, we need to validete only the data was send
        if($request->method() === 'PATCH'){
            $dynamicRules = [];

            foreach($brand->rules() as $input => $rule){
                if(array_key_exists($input, $request->all())){
                    $dynamicRules[$input] = $rule;
                }
            }
            $request->validate($this->brand->dynamicrules($dynamicRules, $brandId));
        } else {
            // doing the full data validation
            $request->validate($this->brand->rules($brandId));
        }

        $brand->fill($request->all());

        if($request->get('image')){
        // Saving the file
            $image = $request->file('image');
            $imageUrn = $image->store('images', 'public');

            if($imageUrn != ''){
                Storage::disk('public')->delete($brand->image);
            }

            $brand->image = $imageUrn;
    
        }

        // If the resource was found, updade the resource and return with 200
        $brand->save();
        return response()->json(['msg' => "Brand updated successfully"], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($brandId)
    {
        $brand = $this->brand->find($brandId);

         // If the resource was not found, return with 404 and a error msg
        if($brand === null){
            return response()->json(['Error' =>'Brand not found'], 404);
        }

        Storage::disk('public')->delete($brand->image);

        // If the resource was found,delete the resource and return with 200
        $this->brand->destroy($brandId);
        return response()->json(['msg' => "Brand deleted successfully"], 200);
    }
}
