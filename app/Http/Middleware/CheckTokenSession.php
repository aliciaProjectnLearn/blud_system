<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckTokenSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->route('token');

        // REVISION 11: 60-Minute Session Check
        $tokenVerifiedAt = session('token_verified_at');
        $isVerified = session('token_verified') === true && 
                     session('booking_token') === $token &&
                     $tokenVerifiedAt && 
                     Carbon::parse($tokenVerifiedAt)->addMinutes(60)->isFuture();

        if (!$isVerified) {
            // REVISION 12: Redirect to OTP if session invalid/expired
            // Note: The auto-send OTP logic is handled in the entry point controller 
            // or the OTP form controller as per requirement.
            return redirect()->route('user.kantin.sewa.otp', ['token' => $token])
                ->with('info', 'Sesi Anda telah berakhir atau belum terverifikasi. Silakan masukkan kode OTP baru.');
        }

        return $next($request);
    }
}
