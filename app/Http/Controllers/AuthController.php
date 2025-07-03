<?php
namespace App\Http\Controllers;
use App\Models\Courier;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:users',
            'email' => 'required|string|unique:users',

            'password' => 'required|string|confirmed',
            'role' => 'required|in:admin,merchant,courier'
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' =>  $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|confirmed',
        ]);

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json(['message' => 'Password updated']);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }






    public function store(Request $request)
{

    if ($request->role === 'courier') {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json([
                'message' => 'Only admin users can create couriers.',
                'your_role' =>  'unauthenticated',
            ], 403);
        }
    }
    $request->validate([
        'name' => 'required|string',
        'phone' => 'required|numeric|unique:users',
        'email' => 'required|string|email|unique:users',
        'address' => 'required|string',
        'password' => 'required|string|confirmed',
        'role' => 'required|in:merchant,courier,admin',


        // Courier-specific fields
        'national_id' => 'required_if:role,courier|string|unique:couriers,national_id',
        'vehicle_info' => 'required_if:role,courier|string',
        //merchant case
         'company_name' =>'required_if:role,merchant|string',
    ]);



    // Create the user
    $newUser = User::create([
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'address' => $request->address,
        'password' => Hash::make($request->password),
        'role' => $request->role,
    ]);

    $newUser->assignRole(roles: $request->role);

    // If role is courier, also create courier record
    if ($request->role === 'courier') {
        $courier = Courier::create([
            'user_id' => $newUser->id,
            'national_id' => $request->national_id,
            'vehicle_info' => $request->vehicle_info,
            'rating' => 0,
        ]);
    }


    if ($request->role === 'merchant') {
        $courier = Merchant::create([
            'user_id' => $newUser->id,
            'company_name' => $request->company_name,

        ]);
    }


    return response()->json([
        'message' => ucfirst($request->role) . ' created successfully',
        'user' => $newUser,
        'courier' => $request->role === 'courier' ? $courier ?? null : null,
    ], 201);
}
}
