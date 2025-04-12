<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Order\OrderStoreRequest;
use App\Http\Requests\Api\v1\Order\OrderUpdateRequest;
use App\Http\Resources\Api\V1\Order\OrderResourceCollection;
use App\Http\Resources\Api\V1\Order\OrderResource;
use App\Repositories\OrderRepositoey;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(public OrderRepositoey $orderRepositoey) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = $this->orderRepositoey->getAllOrders();

        return response()->json(
            [
                'message' => __('success'),
                'data'    => new OrderResourceCollection($orders),
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStoreRequest $request)
    {
        $order = $this->orderRepositoey->createOrder($request->validated());

        if ($order) {
            (new \App\Jobs\OrderMatchJob())->dispatch();
        }

        if ($order) {
            return response()->json(
                [
                    'message' => __('success'),
                    'data'    => new OrderResource($order),
                ],
                Response::HTTP_CREATED
            );
        }

        return response()->json(
            [
                'message' => __('error'),
                'data'    => null,
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request, string $id)
    {
        $order = $this->orderRepositoey->updateOrder($id, $request->validated());

        if ($order) {
            return response()->json(
                [
                    'message' => __('success'),
                    'data'    => new OrderResource($order),
                ],
                Response::HTTP_OK
            );
        }

        return response()->json(
            [
                'message' => __('error'),
                'data'    => null,
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    public function matchOrders()
    {
        $this->orderRepositoey->matchOrders();
        return __('success');
    }
}
