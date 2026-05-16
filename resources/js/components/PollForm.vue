<script setup>
import { ref, computed } from 'vue';
import { usePollStore } from '@/stores/usePollStore';
import PollOptionEditor from './PollOptionEditor.vue';

const props = defineProps({
  poll: { type: Object, default: null },
});

const emit = defineEmits(['saved', 'cancelled']);

const { polls, createPoll, updatePoll, error: storeError } = usePollStore();

const isEditMode = computed(() => props.poll !== null);

const currentOptionsLength = computed(() => {
  if (!isEditMode.value || !props.poll) return 0;
  return polls.value.find(p => p.id === props.poll.id)?.options?.length ?? 0;
});

const title = ref(props.poll?.title ?? '');
const question = ref(props.poll?.question ?? '');
const launchImmediately = ref(props.poll ? !props.poll.is_draft : false);
const allowMultipleChoices = ref(props.poll?.allow_multiple_choices ?? false);
const resultsPublic = ref(props.poll?.results_public ?? false);
const duration = ref(null);

const loading = ref(false);
const fieldErrors = ref({});

async function handleSubmit() {
  loading.value = true;
  fieldErrors.value = {};

  const formData = {
    title: title.value || null,
    question: question.value,
    is_draft: !launchImmediately.value,
    allow_multiple_choices: allowMultipleChoices.value,
    results_public: resultsPublic.value,
    duration: duration.value !== null && duration.value !== '' ? Number(duration.value) : null,
  };

  const result = isEditMode.value
    ? await updatePoll(props.poll.id, formData)
    : await createPoll(formData);

  loading.value = false;

  if (result) {
    emit('saved');
  } else if (storeError.value?.status === 422) {
    fieldErrors.value = storeError.value.data?.errors ?? {};
  }
}
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <div class="field">
      <label for="title" class="text-gray-700 dark:text-gray-300">Titre</label>
      <input
        id="title" v-model="title" type="text" required
        class="bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
      />
      <span v-if="fieldErrors.title" class="field-error">{{ fieldErrors.title[0] }}</span>
    </div>

    <div class="field">
      <label for="question" class="text-gray-700 dark:text-gray-300">Question</label>
      <textarea
        id="question" v-model="question" required
        class="bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
      ></textarea>
      <span v-if="fieldErrors.question" class="field-error">{{ fieldErrors.question[0] }}</span>
    </div>

    <div class="field field--checkbox">
      <label class="text-gray-700 dark:text-gray-300">
        <input v-model="launchImmediately" type="checkbox" />
        Lancer immédiatement
      </label>
    </div>

    <div class="field field--checkbox">
      <label class="text-gray-700 dark:text-gray-300">
        <input v-model="allowMultipleChoices" type="checkbox" />
        Autoriser plusieurs réponses
      </label>
      <span v-if="fieldErrors.allow_multiple_choices" class="field-error">{{ fieldErrors.allow_multiple_choices[0] }}</span>
    </div>

    <div class="field field--checkbox">
      <label class="text-gray-700 dark:text-gray-300">
        <input v-model="resultsPublic" type="checkbox" />
        Résultats publics
      </label>
      <span v-if="fieldErrors.results_public" class="field-error">{{ fieldErrors.results_public[0] }}</span>
    </div>

    <div class="field">
      <label for="duration" class="text-gray-700 dark:text-gray-300">Durée (minutes)</label>
      <input
        id="duration" v-model="duration" type="number" min="1"
        class="bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
      />
      <span v-if="fieldErrors.duration" class="field-error">{{ fieldErrors.duration[0] }}</span>
    </div>

    <div class="section">
      <h3 class="section-title">Options de réponse</h3>
      <PollOptionEditor
        v-if="isEditMode"
        :poll="poll"
        :key="currentOptionsLength"
      />
      <p v-else class="hint">Vous pourrez ajouter des options après avoir sauvegardé le sondage.</p>
    </div>

    <div class="actions">
      <button type="button" @click="emit('cancelled')">Annuler</button>
      <button type="submit" :disabled="loading">
        {{ loading ? 'Enregistrement…' : (isEditMode ? 'Mettre à jour' : 'Créer') }}
      </button>
    </div>
  </form>
</template>

<style scoped>
.field {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}
.field label {
  font-weight: 600;
  margin-bottom: 0.25rem;
}
.field--checkbox {
  flex-direction: row;
  align-items: center;
  gap: 0.5rem;
}
.field--checkbox label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: normal;
  margin-bottom: 0;
}
.field-error {
  color: #e3342f;
  font-size: 0.85rem;
  margin-top: 0.25rem;
}
input[type="text"],
input[type="number"],
textarea {
  border: 1px solid;
  border-radius: 0.25rem;
  padding: 0.4rem 0.5rem;
}
textarea {
  min-height: 4rem;
  resize: vertical;
}
.section {
  margin-bottom: 1.25rem;
  padding-top: 0.5rem;
  border-top: 1px solid #e5e7eb;
}
.section-title {
  font-size: 0.95rem;
  font-weight: 600;
  margin: 0 0 0.75rem;
}
.hint {
  color: #6b7280;
  font-size: 0.9rem;
}
.actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}
button {
  padding: 0.4rem 0.8rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}
button[type="submit"] {
  background-color: #4a1d96;
  color: white;
}
button[type="submit"]:not(:disabled):hover {
  background-color: #3b0764;
}
button[type="submit"]:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
button[type="button"] {
  background-color: #e2e8f0;
  color: #333;
}
</style>
