<?php

namespace App\Http\Middleware;

use App\Models\Election;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureVoterCanVote
{
    public function handle(Request $request, Closure $next): Response
    {
        $election = $request->route('election');

        if (! $election instanceof Election) {
            $election = Election::where('slug', $election)->firstOrFail();
        }

        $participation = Auth::guard('voter')->user();

        // Token milik pemilihan lain
        if ((int) $participation->election_id !== (int) $election->id) {
            Auth::guard('voter')->logout();

            return redirect()->route('voter.login', $election);
        }

        if ($participation->has_voted) {
            Auth::guard('voter')->logout();

            return redirect()->route('portal', $election)
                ->with('status', 'Hak suara kamu sudah digunakan. Terima kasih telah berpartisipasi.');
        }

        if ($election->phase() !== 'open') {
            Auth::guard('voter')->logout();

            return redirect()->route('portal', $election)
                ->with('status', 'Pemungutan suara sedang tidak dibuka.');
        }

        return $next($request);
    }
}