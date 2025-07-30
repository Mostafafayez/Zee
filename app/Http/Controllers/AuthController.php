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
            'user' => $user,
            'role' => $user->getRoleNames(),
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
                'your_role' => 'unauthenticated',
            ], 403);
        }
    }

    // Validation
    $request->validate([
        'name' => 'required|string',
        'phone' => 'required|numeric|unique:users',
        'email' => 'required|string|email|unique:users',
        'address' => 'required|string',
        'password' => 'required|string|confirmed',
        'role' => 'required|in:merchant,courier,admin',

        // Courier-specific fields
        'national_id' => 'required_if:role,courier|string|unique:couriers,national_id',
        'vehicle_type' => 'required_if:role,courier|string',
        'license_number' => 'required_if:role,courier|string',
        'vehicle_plate_number' => 'required_if:role,courier|string',
        'license_image' => 'required_if:role,courier|file|mimes:jpg,jpeg,png,pdf',
        'vehicle_plate_image' => 'required_if:role,courier|file|mimes:jpg,jpeg,png,pdf',

        // Merchant-specific fields
        'company_name' => 'required_if:role,merchant|string',
    ]);


    $newUser = User::create([
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'address' => $request->address,
        'password' => Hash::make($request->password),
        'role' => $request->role,
    ]);

    $newUser->assignRole(roles: $request->role);


    if ($request->role === 'courier') {

        $licenseImagePath = $request->hasFile('license_image')
            ? $request->file('license_image')->store('couriers/licenses', 'public')
            : null;

        $vehiclePlateImagePath = $request->hasFile('vehicle_plate_image')
            ? $request->file('vehicle_plate_image')->store('couriers/plates', 'public')
            : null;

        // Create the courier
        $courier = Courier::create([
            'user_id' => $newUser->id,
            'national_id' => $request->national_id,
            'vehicle_type' => $request->vehicle_type,
            'rating' => 0,
            'license_number' => $request->license_number,
            'vehicle_plate_number' => $request->vehicle_plate_number,
            'license_image' => $licenseImagePath,
            'vehicle_plate_image' => $vehiclePlateImagePath,
        ]);
    }

    // Handle merchant logic
    if ($request->role === 'merchant') {
        $merchant = Merchant::create([
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



public function countUsersByRole()
{
    $users = User::with('roles')->get();


    $countByRole = $users->groupBy(function ($user) {
        return $user->roles->pluck('name')->first();
    })->map(function ($group) {
        return $group->count();
    });

    return response()->json([
        'data' => $countByRole
    ]);
}

}
