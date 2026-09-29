<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FcmTokenController extends Controller
{
    /**
     * Save or refresh the FCM token for the currently authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string|min:100',
        ]);

        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $deviceInfo = substr($request->header('User-Agent', ''), 0, 255);

        FcmToken::saveToken(
            userId:     auth()->id(),
            token:      $request->input('token'),
            deviceInfo: $deviceInfo
        );

        return response()->json(['status' => 'ok']);
    }
}