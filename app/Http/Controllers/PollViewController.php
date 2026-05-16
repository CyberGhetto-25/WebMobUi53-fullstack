<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Support\Facades\Auth;

class PollViewController extends Controller
{
    public function show(string $token)
    {
        $poll = Poll::where('secret_token', $token)
            ->where('is_draft', false)
            ->first();

        if (!$poll) abort(404);

        return view('polls.show', [
            'poll'             => $poll->load('options'),
            'is_authenticated' => Auth::check(),
            'login_url'        => route('login'),
        ]);
    }
}
