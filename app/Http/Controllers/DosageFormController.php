<?php

namespace App\Http\Controllers;

use App\Models\DosageForm;
use Illuminate\Http\Request;

class DosageFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=> ['required','string', 'max:255', 'unique:dosage_forms,name'],
            'desciption'=> ['string', 'max:255'],
        ]);

        $dosage_form = DosageForm::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()
                ->route('categories.index')
                ->with('success', 'Dosage Form Create Successfully');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DosageForm $dosageForm)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:dosage_forms,name,'.$dosageForm->id],
            'description' => ['required','string','max:255']
        ]);

        $dosageForm->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()
                ->route('categories.index')
                ->with('success', 'Dosage Form Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DosageForm $dosageForm)
    {
        // Prevent delete if in use
        if($dosageForm->medicines()->exists()){
            return redirect()
                    ->route('categories.index')
                    ->with('error', 'Opps! Cannot delete the dosage form in use');
        }

        $dosageForm->delete();

        return redirect()
                ->route('categories.index')
                ->with('success', 'Dosage Form Deleted');
    }
}
