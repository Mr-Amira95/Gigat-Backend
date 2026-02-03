<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlockResource;
use App\Models\Block;
use App\Models\User;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function list()
    {
        $user = auth()->user();

        $blocks = Block::with('blockedUser') // eager load user info
            ->where('blocker_id', $user->id)
            ->get();

        return $this->successResponse(__('success'), BlockResource::collection($blocks));
    }


    public function blockOrUnblock(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = auth()->user();
        $blockedUser = User::find($request->user_id);

        if (!$blockedUser) {
            return $this->errorResponse(__('user_not_found'), 404);
        }

        // Determine type (freelancer or client)
        $blockerType = $user->freelancer ? 'freelancer' : 'client';
        $blockedType = $blockedUser->freelancer ? 'freelancer' : 'client';

        // Check if already blocked
        $exists = Block::where('blocker_id', $user->id)
            ->where('blocked_id', $blockedUser->id)
            ->first();

        if ($exists) {
            // Unblock
            $exists->delete();
            return $this->successResponse(__('user_unblocked_successfully'), 200);
        } else {
            // Block
            Block::create([
                'blocker_id'   => $user->id,
                'blocked_id'   => $blockedUser->id,
                'blocker_type' => $blockerType,
                'blocked_type' => $blockedType,
            ]);

            return $this->successResponse(__('user_blocked_successfully'), 201);
        }
    }
}
