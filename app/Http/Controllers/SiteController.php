<?php

namespace App\Http\Controllers;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::all();
        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        return view('sites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        Site::create($validated);

        return redirect()->route('sites.index')->with('success', 'Site ajouté avec succès.');
    }
    public function destroy($id)
    {
        $site = Site::findOrFail($id);
        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site supprimé avec succès.');
    }
}

