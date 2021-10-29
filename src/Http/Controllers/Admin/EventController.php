<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Pilot\Tag;
use Illuminate\Support\Facades\Auth;
use Flex360\Pilot\Pilot\MediaHandler;
use Illuminate\Support\Facades\Validator;
use Flex360\Pilot\Pilot\Event as PilotEvent;
use Flex360\Pilot\Facades\Event as EventFacade;

class EventController extends AdminController
{
    public static $namespace = '\Flex360\Pilot\Pilot\Event\\';
    public static $model = 'Event';
    public static $viewFolder = 'events';

    public function __construct(MediaHandler $mediaHandler)
    {
        $this->fileHandler = $mediaHandler->get();

        // To dynamically set notification of how many drafts there are.
        $draftsCount = EventFacade::getDraftCount();
        view()->share('draftsCount', $draftsCount);

        $tags = Tag::orderBy('name', 'asc')->get();
        view()->share('tags', $tags);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $events = EventFacade::whereNot('draft')
            ->whereNot('scheduled')
            ->pilotIndex()
            ->orderByStartDate()
            ->paginate(20);

        $view = 'published';

        return view('pilot::admin.events.index', compact('events', 'view'));
    }

    public function indexOfScheduled()
    {
        $events = EventFacade::scheduled()
            ->pilotIndex()
            ->orderByStartDate()
            ->paginate(20);

        $view = 'scheduled';

        return view('pilot::admin.events.index', compact('events', 'view'));
    }

    public function indexOfDrafts()
    {
        $events = EventFacade::draft()
            ->pilotIndex()
            ->orderByStartDate()
            ->paginate(20);

        $view = 'drafts';

        return view('pilot::admin.events.index', compact('events', 'view'));
    }

    public function indexOfAll()
    {
        $events = EventFacade::pilotIndex()
            ->orderByStartDate()
            ->paginate(20);

        $view = 'all';

        return view('pilot::admin.events.index', compact('events', 'view'));
    }

    public function indexOfPast()
    {
        $events = EventFacade::past()
            ->pilotIndex()
            ->orderByStartDate()
            ->paginate(20);

        $view = 'past';

        return view('pilot::admin.events.index', compact('events', 'view'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $item = new PilotEvent;

        // set default start and end date
        $item->start = \Carbon\Carbon::parse(date('n/j/Y g:i a'));
        $item->end = \Carbon\Carbon::parse(date('n/j/Y g:i a'));

        // set default publish on date
        $item->published_at = \Carbon\Carbon::parse(date('n/j/Y g:i a'));

        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');

        $formOptions = ['route' => 'admin.event.store'];

        return view('pilot::admin.events.form', compact('item', 'tags', 'formOptions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $request = request();

        //make sure the end date is in the future or equal to the start date,
        //but don't allow for an end date to be less than the start date.
        $validator = Validator::make($request->all(), [
            'end' => 'required|date|after_or_equal:start'
        ]);

        if ($validator->fails()) {
            // set error message
            session()->flash('alert-danger', 'You can not make an event that has an end date before the start date!');

            return redirect()->back()->withInput();
        }

        $item = EventFacade::create($request->except('tags', 'image', 'gallery'));

        // deal with event tags
        if ($request->has('tags')) {
            $tags = $request->input('tags');
            $item->addTags($tags);
        }

        $data = $request->only('image', 'gallery');

        // call media manager file handler
        call_user_func_array($this->fileHandler, [&$item, &$data, 'image']);
        call_user_func_array($this->fileHandler, [&$item, &$data, 'gallery', false]);

        // set success message
        session()->flash('alert-success', 'Event saved successfully!');

        return redirect()->route('admin.event.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $item = EventFacade::withoutGlobalScopes()->belongsToSite()->find($id);

        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');

        $formOptions = [
            'route' => ['admin.event.update', $id],
            'method' => 'put',
            'files' => true,
        ];

        return view('pilot::admin.events.form', compact('item', 'tags', 'formOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $request = request();

        //make sure the end date is in the future or equal to the start date,
        //but don't allow for an end date to be less than the start date.
        $validator = Validator::make($request->all(), [
            'end' => 'required|date|after_or_equal:start'
        ]);

        if ($validator->fails()) {
            // set error message
            session()->flash('alert-danger', 'You can not make an event that has an end date before the start date!');

            return redirect()->back()->withInput();
        }

        $item = EventFacade::withoutGlobalScopes()->belongsToSite()->find($id);

        $data = $request->except('image', 'gallery', 'tags');

        // deal with post tags
        if ($request->has('tags')) {
            $tags = $request->input('tags');
            $item->addTags($tags);
        }

        $item->fill($data);

        $item->save();

        // set success message
        session()->flash('alert-success', 'Event saved successfully!');

        return redirect()->route('admin.event.edit', [$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // make sure the current user is an admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('admin.auth.denied');
        }

        $event = EventFacade::withoutGlobalScopes()->belongsToSite()->find($id);

        $event->tags()->detach();

        $event->delete();

        // set success message
        session()->flash('alert-success', 'Event deleted successfully!');

        return redirect()->route('admin.event.index');
    }

    public function copy($id)
    {
        $event = EventFacade::withoutGlobalScopes()->belongsToSite()->find($id);

        $newEvent = $event->duplicate();

        // set success message
        session()->flash('alert-success', 'Event copied successfully!');

        return redirect()->route('admin.event.edit', [$newEvent->id]);
    }
}
