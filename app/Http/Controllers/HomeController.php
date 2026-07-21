<?php

namespace App\Http\Controllers;

use App\Services\HomeContentService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(HomeContentService $homeContent): View
    {
        return view('home', $homeContent->forHomepage());
    }
}
