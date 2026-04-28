<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, ['en', 'pl'], true), 404);

        $request->session()->put('locale', $locale);
        app()->setLocale($locale);

        return redirect()->back(fallback: route('home'));
    }
}
