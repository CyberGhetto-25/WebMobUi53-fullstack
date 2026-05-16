<script setup>
  import { ref, computed } from 'vue';
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
  <div>
    <div v-if="view === 'list'">
      <button @click="view = 'create'">Nouveau sondage</button>
      <PollTable @edit="onEdit" />
    </div>

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
