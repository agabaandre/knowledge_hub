<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EventsRepository;

class AdminEventsController extends Controller
{
    protected $eventsRepo;

    public function __construct(EventsRepository $eventsRepo)
    {
        $this->eventsRepo = $eventsRepo;
    }

    public function index()
    {
        $events = $this->eventsRepo->getAll();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'venue' => 'required|string|max:255',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
            'organized_by' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'is_online' => 'required|boolean',
            'contact_person' => 'required|string|max:255',
        ]);

        $this->eventsRepo->create($request->all());

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show($id)
    {
        $event = $this->eventsRepo->find($id);
        return view('admin.events.show', compact('event'));
    }

    public function edit($id)
    {
        $event = $this->eventsRepo->find($id);
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = $this->eventsRepo->find($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'venue' => 'required|string|max:255',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
            'organized_by' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'is_online' => 'required|boolean',
            'contact_person' => 'required|string|max:255',
        ]);

        $this->eventsRepo->update($event, $request->all());

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy($id)
    {
        $event = $this->eventsRepo->find($id);
        $this->eventsRepo->delete($event);

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
