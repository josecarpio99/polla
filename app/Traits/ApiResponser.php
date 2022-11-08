<?php

namespace App\Traits;

trait ApiResponser
{
    protected function successResponse($data, $message = null, $code = 200)
	{
		return response()->json([
			'status'=> 'Success',
			'message' => $message,
			'data' => $data
		], $code);
	}

	protected function errorResponse($message = null, $code)
	{
		return response()->json([
			'status'=>'Error',
			'message' => $message,
			'data' => null
		], $code);
	}

    protected function createdResponse($data)
    {
        return response()->json([
			'status'=>'Success',
			'data' => $data
		], 201);
    }

    protected function noContentResponse()
    {
        return response(NULL, 204);
    }

    protected function notFound($message = 'Object not found')
    {
        return response()->json([
			'status'=>'Error',
			'message' => $message
		], 404);
    }
}
