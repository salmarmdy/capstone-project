<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ChecklistItemController extends Controller
{
    /**
     * Display a listing of the checklist items for a specific assessment.
     *
     * @param  string  $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function index($assessmentId)
    {
        $assessment = Assessment::with(['employee', 'vehicle'])->findOrFail($assessmentId);
        $checklistItems = ChecklistItem::where('assessment_id', $assessmentId)
            ->orderBy('category')
            ->orderBy('item_name')
            ->get();
            
        return view('checklist_items.index', compact('assessment', 'checklistItems'));
    }

    /**
     * Show the form for creating a new checklist item.
     *
     * @param  string  $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function create($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        return view('checklist_items.create', compact('assessment'));
    }

    /**
     * Store a newly created checklist item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $assessmentId)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'passed' => 'required|boolean',
            'text_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('checklist_items.create', $assessmentId)
                ->withErrors($validator)
                ->withInput();
        }

        $assessment = Assessment::findOrFail($assessmentId);
        
        $checklistItem = new ChecklistItem();
        $checklistItem->checklist_item_id = (string) Str::uuid();
        $checklistItem->assessment_id = $assessmentId;
        $checklistItem->item_name = $request->item_name;
        $checklistItem->category = $request->category;
        $checklistItem->passed = $request->passed;
        $checklistItem->text_notes = $request->text_notes;
        $checklistItem->save();

        return redirect()->route('checklist_items.index', $assessmentId)
            ->with('success', 'Checklist item created successfully.');
    }

    /**
     * Display the specified checklist item.
     *
     * @param  string  $assessmentId
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($assessmentId, $id)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $checklistItem = ChecklistItem::where('assessment_id', $assessmentId)
            ->where('checklist_item_id', $id)
            ->firstOrFail();
            
        return view('checklist_items.show', compact('assessment', 'checklistItem'));
    }

    /**
     * Show the form for editing the specified checklist item.
     *
     * @param  string  $assessmentId
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($assessmentId, $id)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $checklistItem = ChecklistItem::where('assessment_id', $assessmentId)
            ->where('checklist_item_id', $id)
            ->firstOrFail();
            
        return view('checklist_items.edit', compact('assessment', 'checklistItem'));
    }

    /**
     * Update the specified checklist item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $assessmentId
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $assessmentId, $id)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'passed' => 'required|boolean',
            'text_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('checklist_items.edit', [$assessmentId, $id])
                ->withErrors($validator)
                ->withInput();
        }

        $checklistItem = ChecklistItem::where('assessment_id', $assessmentId)
            ->where('checklist_item_id', $id)
            ->firstOrFail();
            
        $checklistItem->item_name = $request->item_name;
        $checklistItem->category = $request->category;
        $checklistItem->passed = $request->passed;
        $checklistItem->text_notes = $request->text_notes;
        $checklistItem->save();

        return redirect()->route('checklist_items.index', $assessmentId)
            ->with('success', 'Checklist item updated successfully');
    }

}