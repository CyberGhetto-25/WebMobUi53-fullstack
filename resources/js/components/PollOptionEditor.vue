<script setup>
import { ref, reactive, onMounted } from 'vue';
import { usePollStore } from '@/stores/usePollStore';
import { useFetchApi } from '@/composables/useFetchApi';

const props = defineProps({
  poll: { type: Object, required: true },
});

const { addOption, updateOption, deleteOption, error: storeError } = usePollStore();
const { fetchApi } = useFetchApi();

const localOptions = ref([]);
const localLabels = reactive({});
const newLabel = ref('');
const loading = ref(false);
const addError = ref(null);
const optionErrors = reactive({});
const showWarning = ref(false);

onMounted(async () => {
  try {
    const poll = await fetchApi({ url: 'polls/' + props.poll.secret_token });
    localOptions.value = poll.options ?? [];
    localOptions.value.forEach(o => { localLabels[o.id] = o.label; });
    showWarning.value = localOptions.value.length < 2;
  } catch {
    // fallback sur les options de la prop
    localOptions.value = [...(props.poll.options ?? [])];
    localOptions.value.forEach(o => { localLabels[o.id] = o.label; });
  }
});

async function handleAdd() {
  if (!newLabel.value.trim()) return;
  loading.value = true;
  addError.value = null;
  const result = await addOption(props.poll.id, newLabel.value.trim());
  loading.value = false;
  if (result) {
    localOptions.value.push(result);
    localLabels[result.id] = result.label;
    newLabel.value = '';
    showWarning.value = localOptions.value.length < 2;
  } else if (storeError.value?.status === 422) {
    addError.value = storeError.value.data?.errors?.label?.[0] ?? 'Erreur lors de l\'ajout.';
  } else if (storeError.value) {
    addError.value = 'Erreur lors de l\'ajout.';
  }
}

async function handleUpdate(optionId) {
  const original = localOptions.value.find(o => o.id === optionId)?.label;
  if (localLabels[optionId] === original) return;
  loading.value = true;
  optionErrors[optionId] = null;
  const result = await updateOption(props.poll.id, optionId, localLabels[optionId]);
  loading.value = false;
  if (result) {
    const index = localOptions.value.findIndex(o => o.id === optionId);
    if (index !== -1) localOptions.value[index] = result;
  } else if (storeError.value) {
    optionErrors[optionId] = storeError.value.data?.errors?.label?.[0] ?? 'Erreur lors de la mise à jour.';
  }
}

async function handleDelete(optionId) {
  if (!confirm('Supprimer cette option ?')) return;
  loading.value = true;
  await deleteOption(props.poll.id, optionId);
  loading.value = false;
  localOptions.value = localOptions.value.filter(o => o.id !== optionId);
  delete localLabels[optionId];
  delete optionErrors[optionId];
  showWarning.value = localOptions.value.length < 2;
}
</script>

<template>
  <div class="option-editor">
    <p v-if="showWarning" class="warning">
      Recommandé : au moins 2 options de réponse.
    </p>

    <p v-if="localOptions.length === 0" class="empty">
      Aucune option pour l'instant.
    </p>

    <ul v-else>
      <li v-for="option in localOptions" :key="option.id" class="option-row">
        <input v-model="localLabels[option.id]" type="text" class="option-input" />
        <button class="btn-save" :disabled="loading" @click="handleUpdate(option.id)">Sauvegarder</button>
        <button class="btn-delete" :disabled="loading" @click="handleDelete(option.id)">Supprimer</button>
        <span v-if="optionErrors[option.id]" class="field-error">{{ optionErrors[option.id] }}</span>
      </li>
    </ul>

    <div class="add-row">
      <input v-model="newLabel" type="text" placeholder="Nouvelle option…" class="option-input" />
      <button class="btn-add" :disabled="loading" @click="handleAdd">Ajouter</button>
    </div>
    <span v-if="addError" class="field-error">{{ addError }}</span>
  </div>
</template>

<style scoped>
.option-editor {
  margin-top: 0.5rem;
}
.warning {
  color: #b45309;
  font-size: 0.85rem;
  margin-bottom: 0.5rem;
}
.empty {
  color: #6b7280;
  font-size: 0.9rem;
  margin-bottom: 0.75rem;
}
ul {
  list-style: none;
  padding: 0;
  margin: 0 0 0.75rem;
}
.option-row {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-bottom: 0.4rem;
  flex-wrap: wrap;
}
.add-row {
  display: flex;
  gap: 0.4rem;
}
.option-input {
  flex: 1;
  border: 1px solid #ccc;
  border-radius: 0.25rem;
  padding: 0.35rem 0.5rem;
}
.field-error {
  color: #e3342f;
  font-size: 0.85rem;
  width: 100%;
}
button {
  padding: 0.35rem 0.6rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  white-space: nowrap;
}
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.btn-save {
  background-color: #3490dc;
  color: white;
}
.btn-add {
  background-color: #38a169;
  color: white;
}
.btn-delete {
  background-color: #e3342f;
  color: white;
}
</style>
