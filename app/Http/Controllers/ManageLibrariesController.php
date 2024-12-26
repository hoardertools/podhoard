<?php

namespace App\Http\Controllers;

use App\Jobs\AddLibraryDirectoryJob;
use App\Jobs\RemoveLibraryDirectoryJob;
use App\Jobs\RescanLibraryJob;
use App\Library;
use App\Podcast;
use GoncziAkos\Podcast\Feed;
use GoncziAkos\Podcast\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManageLibrariesController extends Controller
{
    public function index()
    {
        return view('pages.manage.libraries.index')->with(
            [
                'libraries' => Library::all(),
            ]
        );
    }

    public function create()
    {
        return view('pages.manage.libraries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $slug = Str::slug($request->name);

        if(Library::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->name) . '-' . Str::random(5);
        }

        $library = new Library();
        $library->name = $request->name;
        $library->slug = $slug;
        $library->save();

        return redirect("/manage/libraries/" . $slug)->with('success', 'Library created successfully.');

    }

    public function show($slug)
    {
        $library = Library::where('slug', $slug)->first();

        if(!$library) {
            return redirect('/manage/libraries')->with('error', 'Library not found.');
        }

        return view('pages.manage.libraries.show')->with(
            [
                'library' => $library,
            ]
        );
    }

    public function updateName(Request $request, $slug)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $library = Library::where('slug', $slug)->first();

        if(!$library) {
            return redirect('/manage/libraries')->with('error', 'Library not found.');
        }

        $slug = Str::slug($request->name);

        if(Library::where('slug', $slug)->where("id", "!=", $library->id)->exists()) {
            $slug = Str::slug($request->name) . '-' . Str::random(5);
        }

        $library->name = $request->name;
        $library->slug = $slug;
        $library->save();

        return redirect("/manage/libraries/" . $slug)->with('status', 'Library name updated successfully.');
    }

    public function addDirectory(Request $request, $slug)
    {
        $request->validate([
            'browsefolder' => 'required'
        ]);

        $library = Library::where('slug', $slug)->first();

        if(!$library) {
            return redirect('/manage/libraries')->with('error', 'Library not found.');
        }

        $library->directories()->create([
            'path' => $request->browsefolder
        ]);

        $directory = $library->directories()->where('path', $request->browsefolder)->first();

        AddLibraryDirectoryJob::dispatch($library, $directory);

        return redirect("/manage/libraries/" . $slug)->with('status', 'Directory added successfully.');
    }

    public function removeDirectoryPath(Request $request, $slug, $path)
    {
        $library = Library::where('slug', $slug)->first();

        if(!$library) {
            return redirect('/manage/libraries')->with('error', 'Library not found.');
        }

        $directory = $library->directories()->where('path', base64_decode($path))->first();

        if(!$directory) {
            return redirect("/manage/libraries/" . $slug)->with('error', 'Directory not found.');
        }

        RemoveLibraryDirectoryJob::dispatch($directory);

        return redirect("/manage/libraries/" . $slug)->with('status', 'The directory will be removed and the Library re-scan will start shortly.');
    }

    public function rescan($slug)
    {
        $library = Library::where('slug', $slug)->first();

        if(!$library) {
            return redirect('/manage/libraries')->with('error', 'Library not found.');
        }

        RescanLibraryJob::dispatch($library);

        return redirect("/manage/libraries/" . $slug)->with('status', 'The Library re-scan will start shortly.');

    }


}
