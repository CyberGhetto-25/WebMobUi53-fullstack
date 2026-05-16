<x-default-layout>
  <div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
      Sondages en cours
    </h1>

    @if($activePolls->isEmpty())
      <p class="text-gray-500 dark:text-gray-400">Aucun sondage actif pour le moment.</p>
    @else
      <div class="flex flex-col gap-4">
        @foreach($activePolls as $poll)
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5
                      bg-white dark:bg-gray-800">
            <h2 class="text-lg font-semibold mb-1 text-gray-900 dark:text-white">
              {{ $poll->title ?? $poll->question }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">
              {{ $poll->question }}
            </p>
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-1">
              {{ $poll->votes_count }} vote(s)
            </p>
            @if($poll->ends_at)
              <p class="text-sm text-amber-600 dark:text-amber-400 mb-1">
                Se termine le {{ $poll->ends_at->format('d.m.Y à H:i') }}
              </p>
            @endif
            <a href="/polls/{{ $poll->secret_token }}"
               class="inline-block mt-3 px-4 py-1.5 rounded-md text-sm text-white
                      bg-teal-700 dark:bg-purple-900
                      hover:bg-teal-800 dark:hover:bg-purple-800">
              Participer →
            </a>
          </div>
        @endforeach
      </div>
    @endif

    @if($expiredPolls->isNotEmpty())
      <h2 class="text-xl font-bold mt-10 mb-4 text-gray-900 dark:text-white">
        Sondages terminés
      </h2>
      <div class="flex flex-col gap-4 opacity-75">
        @foreach($expiredPolls as $poll)
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5
                      bg-white dark:bg-gray-800">
            <span class="text-xs font-medium text-red-500 uppercase tracking-wide">
              Terminé
            </span>
            <h2 class="text-lg font-semibold mb-1 text-gray-900 dark:text-white">
              {{ $poll->title ?? $poll->question }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">
              {{ $poll->question }}
            </p>
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-1">
              {{ $poll->votes_count }} vote(s)
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              Terminé le {{ $poll->ends_at->format('d.m.Y à H:i') }}
            </p>
            <a href="/polls/{{ $poll->secret_token }}"
               class="inline-block mt-3 px-4 py-1.5 rounded-md text-sm text-white
                      bg-gray-500 dark:bg-gray-600
                      hover:bg-gray-600 dark:hover:bg-gray-500">
              Voir les résultats →
            </a>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</x-default-layout>
