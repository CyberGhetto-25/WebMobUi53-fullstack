<script setup>
  import { ref, computed } from 'vue';
  import { PlusCircle } from 'lucide-vue-next';
  import PollTable from './components/PollTable.vue';
  import PollForm from './components/PollForm.vue';
  import { usePollStore } from '@/stores/usePollStore';

  const props = defineProps({
    loginUrl: { type: String, default: null },
    username: { type: String, default: null },
    polls: { type: Array, default: () => [] },
  });

  const { polls, setPolls } = usePollStore();
  setPolls(props.polls);

  const view = ref('list');
  const selectedPollId = ref(null);
  const selectedPoll = computed(() => polls.value.find(p => p.id === selectedPollId.value) ?? null);

  function onEdit(poll) {
    selectedPollId.value = poll.id;
    view.value = 'edit';
  }

  function onSaved() {
    selectedPollId.value = null;
    view.value = 'list';
  }

  function onCancelled() {
    selectedPollId.value = null;
    view.value = 'list';
  }
</script>

<template>
  <div class="dashboard">
    <div class="dashboard-header">
      <a href="/" class="btn-home">← Accueil</a>
      <h1>Mes sondages</h1>
      <button v-if="view === 'list'" class="btn-new" @click="view = 'create'">
        <PlusCircle :size="16" /> Nouveau sondage
      </button>
    </div>

    <PollTable v-if="view === 'list'" @edit="onEdit" />

    <PollForm
      v-else-if="view === 'create'"
      @saved="onSaved"
      @cancelled="onCancelled"
    />

    <PollForm
      v-else
      :poll="selectedPoll"
      @saved="onSaved"
      @cancelled="onCancelled"
    />
  </div>
</template>

<style scoped>
.dashboard {
  width: 100%;
  padding: 1.5rem;
  box-sizing: border-box;
}
.dashboard-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}
.dashboard-header h1 {
  font-size: 1.25rem;
  font-weight: 700;
  flex: 1;
}
.btn-home {
  color: #6b7280;
  text-decoration: none;
  font-size: 0.9rem;
}
.btn-home:hover {
  color: #a78bfa;
}
.btn-new {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 0.9rem;
  background: #4a1d96;
  color: white;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 0.9rem;
}
.btn-new:hover {
  background: #3b0764;
}
</style>
