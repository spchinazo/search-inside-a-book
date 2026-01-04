<?php
namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class GenericResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for items
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request): \Illuminate\Http\JsonResponse {
        return response()->json([
            'message'   => $this->payload['message'] ?? '',
            'status'    => $this->payload['status'] ?? '',
            'payload'   => $this->payload['payload'] ?? null,
        ],$this->payload['code'] ?? 200);
    }

}
