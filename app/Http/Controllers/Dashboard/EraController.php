<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Era;
use Illuminate\Http\Request;

class EraController extends Controller
{
    public function index()
    {
        $eras = Era::paginate(15);
        return view('dashboard.eras.index', compact('eras'));
    }

    public function create()
    {
        return view('dashboard.eras.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:eras,name'],
        ]);

        Era::create($validated);
        return redirect()->route('eras.index')->with('success', 'Era created successfully.');
    }

    public function edit(Era $era)
    {
        return view('dashboard.eras.edit', compact('era'));
    }

    public function update(Request $request, Era $era)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:eras,name,' . $era->id],
        ]);

        $era->update($validated);
        return redirect()->route('eras.index')->with('success', 'Era updated successfully.');
    }

    public function destroy(Era $era)
    {
        $era->delete();
        return redirect()->route('eras.index')->with('success', 'Era deleted successfully.');
    }
}
?>
