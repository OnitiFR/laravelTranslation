<?php

namespace Oniti\Translation\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use Translation;
use Request;

class TranslationMiddleWare
{
    public function handle($request, Closure $next, $guard = null)
    {
        $user = Auth::user();

        //On récupère la local passé en paramètre
        $local = $request->get(config('translation.param_url'));
        // Si aucune information on prend la première langue du navigateur
        if(!$local) $local = explode(',', Request::server('HTTP_ACCEPT_LANGUAGE'))[0];

        Translation::setLocal($local);
        
        return $next($request);
    }
}
