<script setup>
  import PollTable from './components/PollTable.vue';
  import { usePollStore } from '@/stores/usePollStore';

  const props = defineProps({
    loginUrl: { type: String, default: null },
    username: { type: String, default: null },
    polls: { type: Array, default: () => [] },
  });

  console.log('props.polls', props.polls);
  
  const { setPolls } = usePollStore();
  setPolls(props.polls);
</script>

<template>
  <PollTable />
</template>
