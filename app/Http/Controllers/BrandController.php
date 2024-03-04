<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
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
        $newBrand = new Brand();
        $newBrand->fill($request->all());
        $newBrand->save();
        return $newBrand;
    }

    /**
     * Display the specified resource.
     */
    public function show($brandId)
    {
        $brand = $this->brand->find($brandId);
        return $brand;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $brandId)
    {
        $brand = $this->brand->find($brandId);
        $brand->update($request->all());
        return $brand;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($brandId)
    {
        return $this->brand->destroy($brandId);
    }
}
