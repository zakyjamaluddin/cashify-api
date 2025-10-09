<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([$request->user()]);
    }

    public function show(User $user)
    {
        // Authorization check
        if (request()->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        // Authorization check
        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'display_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password_hash'] = bcrypt($validatedData['password']);
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        // Authorization check
        if (request()->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->delete();
        return response()->json(null, 204);
    }
}
