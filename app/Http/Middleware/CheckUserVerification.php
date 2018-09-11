<?php

namespace App\Http\Middleware;

use Closure;

use App\Helpers\Helper;

class CheckUserVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\Auth::check()) {

            $data = \Auth::user();

            if($data->status == USER_DECLINED) {

                \Auth::logout();
                    
                return back()->with('flash_error', Helper::get_error_message(502));

            }

            if(!\Auth::user()->is_verified) {

                \Auth::logout();

                // Check the verification code expiry

                Helper::check_email_verification("" , $data, $error , USER);

                \Log::info("Middleware USER");

                return back()->with('flash_error', tr('email_verify_alert'));

            }
        }

        return $next($request);
    }
}
