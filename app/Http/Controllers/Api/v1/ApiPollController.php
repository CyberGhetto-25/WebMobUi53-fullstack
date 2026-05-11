<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use Illuminate\Http\Request;

class ApiPollController extends Controller
{
    public function index(Request $request)
    {
        $polls = $request->user()->polls()->orderBy('created_at', 'desc')->get();

        return $polls;
    }

    public function show(string $token)
    {
        $poll = Poll::with(['options' => function ($query) {
            $query->withCount('votes');
        }])->where('secret_token', $token)->first();

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        return $poll;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question'              => 'required|string',
            'title'                 => 'nullable|string',
            'is_draft'              => 'boolean',
            'allow_multiple_choices' => 'boolean',
            'allow_vote_change'     => 'boolean',
            'results_public'        => 'boolean',
            'duration'              => 'nullable|integer|min:1',
        ]);

        $poll = new Poll();
        $poll->user_id               = $request->user()->id;
        $poll->question              = $data['question'];
        $poll->title                 = $data['title'] ?? null;
        $poll->is_draft              = $data['is_draft'] ?? true;
        $poll->allow_multiple_choices = $data['allow_multiple_choices'] ?? false;
        $poll->allow_vote_change     = $data['allow_vote_change'] ?? false;
        $poll->results_public        = $data['results_public'] ?? false;
        $poll->secret_token          = bin2hex(random_bytes(16));

        if (!$poll->is_draft) {
            $poll->started_at = now();
            if (!empty($data['duration'])) {
                $poll->ends_at = now()->addMinutes($data['duration']);
            }
        }

        $poll->save();

        return response()->json($poll, 201);
    }

    public function update(Request $request, int $id)
    {
        $poll = Poll::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        $data = $request->validate([
            'question'              => 'sometimes|required|string',
            'title'                 => 'nullable|string',
            'is_draft'              => 'boolean',
            'allow_multiple_choices' => 'boolean',
            'allow_vote_change'     => 'boolean',
            'results_public'        => 'boolean',
            'duration'              => 'nullable|integer|min:1',
        ]);

        if (array_key_exists('question', $data))              $poll->question              = $data['question'];
        if (array_key_exists('title', $data))                 $poll->title                 = $data['title'];
        if (array_key_exists('allow_multiple_choices', $data)) $poll->allow_multiple_choices = $data['allow_multiple_choices'];
        if (array_key_exists('allow_vote_change', $data))     $poll->allow_vote_change     = $data['allow_vote_change'];
        if (array_key_exists('results_public', $data))        $poll->results_public        = $data['results_public'];

        if (array_key_exists('is_draft', $data)) {
            $poll->is_draft = $data['is_draft'];
            if (!$poll->is_draft && $poll->started_at === null) {
                $poll->started_at = now();
            }
        }

        if (array_key_exists('duration', $data) && $data['duration'] !== null && $poll->started_at !== null) {
            $poll->ends_at = $poll->started_at->copy()->addMinutes($data['duration']);
        }

        $poll->save();

        return response()->json($poll);
    }

    public function remove(Request $request, int $id)
    {
        $poll = Poll::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$poll) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        $poll->delete();

        return response()->json(['message' => 'success'], 200);
    }
}
