<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $Orders = Order::all();

        return sendResponse(OrderResource::collection($Orders), 'Orders retrieved successfully.');
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
            'catalog_id'       => 'required|min:5',
            'customer_name' => 'required|min:5',
            'price' => 'required|min:5'
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        try {
            $Order    = Order::create([
                'catalog_id'    => $request->catalog_id,
                'customer_name' => $request->customer_name,
                'price'         => $request->price
            ]);
            $success = new OrderResource($Order);
            $message = 'Yay! A Order has been successfully created.';
        } catch (Exception $e) {
            $success = [];
            $message = 'Oops! Unable to create a new Order.';
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
        $Order = Order::find($id);

        if (is_null($Order)) return sendError('Order not found.');

        return sendResponse(new OrderResource($Order), 'Order retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Order    $Order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Order $Order)
    {
        $validator = Validator::make($request->all(), [
            'catalog_id'    => $request->catalog_id,
            'customer_name' => $request->customer_name,
            'price'         => $request->price
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        try {

            $Order->catalog_id       = $request->catalog_id;
            $Order->customer_name = $request->customer_name;
            $Order->price = $request->price;
            $Order->save();

            $success = new OrderResource($Order);
            $message = 'Yay! Order has been successfully updated.';
        } catch (Exception $e) {
            $success = [];
            $message = 'Oops, Failed to update the Order.';
        }

        return sendResponse($success, $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $Order
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Order $Order)
    {
        try {
            $Order->delete();
            return sendResponse([], 'The Order has been successfully deleted.');
        } catch (Exception $e) {
            return sendError('Oops! Unable to delete Order.');
        }
    }
}