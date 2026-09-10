<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(string $lang)
    {
        abort_unless(
            in_array(
                $lang,
                config('app.supported_locales', ['fa']),
                true
            ),
            404
        );

        Session::put('locale', $lang);
        App::setLocale($lang);

        return Redirect::back();
    }
}
