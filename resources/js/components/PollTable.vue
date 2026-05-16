<script setup>
  import { ref } from 'vue';
  import { usePollStore } from '@/stores/usePollStore';

  const emit = defineEmits(['edit']);

  const { polls, deletePoll } = usePollStore();

  const copiedId = ref(null);

  function getStatus(poll) {
    if (poll.is_draft) return { label: 'Brouillon', class: 'text-gray-500' };
    if (poll.ends_at && new Date(poll.ends_at) < new Date()) return { label: 'Expiré', class: 'text-red-500' };
    return { label: 'Actif', class: 'text-green-500' };
  }

  async function delPoll(id) {
    console.log('delete Poll ID:', id);
    await deletePoll(id);
  }

  async function copyLink(poll) {
    await navigator.clipboard.writeText(window.location.origin + '/polls/' + poll.secret_token);
    copiedId.value = poll.id;
    setTimeout(() => { copiedId.value = null; }, 2000);
  }
</script>

<template>
  <p v-if="polls.length === 0">Aucun sondage.</p>

  <table v-else class="w-full border-collapse text-left">
    <thead>
      <tr>
        <th class="border px-3 py-2">Actions</th>
        <th class="border px-3 py-2">ID</th>
        <th class="border px-3 py-2">Titre</th>
        <th class="border px-3 py-2">Question</th>
        <th class="border px-3 py-2">Statut</th>
        <th class="border px-3 py-2">Debut</th>
        <th class="border px-3 py-2">Fin</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="poll in polls" :key="poll.id">
        <td class="border px-3 py-2">
          <button class="btn-edit" @click="emit('edit', poll)">Éditer</button>
          <button class="btn-link" @click="copyLink(poll)">
            {{ copiedId === poll.id ? '✓ Copié !' : '🔗 Lien' }}
          </button>
          <button class="btn-delete" @click="delPoll(poll.id)">Supp.</button>
        </td>
        <td class="border px-3 py-2">{{ poll.id }}</td>
        <td class="border px-3 py-2">{{ poll.title || '-' }}</td>
        <td class="border px-3 py-2">{{ poll.question }}</td>
        <td class="border px-3 py-2" :class="getStatus(poll).class">{{ getStatus(poll).label }}</td>
        <td class="border px-3 py-2">{{ poll.started_at || '-' }}</td>
        <td class="border px-3 py-2">{{ poll.ends_at || '-' }}</td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
  button {
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    margin-right: 0.25rem;
  }
  button:last-child {
    margin-right: 0;
  }
  .btn-edit {
    background-color: #3490dc;
    color: white;
  }
  .btn-link {
    background-color: #6b7280;
    color: white;
    min-width: 5.5rem;
  }
  .btn-delete {
    background-color: #e3342f;
    color: white;
  }
</style>
