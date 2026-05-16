<?php

namespace App\Http\Controllers;

use App\Models\Poll;

class PublicPollController extends Controller
{
    public function index()
    {
        $activePolls = Poll::where('is_draft', false)
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->withCount('votes')
            ->orderBy('started_at', 'desc')
            ->get();

        $expiredPolls = Poll::where('is_draft', false)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->withCount('votes')
            ->orderBy('ends_at', 'desc')
            ->get();

        return view('polls.index', compact('activePolls', 'expiredPolls'));
    }
}
