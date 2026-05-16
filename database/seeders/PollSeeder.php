<?php

namespace Database\Seeders;

use App\Models\Poll;
use App\Models\PollVote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PollSeeder extends Seeder
{
    public function run(): void
    {
        $john = User::where('email', 'john.doe@example.com')->firstOrFail();
        $jane = User::where('email', 'jane.doe@example.com')->firstOrFail();

        $alice = User::firstOrCreate(
            ['email' => 'alice.martin@example.com'],
            [
                'first_name' => 'Alice',
                'last_name'  => 'Martin',
                'username'   => 'alice_martin',
                'password'   => Hash::make('password'),
            ]
        );

        $bob = User::firstOrCreate(
            ['email' => 'bob.dupont@example.com'],
            [
                'first_name' => 'Bob',
                'last_name'  => 'Dupont',
                'username'   => 'bob_dupont',
                'password'   => Hash::make('password'),
            ]
        );

        // -------------------------------------------------------------------
        // Sondage 1 — Actif, résultats publics, sans date de fin
        // -------------------------------------------------------------------
        $poll1 = Poll::create([
            'user_id'                => $john->id,
            'title'                  => 'Langage préféré',
            'question'               => 'Quel langage de programmation préférez-vous ?',
            'secret_token'           => bin2hex(random_bytes(16)),
            'is_draft'               => false,
            'allow_multiple_choices' => false,
            'results_public'         => true,
            'started_at'             => now()->subDays(7),
            'ends_at'                => null,
        ]);

        $o1_1 = $poll1->options()->create(['label' => 'PHP']);
        $o1_2 = $poll1->options()->create(['label' => 'JavaScript']);
        $o1_3 = $poll1->options()->create(['label' => 'Python']);
        $o1_4 = $poll1->options()->create(['label' => 'TypeScript']);

        PollVote::create(['poll_id' => $poll1->id, 'user_id' => $jane->id,  'poll_option_id' => $o1_1->id]);
        PollVote::create(['poll_id' => $poll1->id, 'user_id' => $alice->id, 'poll_option_id' => $o1_2->id]);
        PollVote::create(['poll_id' => $poll1->id, 'user_id' => $bob->id,   'poll_option_id' => $o1_2->id]);

        // -------------------------------------------------------------------
        // Sondage 2 — Actif avec date de fin future, choix multiple, résultats publics
        // -------------------------------------------------------------------
        $poll2 = Poll::create([
            'user_id'                => $john->id,
            'title'                  => 'Frameworks utilisés',
            'question'               => 'Quels frameworks utilisez-vous au quotidien ?',
            'secret_token'           => bin2hex(random_bytes(16)),
            'is_draft'               => false,
            'allow_multiple_choices' => true,
            'results_public'         => true,
            'started_at'             => now()->subDays(3),
            'ends_at'                => now()->addDays(7),
        ]);

        $o2_1 = $poll2->options()->create(['label' => 'Laravel']);
        $o2_2 = $poll2->options()->create(['label' => 'Vue.js']);
        $o2_3 = $poll2->options()->create(['label' => 'React']);
        $o2_4 = $poll2->options()->create(['label' => 'Symfony']);

        PollVote::create(['poll_id' => $poll2->id, 'user_id' => $jane->id,  'poll_option_id' => $o2_1->id]);
        PollVote::create(['poll_id' => $poll2->id, 'user_id' => $jane->id,  'poll_option_id' => $o2_2->id]);
        PollVote::create(['poll_id' => $poll2->id, 'user_id' => $alice->id, 'poll_option_id' => $o2_3->id]);
        PollVote::create(['poll_id' => $poll2->id, 'user_id' => $bob->id,   'poll_option_id' => $o2_2->id]);
        PollVote::create(['poll_id' => $poll2->id, 'user_id' => $bob->id,   'poll_option_id' => $o2_4->id]);

        // -------------------------------------------------------------------
        // Sondage 3 — Expiré, résultats publics
        // -------------------------------------------------------------------
        $poll3 = Poll::create([
            'user_id'                => $john->id,
            'title'                  => 'Éditeur de code',
            'question'               => 'Quel est votre éditeur de code préféré ?',
            'secret_token'           => bin2hex(random_bytes(16)),
            'is_draft'               => false,
            'allow_multiple_choices' => false,
            'results_public'         => true,
            'started_at'             => now()->subDays(30),
            'ends_at'                => now()->subDays(7),
        ]);

        $o3_1 = $poll3->options()->create(['label' => 'VS Code']);
        $o3_2 = $poll3->options()->create(['label' => 'PhpStorm']);
        $o3_3 = $poll3->options()->create(['label' => 'Vim / Neovim']);
        $o3_4 = $poll3->options()->create(['label' => 'Sublime Text']);

        PollVote::create(['poll_id' => $poll3->id, 'user_id' => $jane->id,  'poll_option_id' => $o3_1->id]);
        PollVote::create(['poll_id' => $poll3->id, 'user_id' => $alice->id, 'poll_option_id' => $o3_1->id]);
        PollVote::create(['poll_id' => $poll3->id, 'user_id' => $bob->id,   'poll_option_id' => $o3_2->id]);

        // -------------------------------------------------------------------
        // Sondage 4 — Brouillon, pas de votes
        // -------------------------------------------------------------------
        $poll4 = Poll::create([
            'user_id'                => $john->id,
            'title'                  => 'Mode de travail',
            'question'               => 'Préférez-vous le travail en équipe ou en solo ?',
            'secret_token'           => bin2hex(random_bytes(16)),
            'is_draft'               => true,
            'allow_multiple_choices' => false,
            'results_public'         => false,
            'started_at'             => null,
            'ends_at'                => null,
        ]);

        $poll4->options()->create(['label' => 'En équipe, toujours']);
        $poll4->options()->create(['label' => 'En solo, je préfère']);
        $poll4->options()->create(['label' => 'Ça dépend du projet']);

        // -------------------------------------------------------------------
        // Sondage 5 — Actif, résultats privés
        // -------------------------------------------------------------------
        $poll5 = Poll::create([
            'user_id'                => $john->id,
            'title'                  => 'Réunions hebdomadaires',
            'question'               => 'Quelle est votre opinion sur les réunions hebdomadaires ?',
            'secret_token'           => bin2hex(random_bytes(16)),
            'is_draft'               => false,
            'allow_multiple_choices' => false,
            'results_public'         => false,
            'started_at'             => now()->subDays(2),
            'ends_at'                => now()->addDays(5),
        ]);

        $o5_1 = $poll5->options()->create(['label' => 'Très utiles']);
        $o5_2 = $poll5->options()->create(['label' => 'Parfois utiles']);
        $o5_3 = $poll5->options()->create(['label' => 'Rarement utiles']);
        $o5_4 = $poll5->options()->create(['label' => 'Inutiles, à supprimer']);

        PollVote::create(['poll_id' => $poll5->id, 'user_id' => $jane->id,  'poll_option_id' => $o5_2->id]);
        PollVote::create(['poll_id' => $poll5->id, 'user_id' => $alice->id, 'poll_option_id' => $o5_3->id]);
        PollVote::create(['poll_id' => $poll5->id, 'user_id' => $bob->id,   'poll_option_id' => $o5_4->id]);
    }
}
