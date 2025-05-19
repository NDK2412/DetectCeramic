<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function submitRating(Request $request)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
            'rating' => 'required|numeric|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $user = User::find($request->userId);
        $user->update([
            'rating' => $request->rating,
            'feedback' => $request->feedback,
        ]);

        return response()->json(['success' => true]);
    }

}