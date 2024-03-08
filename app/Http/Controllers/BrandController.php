<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

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
    public function index()
    {
        $brands = $this->brand->all();
        return $brands;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validating the data
        $request->validate($this->brand->rules());

        // store the new brand
        $newBrand = new Brand();
        $newBrand->fill($request->all());
        $newBrand->save();

        return response()->json(['msg' => "Brand $request->name created successfully"], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($brandId)
    {
        $brand = $this->brand->find($brandId);
        
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

        // If the resource was found, updade the resource and return with 200
        $brand->update($request->all());
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

        // If the resource was found,delete the resource and return with 200
        $this->brand->destroy($brandId);
        return response()->json(['msg' => "Brand deleted successfully"], 200);
    }
}
