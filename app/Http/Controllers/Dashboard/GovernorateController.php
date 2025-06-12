<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GovernorateController extends Controller
{
    public function index()
    {
        $governorates = Governorate::paginate(15);
        return view('dashboard.governorates.index', compact('governorates'));
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('dashboard.governorates.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:governorates,name'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'brief' => ['required', 'string'],
            'description' => ['required', 'string'],
            'visit_count' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('governorate_images', 'public');
        }

        Governorate::create($validated);
        return redirect()->route('governorates.index')->with('success', 'Governorate created successfully.');
    }

    public function edit(Governorate $governorate)
    {
        return view('dashboard.governorates.edit', compact('governorate'));
    }

    public function update(Request $request, Governorate $governorate)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:governorates,name,' . $governorate->id],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'brief' => ['required', 'string'],
            'description' => ['required', 'string'],
            'visit_count' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image')) {
            if ($governorate->image) {
                Storage::disk('public')->delete($governorate->image);
            }
            $validated['image'] = $request->file('image')->store('governorate_images', 'public');
        }

        $governorate->update($validated);
        return redirect()->route('governorates.index')->with('success', 'Governorate updated successfully.');
    }

    public function destroy(Governorate $governorate)
    {
        if ($governorate->image) {
            Storage::disk('public')->delete($governorate->image);
        }
        $governorate->delete();
        return redirect()->route('governorates.index')->with('success', 'Governorate deleted successfully.');
    }
}

?>
