<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Location;
use App\Models\WorkLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogbookController extends Controller
{
    public function index(Request $request): View
    {
        $logs = WorkLog::query()->with(['team.department', 'location', 'asset', 'author'])->latest('started_at');
        $logs->when($request->filled('q'), function ($query) use ($request) {
            $term = '%'.trim($request->string('q')->toString()).'%';
            $query->where(fn ($inner) => $inner->where('number', 'ilike', $term)->orWhere('title', 'ilike', $term)->orWhere('description', 'ilike', $term));
        });
        $logs->when($request->filled('location'), fn ($q) => $q->where('location_id', $request->integer('location')));
        $logs->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        $logs->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->string('priority')));

        return view('logbook.index', ['logs' => $logs->paginate(20)->withQueryString(), 'locations' => Location::where('active', true)->orderBy('name')->get(), 'metrics' => ['today' => WorkLog::whereDate('started_at', today())->count(), 'open' => WorkLog::whereIn('status', ['planned', 'in_progress', 'requires_follow_up'])->count(), 'critical' => WorkLog::where('priority', 'critical')->where('status', '!=', 'completed')->count(), 'offline' => Asset::where('status', 'offline')->count()]]);
    }

    public function show(WorkLog $workLog): View
    {
        return view('logbook.show', ['workLog' => $workLog->load(['team.department', 'location', 'asset', 'author','inputValues'])]);
    }
}
