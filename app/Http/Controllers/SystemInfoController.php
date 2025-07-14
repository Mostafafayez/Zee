<?php

namespace App\Http\Controllers;

use App\Models\SystemInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SystemInfoController extends Controller
{
    public function index()
    {
        return response()->json(SystemInfo::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            // 'created_by' => 'required|string',
            'logo' => 'nullable|file|mimes:png,jpg,jpeg,webp',
        ]);

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('system_logos', 'public')
            : null;

        $systemInfo = SystemInfo::create([
            'system_name' => $request->system_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $logoPath,
            // 'created_by' =>  $request->created_by,
        ]);

        return response()->json($systemInfo, 201);
    }

    public function show($id)
    {
        $systemInfo = SystemInfo::findOrFail($id);
        return response()->json($systemInfo);
    }

    public function update(Request $request, $id)
    {
        $systemInfo = SystemInfo::findOrFail($id);

        $request->validate([
            'system_name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'phone' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'logo' => 'nullable|file|mimes:png,jpg,jpeg,webp',
        ]);

        if ($request->hasFile('logo')) {
            if ($systemInfo->logo) {
                Storage::disk('public')->delete($systemInfo->logo);
            }
            $systemInfo->logo = $request->file('logo')->store('system_logos', 'public');
        }

        $systemInfo->update($request->only(['system_name', 'email', 'phone', 'address']));

        return response()->json($systemInfo);
    }

    public function destroy($id)
    {
        $systemInfo = SystemInfo::findOrFail($id);
        if ($systemInfo->logo) {
            Storage::disk('public')->delete($systemInfo->logo);
        }
        $systemInfo->delete();

        return response()->json(['message' => 'System info deleted']);
    }
}
