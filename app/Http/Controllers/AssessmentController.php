<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\ChecklistItem;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the assessments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assessments = Assessment::with(['employee', 'vehicle'])->latest()->paginate(10);
        return view('assessments.index', compact('assessments'));
    }

    /**
     * Show the form for creating a new assessment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $vehicles = Vehicle::with('employee')->get();
        return view('assessments.create', compact('employees', 'vehicles'));
    }

    /**
     * Store a newly created assessment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,employee_id',
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'assessment_date' => 'required|date',
            'comments' => 'nullable|string',
            'approved' => 'required|boolean',
            'status_name' => 'required|string|max:50',
            'status_description' => 'nullable|string',
            'status_color_code' => 'nullable|string|max:20',
            'checklist_items' => 'required|array|min:1',
            'checklist_items.*.item_name' => 'required|string|max:255',
            'checklist_items.*.category' => 'required|string|max:100',
            'checklist_items.*.passed' => 'required|boolean',
            'checklist_items.*.text_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('assessments.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Create assessment
        $assessment = new Assessment();
        $assessment->assessment_id = (string) Str::uuid();
        $assessment->employee_id = $request->employee_id;
        $assessment->vehicle_id = $request->vehicle_id;
        $assessment->assessment_date = $request->assessment_date;
        $assessment->comments = $request->comments;
        $assessment->approved = $request->approved;
        $assessment->status_name = $request->status_name;
        $assessment->status_description = $request->status_description;
        $assessment->status_color_code = $request->status_color_code;
        $assessment->save();

        // Create checklist items
        foreach ($request->checklist_items as $item) {
            $checklistItem = new ChecklistItem();
            $checklistItem->checklist_item_id = (string) Str::uuid();
            $checklistItem->assessment_id = $assessment->assessment_id;
            $checklistItem->item_name = $item['item_name'];
            $checklistItem->category = $item['category'];
            $checklistItem->passed = $item['passed'];
            $checklistItem->text_notes = $item['text_notes'] ?? null;
            $checklistItem->save();
        }

        // Create notification
        $notification = new Notification();
        $notification->notification_id = (string) Str::uuid();
        $notification->user_id = Auth::id();
        $notification->employee_id = $request->employee_id;
        $notification->type = 'assessment_created';
        $notification->message = 'New assessment has been created for your vehicle';
        $notification->is_read = false;
        $notification->sent_at = now();
        $notification->save();

        return redirect()->route('assessments.index')
            ->with('success', 'Assessment created successfully.');
    }

    /**
     * Display the specified assessment.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $assessment = Assessment::with(['employee', 'vehicle', 'checklistItems'])->findOrFail($id);
        return view('assessments.show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified assessment.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $assessment = Assessment::with('checklistItems')->findOrFail($id);
        $employees = Employee::orderBy('name')->get();
        $vehicles = Vehicle::with('employee')->get();
        return view('assessments.edit', compact('assessment', 'employees', 'vehicles'));
    }

    /**
     * Update the specified assessment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,employee_id',
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'assessment_date' => 'required|date',
            'comments' => 'nullable|string',
            'approved' => 'required|boolean',
            'status_name' => 'required|string|max:50',
            'status_description' => 'nullable|string',
            'status_color_code' => 'nullable|string|max:20',
            'checklist_items' => 'required|array|min:1',
            'checklist_items.*.id' => 'nullable|string', // For existing items
            'checklist_items.*.item_name' => 'required|string|max:255',
            'checklist_items.*.category' => 'required|string|max:100',
            'checklist_items.*.passed' => 'required|boolean',
            'checklist_items.*.text_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('assessments.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        // Update assessment
        $assessment = Assessment::findOrFail($id);
        $assessment->employee_id = $request->employee_id;
        $assessment->vehicle_id = $request->vehicle_id;
        $assessment->assessment_date = $request->assessment_date;
        $assessment->comments = $request->comments;
        $assessment->approved = $request->approved;
        $assessment->status_name = $request->status_name;
        $assessment->status_description = $request->status_description;
        $assessment->status_color_code = $request->status_color_code;
        $assessment->save();

        // Get current checklist items
        $existingItems = $assessment->checklistItems->pluck('checklist_item_id')->toArray();
        $updatedItems = [];

        // Update or create checklist items
        foreach ($request->checklist_items as $item) {
            if (isset($item['id']) && !empty($item['id'])) {
                // Update existing item
                $checklistItem = ChecklistItem::findOrFail($item['id']);
                $checklistItem->item_name = $item['item_name'];
                $checklistItem->category = $item['category'];
                $checklistItem->passed = $item['passed'];
                $checklistItem->text_notes = $item['text_notes'] ?? null;
                $checklistItem->save();
                
                $updatedItems[] = $item['id'];
            } else {
                // Create new item
                $checklistItem = new ChecklistItem();
                $checklistItem->checklist_item_id = (string) Str::uuid();
                $checklistItem->assessment_id = $assessment->assessment_id;
                $checklistItem->item_name = $item['item_name'];
                $checklistItem->category = $item['category'];
                $checklistItem->passed = $item['passed'];
                $checklistItem->text_notes = $item['text_notes'] ?? null;
                $checklistItem->save();
                
                $updatedItems[] = $checklistItem->checklist_item_id;
            }
        }

        // Delete checklist items that were removed
        $itemsToDelete = array_diff($existingItems, $updatedItems);
        if (!empty($itemsToDelete)) {
            ChecklistItem::whereIn('checklist_item_id', $itemsToDelete)->delete();
        }

        // Create notification
        $notification = new Notification();
        $notification->notification_id = (string) Str::uuid();
        $notification->user_id = Auth::id();
        $notification->employee_id = $request->employee_id;
        $notification->type = 'assessment_updated';
        $notification->message = 'An assessment for your vehicle has been updated';
        $notification->is_read = false;
        $notification->sent_at = now();
        $notification->save();

        return redirect()->route('assessments.index')
            ->with('success', 'Assessment updated successfully');
    }

    /**
     * Remove the specified assessment from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assessment = Assessment::findOrFail($id);
        
        // Delete associated checklist items
        ChecklistItem::where('assessment_id', $assessment->assessment_id)->delete();
        
        $assessment->delete();

        return redirect()->route('assessments.index')
            ->with('success', 'Assessment deleted successfully');
    }
}