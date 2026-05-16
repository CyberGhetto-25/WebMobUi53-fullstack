<script setup>
import { ref, computed, onMounted } from 'vue';
import { useFetchApi } from '@/composables/useFetchApi';

const props = defineProps({
  poll: { type: Object, required: true },
});

const { fetchApi } = useFetchApi();

const localPoll = ref({ ...props.poll });
const selectedOptionId = ref(null);
const selectedOptionIds = ref([]);
const loading = ref(false);
const apiError = ref(null);
const voteSuccess = ref(false);
const ready = ref(false);

onMounted(async () => {
  try {
    const fresh = await fetchApi({ url: 'polls/' + props.poll.secret_token });
    localPoll.value = fresh;
  } catch {
    // fallback sur les données Blade initiales
  }
  ready.value = true;
});

const isExpired = computed(() => {
  if (!localPoll.value.ends_at) return false;
  return new Date(localPoll.value.ends_at) < new Date();
});

const hasVoted = computed(() => localPoll.value.user_has_voted || voteSuccess.value);

const canVote = computed(() =>
  localPoll.value.allow_multiple_choices
    ? selectedOptionIds.value.length > 0
    : selectedOptionId.value !== null
);

const showResults = computed(() =>
  localPoll.value.results_public && (isExpired.value || hasVoted.value)
);

const totalVotes = computed(() =>
  (localPoll.value.options ?? []).reduce((sum, o) => sum + (o.votes_count ?? 0), 0)
);

function optionProgress(option) {
  if (totalVotes.value === 0) return 0;
  return Math.round(((option.votes_count ?? 0) / totalVotes.value) * 100);
}

async function handleVote() {
  const ids = localPoll.value.allow_multiple_choices
    ? selectedOptionIds.value
    : [selectedOptionId.value];

  loading.value = true;
  apiError.value = null;

  try {
    const result = await fetchApi({
      url: 'polls/' + localPoll.value.secret_token + '/vote',
      method: 'POST',
      data: { option_ids: ids },
    });
    localPoll.value = result;
    voteSuccess.value = true;
  } catch (err) {
    if (err.status === 403) {
      apiError.value = 'Ce sondage est terminé.';
    } else if (err.status === 409) {
      apiError.value = 'Vous avez déjà voté.';
      localPoll.value = { ...localPoll.value, user_has_voted: true };
    } else if (err.status === 422) {
      apiError.value = err.data?.message ?? 'Sélection invalide.';
    } else {
      apiError.value = 'Une erreur est survenue. Veuillez réessayer.';
    }
  }

  loading.value = false;
}
</script>

<template>
  <div class="poll-vote">
    <p v-if="!ready" class="loading">Chargement…</p>

    <template v-else>
      <h1 class="poll-title">{{ localPoll.title || localPoll.question }}</h1>
      <p v-if="localPoll.title" class="poll-question">{{ localPoll.question }}</p>

      <p v-if="isExpired" class="status-msg status-expired">
        Ce sondage est terminé.
      </p>
      <p v-else-if="hasVoted" class="status-msg status-voted">
        {{ voteSuccess ? 'Merci pour votre vote !' : 'Vous avez déjà voté.' }}
      </p>

      <form v-else @submit.prevent="handleVote" class="vote-form">
        <ul class="options-list">
          <li v-for="option in (localPoll.options ?? [])" :key="option.id" class="option-item">
            <label class="option-label">
              <input
                v-if="localPoll.allow_multiple_choices"
                type="checkbox"
                :value="option.id"
                v-model="selectedOptionIds"
              />
              <input
                v-else
                type="radio"
                name="vote-option"
                :value="option.id"
                v-model="selectedOptionId"
              />
              <span>{{ option.label }}</span>
            </label>
          </li>
        </ul>

        <p v-if="apiError" class="error">{{ apiError }}</p>

        <button type="submit" :disabled="loading || !canVote" class="btn-vote">
          {{ loading ? 'Envoi…' : 'Voter' }}
        </button>
      </form>

      <div v-if="showResults" class="results">
        <h2 class="results-title">Résultats</h2>
        <ul class="results-list">
          <li v-for="option in (localPoll.options ?? [])" :key="option.id" class="result-item">
            <div class="result-header">
              <span class="result-label">{{ option.label }}</span>
              <span class="result-count">
                {{ option.votes_count ?? 0 }} vote{{ (option.votes_count ?? 0) !== 1 ? 's' : '' }}
                ({{ optionProgress(option) }}%)
              </span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" :style="{ width: optionProgress(option) + '%' }"></div>
            </div>
          </li>
        </ul>
        <p class="total-votes">Total : {{ totalVotes }} vote{{ totalVotes !== 1 ? 's' : '' }}</p>
      </div>
    </template>
  </div>
</template>

<style scoped>
.poll-vote {
  max-width: 600px;
  margin: 2rem auto;
  padding: 1.5rem;
}
.loading {
  color: #6b7280;
}
.poll-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}
.poll-question {
  color: #374151;
  margin-bottom: 1.5rem;
}
.status-msg {
  font-size: 1.1rem;
  padding: 0.75rem 1rem;
  border-radius: 0.375rem;
  margin-bottom: 1rem;
}
.status-expired {
  background-color: #fef2f2;
  color: #991b1b;
}
.status-voted {
  background-color: #f0fdf4;
  color: #166534;
}
.vote-form {
  margin-top: 1rem;
}
.options-list {
  list-style: none;
  padding: 0;
  margin: 0 0 1.25rem;
}
.option-item {
  margin-bottom: 0.5rem;
}
.option-label {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  cursor: pointer;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
}
.option-label:hover {
  background-color: #f9fafb;
}
.error {
  color: #e3342f;
  font-size: 0.9rem;
  margin-bottom: 0.75rem;
}
.btn-vote {
  background-color: #3490dc;
  color: white;
  padding: 0.5rem 1.25rem;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 1rem;
}
.btn-vote:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.results {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}
.results-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 1rem;
}
.results-list {
  list-style: none;
  padding: 0;
  margin: 0 0 0.75rem;
}
.result-item {
  margin-bottom: 1rem;
}
.result-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.3rem;
}
.result-label {
  font-weight: 500;
}
.result-count {
  color: #6b7280;
  font-size: 0.9rem;
}
.progress-bar {
  height: 1rem;
  background-color: #e5e7eb;
  border-radius: 9999px;
  overflow: hidden;
}
.progress-fill {
  height: 100%;
  background-color: #3490dc;
  border-radius: 9999px;
  transition: width 0.3s ease;
}
.total-votes {
  color: #6b7280;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}
</style>
