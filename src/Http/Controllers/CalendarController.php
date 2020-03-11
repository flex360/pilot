<?php

namespace Flex360\Pilot\Http\Controllers;

class CalendarController extends Controller
{

    public function index()
    {
        $events = \MyEvent::whereRaw('events.end >= NOW()')
                ->orderBy('events.start', 'asc')
                ->limit(15)
                ->get();

        \Page::mimic([
            'title' => 'Upcoming Events'
        ]);

        return \View::make('frontend.calendar.index', compact('events'));
    }

    public function month()
    {
        return \View::make('frontend.calendar.month');
    }

    public function event($id, $slug)
    {
        if (\Auth::check()) {
            $event = \MyEvent::withoutGlobalScopes()->findOrFail($id);
        } else {
            $event = \MyEvent::findOrFail($id);
        }

        \Page::mimic([
            'title' => $event->title
        ]);

        return \View::make('frontend.calendar.event', compact('event'));
    }

    public function tagged($id, $tagged)
    {
        $events = \MyEvent::join('event_tag', 'posts.id', '=', 'event_tag.event_id')
                ->where('event_tag.tag_id', '=', $id)
                ->whereRaw('events.end >= NOW()')
                ->orderBy('events.start', 'asc')
                ->simplePaginate(10);

        \Page::mimic([
            'title' => 'Events tagged '.$tagged
        ]);

        return \View::make('frontend.blog.index', compact('events'));
    }

    public function json()
    {
        $start = \Input::get('start') . ' 00:00:00';

        $end = \Input::get('end') . ' 23:59:59';

        $events = \MyEvent::whereBetween('start', array($start, $end))
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
