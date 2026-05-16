<x-default-layout>
  <div class="max-w-2xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
      Sondages en cours
    </h1>

    @if($activePolls->isEmpty())
      <p class="text-gray-500 dark:text-gray-400">
        Aucun sondage actif pour le moment.
      </p>
    @else
      <div class="flex flex-col gap-3">
        @foreach($activePolls as $poll)
          <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4
                      bg-white dark:bg-gray-800 shadow-sm">
            <div class="flex items-start justify-between gap-2">
              <div class="flex-1 min-w-0">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white
                           truncate">
                  {{ $poll->title ?? $poll->question }}
                </h2>
                @if($poll->title)
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                    {{ $poll->question }}
                  </p>
                @endif
              </div>
              <span class="text-xs font-medium text-green-600 dark:text-green-400
                           bg-green-50 dark:bg-green-900/30 px-2 py-0.5 rounded-full
                           whitespace-nowrap flex-shrink-0">
                Actif
              </span>
            </div>

            <div class="flex items-center gap-4 mt-3 text-sm
                        text-gray-500 dark:text-gray-400">
              <span>{{ $poll->votes_count }} vote(s)</span>
              @if($poll->ends_at)
                <span class="text-amber-600 dark:text-amber-400">
                  Fin le {{ $poll->ends_at->format('d.m.Y') }}
                </span>
              @endif
            </div>

            <a href="/polls/{{ $poll->secret_token }}"
               class="mt-3 w-full inline-flex justify-center items-center
                      px-4 py-2 rounded-lg text-sm font-medium text-white
                      bg-teal-700 dark:bg-purple-900
                      hover:bg-teal-800 dark:hover:bg-purple-800
                      transition-colors">
              Participer
            </a>
          </div>
        @endforeach
      </div>
    @endif

    @if($expiredPolls->isNotEmpty())
      <h2 class="text-xl font-bold mt-10 mb-4 text-gray-900 dark:text-white">
        Sondages terminés
      </h2>
      <div class="flex flex-col gap-3 opacity-75">
        @foreach($expiredPolls as $poll)
          <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4
                      bg-white dark:bg-gray-800 shadow-sm">
            <div class="flex items-start justify-between gap-2">
              <div class="flex-1 min-w-0">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white
                           truncate">
                  {{ $poll->title ?? $poll->question }}
                </h2>
                @if($poll->title)
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                    {{ $poll->question }}
                  </p>
                @endif
              </div>
              <span class="text-xs font-medium text-red-500
                           bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full
                           whitespace-nowrap flex-shrink-0">
                Terminé
              </span>
            </div>

            <div class="flex items-center gap-4 mt-3 text-sm
                        text-gray-500 dark:text-gray-400">
              <span>{{ $poll->votes_count }} vote(s)</span>
              <span>Terminé le {{ $poll->ends_at->format('d.m.Y') }}</span>
            </div>

            <a href="/polls/{{ $poll->secret_token }}"
               class="mt-3 w-full inline-flex justify-center items-center
                      px-4 py-2 rounded-lg text-sm font-medium text-white
                      bg-gray-500 dark:bg-gray-600
                      hover:bg-gray-600 dark:hover:bg-gray-500
                      transition-colors">
              Voir les résultats
            </a>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</x-default-layout>
