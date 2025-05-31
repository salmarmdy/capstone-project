<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklistItems = ChecklistItem::latest()->paginate(10);
        return view('pages.checklist.index', compact('checklistItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        ChecklistItem::create([
            'name' => $request->name
        ]);

        return redirect()->route('pages.checklist.index');
    }

    public function edit($id)
    {
        $item = ChecklistItem::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $item = ChecklistItem::findOrFail($id);
        $item->update([
            'name' => $request->name
        ]);

        return redirect()->route('pages.checklist.index');
    }

    public function destroy($id)
    {
        $item = ChecklistItem::findOrFail($id);
        $item->delete();

        return redirect()->route('pages.checklist.index');
    }
}