<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DosageForm;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dosage_forms = DosageForm::latest()->get();
        $categories = Category::latest()->get();
        return view('category.index', compact('categories','dosage_forms'));
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
            'name' => ['required', 'string', 'max:255', 'unique:categories,name']
        ]);

        $category = Category::create([
            'name' => $request->name
        ]);

        return redirect()->route('categories.index')->with('success', 'Category Create Successfully');
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
    public function edit(Category $category)
    {
        return view('category.index', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'=>['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
        ]);

        $category->update([
            'name'=>$request->name
        ]);

        return redirect()->route('categories.index')->with('success','Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {

        // Prevent delete if category in use
        if($category->medicines()->exists()){
            return redirect()->route('categories.index')->with('error', 'Opps! Cannot delete category in use.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category Deleted');
    }
}
