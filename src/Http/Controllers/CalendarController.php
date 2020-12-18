<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Pilot\Event;
use Illuminate\Support\Facades\Auth;
use Flex360\Pilot\Facades\Event as EventFacade;
use Flex360\Pilot\Facades\Page as PageFacade;

class CalendarController extends Controller
{
    public function index()
    {
        $events = EventFacade::whereRaw('events.end >= NOW()')
                ->orderBy('events.start', 'asc')
                ->limit(15)
                ->get();

        PageFacade::mimic([
            'title' => 'Upcoming Events'
        ]);

        return view('pilot::frontend.calendar.index', compact('events'));
    }

    public function month()
    {
        return view('pilot::frontend.calendar.month');
    }

    public function event($id, $slug)
    {
        if (Auth::check()) {
            $event = EventFacade::withoutGlobalScopes()->findOrFail($id);
        } else {
            $event = EventFacade::findOrFail($id);
        }

        PageFacade::mimic([
            'title' => $event->title
        ]);

        return view('pilot::frontend.calendar.event', compact('event'));
    }

    public function tagged($id, $tagged)
    {
        $events = EventFacade::join('event_tag', 'events.id', '=', 'event_tag.event_id')
                ->where('event_tag.tag_id', '=', $id)
                ->whereRaw('events.end >= NOW()')
                ->orderBy('events.start', 'asc')
                ->simplePaginate(10);

        PageFacade::mimic([
            'title' => 'Events tagged ' . $tagged
        ]);

        return view('pilot::frontend.calendar.index', compact('events'));
    }

    public function json()
    {
        $start = request()->input('start') . ' 00:00:00';

        $end = request()->input('end') . ' 23:59:59';

        $events = EventFacade::whereBetween('start', [$start, $end])
                    ->orderBy('start', 'asc')
                    ->limit(100)
                    ->get();

        $events = $events->map(function ($event) {
            $e = new \StdClass();

            $e->title = $event->title;

            $e->start = $event->start->toIso8601String();

            $e->end = $event->end->toIso8601String();

            $e->url = $event->url();

            return $e;
        });

        return $events;
    }
}
