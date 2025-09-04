<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\NetworkLeader;
use App\Models\G12Leader;
use App\Models\SeniorPastor;

class RestrictToLeaders
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user->is_admin) {
            return $next($request);
        }
        $isNetworkLeader = NetworkLeader::where('user_id', $user->id)->exists();
        $isG12Leader = G12Leader::where('user_id', $user->id)->exists();
        $isSeniorPastor = SeniorPastor::where('user_id', $user->id)->exists();

        if (!$isNetworkLeader && !$isG12Leader && !$isSeniorPastor) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
