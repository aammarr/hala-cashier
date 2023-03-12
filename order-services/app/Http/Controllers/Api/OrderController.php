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
            'user_id'       => 'required',
            'catalog_id'    => 'required',
            'quantity'      => 'required',
            'price'         => 'required',
            'payment_method'=> 'required'
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        try {
            $Order    = Order::create([
                'user_id'           => $request->user_id,
                'catalog_id'        => $request->catalog_id,
                'quantity'          => $request->quantity,
                'price'             => $request->price,
                'total_price'       => $request->quantity * $request->price,
                'payment_method'    => $request->payment_method,
                'order_status'      => 'pending'
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
            'user_id'           => $request->user_id,
            'catalog_id'        => $request->catalog_id,
            'quantity'          => $request->quantity,
            'price'             => $request->price,
            'total_price'       => $request->total_price,
            'payment_method'    => $request->payment_method,
            'order_status'      => 'pending'
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        try {

            $Order->user_id     = $request->user_id;
            $Order->catalog_id  = $request->catalog_id;
            $Order->quantity    = $request->quantity;
            $Order->price       = $request->price;
            $Order->total_price = $request->total_price;
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