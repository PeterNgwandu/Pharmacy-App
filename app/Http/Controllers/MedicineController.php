<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DosageForm;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicines = Medicine::with('category', 'dosageForm', 'creator')->orderBy('name')->get();
        return view('medicines.index', compact('medicines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $dosage_forms = DosageForm::all();
        return view('medicines.create', compact('categories','dosage_forms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string', 'max:255'],
            'generic_name' => ['required', 'string', 'max:255'],
            'brand_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required'],
            'dosage_form_id' => ['required'],
            'strength' => ['required', 'string', 'max:255'],
            'base_unit' => ['required', 'string'],
            'pack_unit' => ['required', 'string'],
            'units_per_pack' => ['required', 'integer', 'min:1'],
            'reorder_level' => ['required', 'integer']
        ]);
        Medicine::create([
            'name' => $request->name,
            'generic_name' => $request->generic_name,
            'brand_name' => $request->brand_name,
            'category_id' => $request->category_id,
            'dosage_form_id' => $request->dosage_form_id,
            'strength' => $request->strength,
            'barcode' => $request->barcode,
            'base_unit' => $request->base_unit,
            'pack_unit' => $request->pack_unit,
            'units_per_pack' => $request->units_per_pack,
            'reorder_level' => $request->reorder_level,
            'status' => 1,
            'created_by' => Auth::id()
        ]);

        return redirect()
                ->route('medicines.index')
                ->with('success', 'Medicine created successfully');
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
    public function edit(Medicine $medicine)
    {

        $categories = Category::all();
        $dosage_forms = DosageForm::all();
        return view('medicines.edit', compact('medicine','categories','dosage_forms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicine $medicine)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['required', 'string', 'max:255'],
            'brand_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'dosage_form_id' => ['required', 'exists:dosage_forms,id'],
            'strength' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'base_unit' => ['required', 'string', 'max:255'],
            'pack_unit' => ['required', 'string', 'max:255'],
            'units_per_pack' => ['required', 'integer', 'min:1'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ]);

        $medicine->update([
            'name' => $request->name,
            'generic_name' => $request->generic_name,
            'brand_name' => $request->brand_name,
            'category_id' => $request->category_id,
            'dosage_form_id' => $request->dosage_form_id,
            'strength' => $request->strength,
            'barcode' => $request->barcode,
            'base_unit' => $request->base_unit,
            'pack_unit' => $request->pack_unit,
            'units_per_pack' => $request->units_per_pack,
            'reorder_level' => $request->reorder_level,
            'status' => $request->status,
        ]);

        return redirect()
                ->route('medicines.index')
                ->with('success', 'Medicine updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return redirect()
                ->route('medicines.index')
                ->with('success', 'Medicine deleted successfully');
            
    }
}
