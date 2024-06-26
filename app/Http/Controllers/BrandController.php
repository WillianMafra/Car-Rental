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
 * @OA\Get(
 *     path="/api/brand",
 *     summary="Get a list of brands",
 *     tags={"Brands"},
 *      @OA\Parameter(
 *         name="columns",
 *         in="query",
 *         description="Columns for cars table. ex:",
 *         @OA\Schema(
 *             type="object",
 *             @OA\Property(property="id", type="text", example="=:1"),
 *             @OA\Property(property="name", type="text", example="ilike:%ford%"),
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
        $brandRepository = new brandRepository($this->brand);

        // If the user specified the columns
        if ($request->filled('columns')) {
            $columns = $request->columns;
            $brandRepository->selectColumns($columns);
        }

        // Filter on cars table
        $filters = ['name', 'id'];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $brandRepository->filter($request->$filter, $filter);
            }
        }

        if($request->has('paginate') && $request->paginate != ''){
           return $brandRepository->getPaginatedResults($request->paginate);
        }

        return $brandRepository->getResults();
    }

    /**
     * @OA\Post(
     *     path="/api/brand",
     *     summary="Create a new brand",
     *     tags={"Brands"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Brand data to be created",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the brand",
     *                     example="Brand X"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file of the brand logo"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="msg", type="string", example="Brand X created successfully")
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
     * @OA\Get(
     *     path="/api/brand/{brandId}",
     *     summary="Get details of a specific brand",
     *     tags={"Brands"},
     *     @OA\Parameter(
     *         name="brandId",
     *         in="path",
     *         description="ID of the brand",
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
     *         description="Brand not found"
     *     )
     * )
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
     * @OA\POST(
     *     path="/api/brand/{brandId}",
     *     summary="Update a brand",
     *     tags={"Brands"},
     *     @OA\Parameter(
     *         name="brandId",
     *         in="path",
     *         description="ID of the brand",
     *         required=true,
     *         example="1",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="ford",
     *                     description="Name of the brand"
     *                 ),
     *                     @OA\Property(
     *                     property="_method",
     *                     type="string",
     *                     description="method for the request",
     *                     example="put"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file of the brand"
     *                 )
     *             )
     *         )
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
        if($request->file('image') && $request->file('image')!= ''){
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
        * @OA\Delete(
        *     path="/api/brand/{brandId}",
        *     summary="Delete a brand",
        *     tags={"Brands"},
        *     @OA\Parameter(
        *         name="brandId",
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
        *             @OA\Property(property="msg", type="string", example="Brand deleted successfully")
        *         )
        *     ),
        *     @OA\Response(
        *         response=404,
        *         description="Brand not found"
        *     )
        * )
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
