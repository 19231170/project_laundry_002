<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPosSession
{
    /**
     * POS session timeout in minutes.
     */
    protected int $timeout = 10;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if POS session exists
        if (! session()->has('pos_user_id')) {
            return redirect()->route('pos.login')->with('error', 'Silakan login dengan PIN terlebih dahulu.');
        }

        // Check session timeout (10 minutes)
        $lastActivity = session('pos_last_activity');
        $timeoutSeconds = $this->timeout * 60;

        if ($lastActivity && (time() - $lastActivity) > $timeoutSeconds) {
            // Session expired, clear POS session
            session()->forget(['pos_user_id', 'pos_user_name', 'pos_last_activity']);

            return redirect()->route('pos.login')->with('error', 'Sesi POS telah berakhir. Silakan login kembali.');
        }

        // Update last activity timestamp
        session(['pos_last_activity' => time()]);

        return $next($request);
    }
}
