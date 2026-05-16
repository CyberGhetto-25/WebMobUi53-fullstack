<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiPollVoteController extends Controller
{
    public function store(Request $request, string $token)
    {
        $poll = Poll::with(['options' => function ($query) {
            $query->withCount('votes');
        }])->where('secret_token', $token)->first();

        if (!$poll || $poll->is_draft) {
            return response()->json(['message' => 'Poll not found.'], 404);
        }

        if ($poll->ends_at !== null && $poll->ends_at->isPast()) {
            return response()->json(['message' => 'This poll has expired.'], 403);
        }

        $optionIds = $poll->options->pluck('id')->toArray();

        $data = $request->validate([
            'option_ids'   => $poll->allow_multiple_choices
                ? ['required', 'array', 'min:1']
                : ['required', 'array', 'size:1'],
            'option_ids.*' => ['integer', Rule::in($optionIds)],
        ]);

        $existingVotes = PollVote::where('poll_id', $poll->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($existingVotes) {
            if (!$poll->allow_vote_change) {
                return response()->json(['message' => 'You have already voted on this poll.'], 409);
            }
            PollVote::where('poll_id', $poll->id)
                ->where('user_id', $request->user()->id)
                ->delete();
        }

        foreach ($data['option_ids'] as $optionId) {
            PollVote::create([
                'poll_id'        => $poll->id,
                'user_id'        => $request->user()->id,
                'poll_option_id' => $optionId,
            ]);
        }

        $poll->load(['options' => function ($query) {
            $query->withCount('votes');
        }]);

        return response()->json($poll, 201);
    }
}
