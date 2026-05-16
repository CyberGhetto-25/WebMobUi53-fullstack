<script setup>
  import { ref } from 'vue';
  import { Pencil, Link, Trash2, CheckCheck } from 'lucide-vue-next';
  import { usePollStore } from '@/stores/usePollStore';

  const emit = defineEmits(['edit']);

  const { polls, deletePoll } = usePollStore();

  const copiedId = ref(null);

  function getStatus(poll) {
    if (poll.is_draft) return { label: 'Brouillon', class: 'text-gray-500' };
    if (poll.ends_at && new Date(poll.ends_at) < new Date()) return { label: 'Expiré', class: 'text-red-500' };
    return { label: 'Actif', class: 'text-green-500' };
  }

  function formatDate(dateStr) {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleString('fr-CH', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }

  async function delPoll(id) {
    await deletePoll(id);
  }

  async function copyLink(poll) {
    await navigator.clipboard.writeText(window.location.origin + '/polls/' + poll.secret_token);
    copiedId.value = poll.id;
    setTimeout(() => { copiedId.value = null; }, 2000);
  }
</script>

<template>
  <p v-if="polls.length === 0" class="text-gray-500 dark:text-gray-400">
    Aucun sondage.
  </p>

  <div v-else class="table-wrapper">
    <table class="bg-white dark:bg-gray-800 w-full border-collapse text-left">
    <thead>
      <tr>
        <th class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900">Actions</th>
        <th class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900">ID</th>
        <th class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900">Titre</th>
        <th class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900">Question</th>
        <th class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900">Statut</th>
        <th class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900">Debut</th>
        <th class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900">Fin</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="poll in polls" :key="poll.id">
        <td class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-900 dark:text-white">
          <div style="display: flex; flex-direction: column; gap: 0.35rem;">
            <button class="btn-edit" @click="emit('edit', poll)">
              <Pencil :size="14" /> Éditer
            </button>
            <button class="btn-link" @click="copyLink(poll)">
              <component :is="copiedId === poll.id ? CheckCheck : Link" :size="14" />
              {{ copiedId === poll.id ? 'Copié !' : 'Lien' }}
            </button>
            <button class="btn-delete" @click="delPoll(poll.id)">
              <Trash2 :size="14" /> Supp.
            </button>
          </div>
        </td>
        <td class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-900 dark:text-white">{{ poll.id }}</td>
        <td class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-900 dark:text-white">{{ poll.title || '-' }}</td>
        <td class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-900 dark:text-white">{{ poll.question }}</td>
        <td class="border border-gray-200 dark:border-gray-700 px-3 py-2" :class="getStatus(poll).class">{{ getStatus(poll).label }}</td>
        <td class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-900 dark:text-white">{{ formatDate(poll.started_at) }}</td>
        <td class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-gray-900 dark:text-white">{{ formatDate(poll.ends_at) }}</td>
      </tr>
    </tbody>
    </table>
  </div>
</template>

<style scoped>
  button {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
  }
  .btn-edit {
    background-color: #4a1d96;
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
  @media (max-width: 640px) {
    .table-wrapper {
      overflow-x: auto;
    }
  }
</style>
