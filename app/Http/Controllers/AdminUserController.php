<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{

    protected function authorizeAction(string $action, User $user)
    {
        $map = [
            'manage'  => $user->canManageUsers(),
            'suspend' => $user->canSuspendUsers(),
            'delete'  => $user->canDeleteUsers(),
        ];

        abort_unless($map[$action] ?? false, 403);
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->canManageUsers(), 403);

        $users = User::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            })
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when(
                $request->membership,
                fn($q) =>
                $q->where('membership', $request->membership)
            )
            ->latest()
            ->paginate(10);

        $users->appends($request->only('search', 'role', 'status'));


        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canManageUsers(), 403);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:50|unique:users,username',
            'email'      => 'required|email|unique:users,email',
            'role'       => ['required', Rule::in(['admin', 'user'])],
            'status'     => ['nullable', Rule::in(['active', 'suspended'])],
            'membership' => ['nullable', Rule::in(['free', 'premium'])],
            'password'   => 'nullable|string|min:8',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::create($data);

        return new UserResource($user);
    }

    public function update(Request $request, $id)
    {
        $authUser = $request->user();
        abort_unless($authUser->canManageUsers(), 403);

        $user = User::findOrFail($id);

        // Prevent self-destruction scenarios
        if ($authUser->id === $user->id) {
            abort(403, 'You cannot modify your own account.');
        }

        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'role'       => ['sometimes', Rule::in(['admin', 'user'])],
            'status'     => ['sometimes', Rule::in(['active', 'suspended'])],
            'membership' => ['sometimes', Rule::in(['free', 'premium'])],
        ]);

        $user->update($data);

        return new UserResource($user);
    }

    public function destroy($id)
    {
        abort_unless(request()->user()->canDeleteUsers(), 403);

        $user = User::findOrFail($id);

        if (request()->user()->id === $user->id) {
            abort(403, 'You cannot delete your own account.');
        }

        $user->delete();

        return response()->noContent();
    }

    public function bulkAction(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'action' => 'required|in:activate,suspend,delete',
            'ids'    => 'required|array',
            'ids.*'  => 'exists:users,id',
        ]);

        match ($data['action']) {
            'activate' => $this->authorizeAction('manage', $user),
            'suspend'  => $this->authorizeAction('suspend', $user),
            'delete'   => $this->authorizeAction('delete', $user),
        };

        match ($data['action']) {
            'activate' => User::whereIn('id', $data['ids'])->update(['status' => 'active']),
            'suspend'  => User::whereIn('id', $data['ids'])->update(['status' => 'suspended']),
            'delete'   => User::whereIn('id', $data['ids'])->delete(),
        };

        if (in_array($user->id, $data['ids'])) {
            abort(403, 'You cannot perform bulk actions on yourself.');
        }


        return response()->json(['success' => true]);
    }

    public function show($id)
    {
    // Find the user and count their related 'resumes'
    $user = User::withCount('resumes')->find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json($user);
    }
}
