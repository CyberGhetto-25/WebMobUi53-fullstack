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
        data-is-authenticated='@json($is_authenticated)'
        data-login-url="{{ $login_url }}"
    ></div>
</x-vue-app-layout>
