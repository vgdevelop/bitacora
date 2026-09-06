<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        return view('locations.index', ['locations' => Location::withCount(['assets', 'workLogs'])->with(['assets' => fn ($q) => $q->orderBy('name')])->orderBy('name')->get()]);
    }

    public function show(Location $location): View
    {
        return view('locations.show', ['location' => $location->load(['assets' => fn ($q) => $q->orderBy('name'), 'workLogs' => fn ($q) => $q->with(['team', 'asset', 'author'])->latest('started_at')->limit(20)])]);
    }
}
