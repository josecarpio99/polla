<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'play_cost'         => ['required', 'integer'],
            'system_percentage' => ['required', 'digits_between:0,100']
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->first(), 400);
        }

        Setting::where('name', 'play_cost')->update(['play_cost', $request->play_cost]);
        Setting::where('name', 'system_percentage')->update(['system_percentage', $request->system_percentage]);

        return $this->successResponse(NULL, 'Settings updated successfully');
    }
}
