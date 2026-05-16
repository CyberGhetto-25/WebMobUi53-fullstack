<x-default-layout>
  <div class="polls-list">
    <h1>Sondages en cours</h1>

    @if($polls->isEmpty())
      <p>Aucun sondage actif pour le moment.</p>
    @else
      <div class="poll-cards">
        @foreach($polls as $poll)
          <div class="poll-card">
            <h2>{{ $poll->title ?? $poll->question }}</h2>
            <p>{{ $poll->question }}</p>
            <p class="votes-count">{{ $poll->votes_count }} vote(s)</p>
            @if($poll->ends_at)
              <p class="ends-at">Se termine le {{ $poll->ends_at->format('d.m.Y à H:i') }}</p>
            @endif
            <a href="/polls/{{ $poll->secret_token }}" class="btn-vote">
              Participer →
            </a>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <style>
    .polls-list { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
    .polls-list h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; }
    .poll-cards { display: flex; flex-direction: column; gap: 1rem; }
    .poll-card {
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      padding: 1.25rem;
      background: white;
    }
    .poll-card h2 { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; }
    .poll-card p { color: #6b7280; font-size: 0.9rem; margin-bottom: 0.25rem; }
    .votes-count { color: #3490dc; font-weight: 500; }
    .ends-at { color: #b45309; }
    .btn-vote {
      display: inline-block;
      margin-top: 0.75rem;
      padding: 0.4rem 1rem;
      background: #3490dc;
      color: white;
      border-radius: 0.25rem;
      text-decoration: none;
      font-size: 0.9rem;
    }
    .btn-vote:hover { background: #2779bd; }
  </style>
</x-default-layout>
