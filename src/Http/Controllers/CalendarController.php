<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\Event;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $events = Event::whereRaw('events.end >= NOW()')
                ->orderBy('events.start', 'asc')
                ->limit(15)
                ->get();

        Page::mimic([
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
            $event = Event::withoutGlobalScopes()->findOrFail($id);
        } else {
            $event = Event::findOrFail($id);
        }

        Page::mimic([
            'title' => $event->title
        ]);

        return view('pilot::frontend.calendar.event', compact('event'));
    }

    public function tagged($id, $tagged)
    {
        $events = Event::join('event_tag', 'events.id', '=', 'event_tag.event_id')
                ->where('event_tag.tag_id', '=', $id)
                ->whereRaw('events.end >= NOW()')
                ->orderBy('events.start', 'asc')
                ->simplePaginate(10);

        Page::mimic([
            'title' => 'Events tagged ' . $tagged
        ]);

        return view('pilot::frontend.calendar.index', compact('events'));
    }

    public function json()
    {
        $start = request()->input('start') . ' 00:00:00';

        $end = request()->input('end') . ' 23:59:59';

        $events = Event::whereBetween('start', [$start, $end])
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
