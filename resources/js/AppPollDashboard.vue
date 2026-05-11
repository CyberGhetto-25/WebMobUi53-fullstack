<script setup>
  import { ref } from 'vue';
  import PollTable from './components/PollTable.vue';
  import PollForm from './components/PollForm.vue';
  import { usePollStore } from '@/stores/usePollStore';

  const props = defineProps({
    loginUrl: { type: String, default: null },
    username: { type: String, default: null },
    polls: { type: Array, default: () => [] },
  });

  const { setPolls } = usePollStore();
  setPolls(props.polls);

  const view = ref('list');
  const selectedPoll = ref(null);

  function onEdit(poll) {
    selectedPoll.value = poll;
    view.value = 'edit';
  }

  function onSaved() {
    selectedPoll.value = null;
    view.value = 'list';
  }

  function onCancelled() {
    selectedPoll.value = null;
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
