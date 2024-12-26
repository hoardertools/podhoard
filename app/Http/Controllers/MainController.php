<?php
namespace App\Http\Controllers;


use App\Episode;

class MainController extends Controller {
    public function home() {
        return view('pages/home');
    }

    public function permissionDenied() {
        return view('pages/permission-denied');
    }

    public function downloadQueue()
    {
        return view('pages.download-queue')->with([
            'downloadQueue' => Episode::where("downloaded", "=", false)->whereNotNull("download_url")->orderBy("created_at", "ASC")->get()
        ]);
    }
}