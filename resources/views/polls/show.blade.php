<x-vue-app-layout>
    <x-slot:scripts>
        @vite(['resources/js/poll-vote.js'])
    </x-slot>

    <x-slot:title>
        {{ $poll->title ?? $poll->question }}
    </x-slot>

    <div
        id="poll-vote-app"
        data-poll='@json($poll)'
    ></div>
</x-vue-app-layout>
