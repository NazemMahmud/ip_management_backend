<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Helpers\HttpHandler;
use App\Http\Requests\IpAddress\IpAddressCreateRequest;
use App\Http\Requests\IpAddress\IpAddressUpdateRequest;
use App\Repositories\IPManage\IPAddressRepositoryInterface;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\IpAddress\IpAddressResource;
use App\Http\Resources\IpAddress\IpAddressResourceCollection;

class IPAddressController extends BaseController
{

    public function __construct(protected IPAddressRepositoryInterface $repository)
    {
        $this->resource = IpAddressResource::class;
        $this->resourceCollection = IpAddressResourceCollection::class;
    }

    /**
     * Store a new IP address in db
     * @param IpAddressCreateRequest $request
     * @return JsonResponse
     */
    public function store(IpAddressCreateRequest $request): JsonResponse
    {
        if ($response = $this->repository->storeResource($request->all())) {
            return HttpHandler::successResponse(new $this->resource($response), 201);
        }

        return HttpHandler::errorMessage(Constants::SOMETHING_WENT_WRONG);
    }

    /**
     * Get a single IP address info
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        if ($response = $this->repository->getByColumn('id', $id)) {
            return HttpHandler::successResponse(new $this->resource($response));
        }

        return HttpHandler::errorMessage(Constants::NOT_FOUND, 404);
    }

    /**
     * Update only label (IP can't be modified)
     * @param IpAddressUpdateRequest $request
     * @param mixed $id
     * @return JsonResponse
     */
    public function update(IpAddressUpdateRequest $request, mixed $id): JsonResponse
    {
        $data['label'] = $request->input('label');
        $conditions = ['id' => $id];

        if ($this->repository->updateResource($data, $conditions)) {
            return HttpHandler::successMessage(Constants::UPDATE_SUCCESS);
        }

        return HttpHandler::errorMessage(Constants::SOMETHING_WENT_WRONG);
    }
}
