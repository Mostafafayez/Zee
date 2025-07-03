<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class Price_listController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:areas,name',
            'shipping_price' => 'required|numeric|min:0',
        ]);

        $area = Area::create($request->only('name', 'shipping_price'));

        return response()->json($area, 201);
    }

    // 📄 Get All Areas
    public function index()
    {
        return response()->json(Area::all());
    }

    // 👁️ Show One Area
    public function show($id)
    {
        $area = Area::findOrFail($id);
        return response()->json($area);
    }

    // ✏️ Update Area
    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|unique:areas,name,' . $id,
            'shipping_price' => 'sometimes|required|numeric|min:0',
        ]);

        $area->update($request->only('name', 'shipping_price'));

        return response()->json($area);
    }

    // 🗑️ Delete Area
    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();

        return response()->json(['message' => 'Area deleted successfully']);
    }
}
