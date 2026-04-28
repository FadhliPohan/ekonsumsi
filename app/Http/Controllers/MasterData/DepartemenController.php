<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\masterData\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view departemen')->only(['index', 'show']);
        $this->middleware('permission:create departemen')->only(['store']);
        $this->middleware('permission:edit departemen')->only(['update']);
        $this->middleware('permission:delete departemen')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Departemen::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code_departement', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        if ($request->ajax()) {
            $departements = $query->latest()->paginate(10);
            return view('masterdata.departement.table', compact('departements'))->render();
        }

        $departements = $query->latest()->paginate(10);
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
