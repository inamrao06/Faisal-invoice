<?php
namespace App\Http\Controllers;

use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $templates  = NotificationTemplate::orderBy('event_key')->get()->keyBy('event_key');
        $eventList  = NotificationTemplate::eventList();
        $channels   = NotificationTemplate::channels();

        // Group events by category
        $grouped = collect($eventList)->groupBy(fn($e) => $e['category']);

        return view('settings.notifications.index', compact('templates', 'eventList', 'grouped', 'channels'));
    }

    public function create(Request $request)
    {
        $eventList = NotificationTemplate::eventList();
        $channels  = NotificationTemplate::channels();
        $variables = NotificationTemplate::variables();

        // Pre-select event if passed via query string
        $selectedEvent = $request->query('event');
        $template = $selectedEvent && isset($eventList[$selectedEvent])
            ? new NotificationTemplate(['event_key' => $selectedEvent])
            : new NotificationTemplate();

        return view('settings.notifications.form', compact(
            'template', 'eventList', 'channels', 'variables', 'selectedEvent'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_key' => ['required', Rule::in(array_keys(NotificationTemplate::eventList())),
                            Rule::unique('notification_templates', 'event_key')],
            'title'     => ['required', 'max:180'],
            'body'      => ['required'],
            'channel'   => ['required', Rule::in(array_keys(NotificationTemplate::channels()))],
            'icon'      => ['nullable', 'max:80'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        NotificationTemplate::create($data);

        return redirect()->route('settings.notifications.index')
                         ->with('success', 'Notification template created.');
    }

    public function edit(NotificationTemplate $notification)
    {
        $eventList = NotificationTemplate::eventList();
        $channels  = NotificationTemplate::channels();
        $variables = NotificationTemplate::variables();
        $template  = $notification;

        return view('settings.notifications.form', compact(
            'template', 'eventList', 'channels', 'variables'
        ));
    }

    public function update(Request $request, NotificationTemplate $notification)
    {
        $data = $request->validate([
            'title'    => ['required', 'max:180'],
            'body'     => ['required'],
            'channel'  => ['required', Rule::in(array_keys(NotificationTemplate::channels()))],
            'icon'     => ['nullable', 'max:80'],
            'is_active'=> ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $notification->update($data);

        return redirect()->route('settings.notifications.index')
                         ->with('success', 'Notification template updated.');
    }

    public function toggle(NotificationTemplate $notification)
    {
        $notification->update(['is_active' => ! $notification->is_active]);
        return back()->with('success', $notification->is_active ? 'Template enabled.' : 'Template disabled.');
    }

    public function destroy(NotificationTemplate $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification template deleted.');
    }
}
