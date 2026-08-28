<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(['permission:edit users'])->only(['edit','update']);
        $this->middleware(['permission:create users'])->only(['create','store']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereNotNull('username')->with('roles')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:users,name'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        //event(new Registered($user));

        return redirect(route('users.index', absolute: false))->with('success', 'User Created Successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name','name');
        $userRole = $user->roles->pluck('name')->first();

        return view('users.edit', compact('user','roles','userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:users,name,' . $user->id],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'exists:roles,name'],
        ]);

        // Update Basic Info
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
        ]);

        // Update password if only provided
        if($request->filled('password'))
        {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        // Updating the role
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User details updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // prevent deleting self
        if (auth()->id() == $user->id)
        {
            return redirect()->route('users.index')->with('error', 'You cannot delete userself');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User '.$user->name.' deleted successfully');

    }
}
