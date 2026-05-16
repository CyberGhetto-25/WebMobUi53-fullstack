<?php

namespace App\Http\Controllers;

use App\Models\Poll;

class PublicPollController extends Controller
{
    public function index()
    {
        $polls = Poll::where('is_draft', false)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->withCount('votes')
            ->orderBy('started_at', 'desc')
            ->get();

        return view('polls.index', compact('polls'));
    }
}
