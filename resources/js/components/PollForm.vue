<script setup>
import { ref, computed } from 'vue';
import { usePollStore } from '@/stores/usePollStore';

const props = defineProps({
  poll: { type: Object, default: null },
});

const emit = defineEmits(['saved', 'cancelled']);

const { createPoll, updatePoll, error: storeError } = usePollStore();

const isEditMode = computed(() => props.poll !== null);

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
      <label for="title">Titre</label>
      <input id="title" v-model="title" type="text" required />
      <span v-if="fieldErrors.title" class="field-error">{{ fieldErrors.title[0] }}</span>
    </div>

    <div class="field">
      <label for="question">Question</label>
      <textarea id="question" v-model="question" required></textarea>
      <span v-if="fieldErrors.question" class="field-error">{{ fieldErrors.question[0] }}</span>
    </div>

    <div class="field field--checkbox">
      <label>
        <input v-model="launchImmediately" type="checkbox" />
        Lancer immédiatement
      </label>
    </div>

    <div class="field field--checkbox">
      <label>
        <input v-model="allowMultipleChoices" type="checkbox" />
        Autoriser plusieurs réponses
      </label>
      <span v-if="fieldErrors.allow_multiple_choices" class="field-error">{{ fieldErrors.allow_multiple_choices[0] }}</span>
    </div>

    <div class="field field--checkbox">
      <label>
        <input v-model="resultsPublic" type="checkbox" />
        Résultats publics
      </label>
      <span v-if="fieldErrors.results_public" class="field-error">{{ fieldErrors.results_public[0] }}</span>
    </div>

    <div class="field">
      <label for="duration">Durée (minutes)</label>
      <input id="duration" v-model="duration" type="number" min="1" />
      <span v-if="fieldErrors.duration" class="field-error">{{ fieldErrors.duration[0] }}</span>
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
  border: 1px solid #ccc;
  border-radius: 0.25rem;
  padding: 0.4rem 0.5rem;
}
textarea {
  min-height: 4rem;
  resize: vertical;
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
  background-color: #3490dc;
  color: white;
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
