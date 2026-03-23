<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function set_setting(Request $request)
    {
        try {

            // Seule les admins peuvent accéder à cette route
            if (auth()->user()->type_id <= 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized or not allowed to access this route',
                ], 403);
            }

            //validation des données
            $request->validate([
                'title_welcome' => 'nullable',
                'description' => 'nullable',
                'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'phone' => 'nullable',
                'email' => 'nullable|email',
                'address' => 'nullable',
                'social_networks' => 'nullable|array',
                'schedule' => 'nullable',
            ]);
            $data = $request->all();
            $setting = Setting::first();
            //uploade de l'icone
            if ($request->hasFile('favicon')) {
                // suppression de l'ancienne icone si elle existe
                if ($setting && $setting->favicon) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $setting->favicon));
                }   
                $path = $request->file('favicon')->store('settings', 'public');
                $data['favicon'] = Storage::url($path);
            }
            if ($setting) {
                $setting->update($data);
            } else {
                $setting = Setting::create($data);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Setting updated successfully',
                'data' => $setting,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_setting()
    {
        try {
            $setting = Setting::first();
            return response()->json([
                'status' => 'success',
                'message' => 'Setting retrieved successfully',
                'data' => $setting,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
