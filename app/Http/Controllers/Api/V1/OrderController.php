<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Order\OrderStoreRequest;
use App\Http\Requests\Api\v1\Order\OrderUpdateRequest;
use App\Http\Resources\Api\V1\Order\OrderResourceCollection;
use App\Http\Resources\Api\V1\Order\OrderResource;
use App\Repositories\OrderRepository;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(public OrderRepository $orderRepository) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = $this->orderRepository->getAllOrders();

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
        $order = $this->orderRepository->createOrder($request->validated());

        if ($order) {
            (new \App\Jobs\OrderMatchJob())
                ->delay(now()->addSeconds(5))
                ->dispatch();
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
        $order = $this->orderRepository->updateOrder($id, $request->validated());

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
        $this->orderRepository->matchOrders();
        return __('success');
    }
}
