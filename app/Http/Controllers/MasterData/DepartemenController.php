<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\masterData\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepartemenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departements = Departemen::latest()->paginate(10);
            return view('masterdata.departement.table', compact('departements'))->render();
        }

        $departements = Departemen::latest()->paginate(10);
        return view('masterdata.departement.index', compact('departements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code_departement' => 'required|string|max:255|unique:departemens,code_departement',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        Departemen::create($request->all());

        return response()->json(['success' => 'Departemen created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $departemen = Departemen::where('uuid', $uuid)->firstOrFail();
        return response()->json($departemen);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $departemen = Departemen::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'code_departement' => 'required|string|max:255|unique:departemens,code_departement,' . $departemen->id,
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $departemen->update($request->all());

        return response()->json(['success' => 'Departemen updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $departemen = Departemen::where('uuid', $uuid)->firstOrFail();
        $departemen->delete();

        return response()->json(['success' => 'Departemen deleted successfully.']);
    }
}
