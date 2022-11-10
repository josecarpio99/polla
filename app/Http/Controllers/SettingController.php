<?php

namespace App\Http\Controllers;

use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        return SettingResource::collection(Setting::all());
    }
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'play_cost'         => ['required', 'integer'],
            'system_percentage' => ['required', 'digits_between:0,100']
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->first(), 400);
        }

        Setting::where('name', 'play_cost')->update(['value' => $request->play_cost]);
        Setting::where('name', 'system_percentage')->update(['value' => $request->system_percentage]);

        return $this->successResponse(NULL, 'Settings updated successfully');
    }
}
