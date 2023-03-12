<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CatalogResource;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class CatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $Catalogs = Catalog::all();

        return sendResponse(CatalogResource::collection($Catalogs), 'Catalogs retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|min:5',
            'description' => 'required|min:5',
            'price'       => 'required'
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        try {
            $Catalog    = Catalog::create([
                'title'       => $request->title,
                'description' => $request->description,
                'price'       => $request->price
            ]);
            $success = new CatalogResource($Catalog);
            $message = 'Yay! A Catalog has been successfully created.';
        } catch (Exception $e) {
            $success = [];
            $message = 'Oops! Unable to create a new Catalog.';
        }

        return sendResponse($success, $message);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $Catalog = Catalog::find($id);

        if (is_null($Catalog)) return sendError('Catalog not found.');

        return sendResponse(new CatalogResource($Catalog), 'Catalog retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Catalog    $Catalog
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Catalog $Catalog)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|min:5',
            'description' => 'required|min:5',
            'price'       => 'required'
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        try {
            $Catalog->title       = $request->title;
            $Catalog->description = $request->description;
            $Catalog->price       = $request->price;
            $Catalog->save();

            $success = new CatalogResource($Catalog);
            $message = 'Yay! Catalog has been successfully updated.';
        } catch (Exception $e) {
            $success = [];
            $message = 'Oops, Failed to update the Catalog.';
        }

        return sendResponse($success, $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Catalog $Catalog
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Catalog $catalog)
    {
        try {
            $catalog->delete();
            return sendResponse([], 'The Catalog has been successfully deleted.');
        } catch (Exception $e) {
            return sendError('Oops! Unable to delete Catalog.');
        }
    }
}