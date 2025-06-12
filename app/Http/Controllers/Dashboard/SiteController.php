<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ArticleImage;
use App\Models\Era;
use App\Models\Governorate;
use App\Models\Site;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $sites = Site::with('era', 'governorate')->paginate(15);   //* return collection object not only array
        return View('dashboard.sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): Factory|View|Application
    {
        $eras = Era::all();
        $governorates = Governorate::all();
        $sites = Site::with('era')->get();

//        return $sites;
        return View('dashboard.sites.create', compact('sites', 'eras', 'governorates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate(Article::rules());

        $data = $request->except('images');
        $site = Site::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('articles', 'public');
                ArticleImage::create([
                    'article_id' => $site->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('site.index')->with('success', 'Article created successfully');


    }

    /**
     * Display the specified resource.
     *
     * @param Site $site
     * @return void
     */
    public function show(Site $site)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): Factory|View|Application
    {
        $eras = Era::all();
        $site = Site::with('era', 'images')->findOrFail($id);
        $governorates = Governorate::all();
        return View('dashboard.sites.edit', compact('eras', 'site' , 'governorates'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate(Site::rules());

        $site = Site::findOrFail($id);
        $data = $request->except('images');
        $site->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('articles', 'public');
                ArticleImage::create([
                    'site_id' => $site->id,
                    'image_path' => $path,
                ]);
            }
        }
        return redirect()
            ->route('sites.index')
            ->with('updated', 'The record has been updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {

        $site = Site::findOrFail($id);
        foreach ($site->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $site->delete();
        return redirect()
            ->route('sites.index')
            ->with('deleted', 'Record has been deleted');
    }
    public function deleteImage($imageId)
    {
        $image = ArticleImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Image deleted successfully');
    }
}
