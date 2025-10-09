<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletInvitation;
use App\Models\User;
use Illuminate\Http\Request;

class WalletInvitationController extends Controller
{
    public function store(Request $request, Wallet $wallet)
    {
        // Authorization: only admin can invite
        if ($wallet->admin_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $emailToInvite = $request->input('email');
        $userToInvite = User::where('email', $emailToInvite)->first();

        // Check if user is already a member
        if ($userToInvite) {
            $isMember = $wallet->members()->where('user_id', $userToInvite->id)->exists();
            if ($isMember) {
                return response()->json(['error' => 'User is already a member of this wallet'], 422);
            }
        }

        // Check if an invitation is already pending
        $existingInvitation = WalletInvitation::where('wallet_id', $wallet->id)
            ->where('email', $emailToInvite)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return response()->json(['error' => 'An invitation has already been sent to this user for this wallet'], 422);
        }

        $invitation = WalletInvitation::create([
            'wallet_id' => $wallet->id,
            'email' => $emailToInvite,
            'invited_by' => $request->user()->id,
            'user_id' => $userToInvite ? $userToInvite->id : null,
        ]);

        return response()->json($invitation, 201);
    }

    public function index(Request $request)
    {
        $invitations = WalletInvitation::where('email', $request->user()->email)
            ->where('status', 'pending')
            ->with(['wallet', 'invitedBy'])
            ->get();

        return response()->json($invitations);
    }

    public function accept(Request $request, WalletInvitation $invitation)
    {
        // Authorization: only invited user can accept
        if ($invitation->email !== $request->user()->email) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if invitation is pending
        if ($invitation->status !== 'pending') {
            return response()->json(['error' => 'Invitation is not pending'], 422);
        }

        $invitation->update([
            'status' => 'accepted',
            'user_id' => $request->user()->id,
        ]);

        $invitation->wallet->members()->create([
            'user_id' => $request->user()->id,
            'role' => 'member', // You can make this configurable
        ]);

        return response()->json(['message' => 'Invitation accepted']);
    }

    public function decline(Request $request, WalletInvitation $invitation)
    {
        // Authorization: only invited user can decline
        if ($invitation->email !== $request->user()->email) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if invitation is pending
        if ($invitation->status !== 'pending') {
            return response()->json(['error' => 'Invitation is not pending'], 422);
        }

        $invitation->update(['status' => 'declined']);

        return response()->json(['message' => 'Invitation declined']);
    }
}
