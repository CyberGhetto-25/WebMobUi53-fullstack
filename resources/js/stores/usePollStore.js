import { ref } from 'vue';
import { useFetchApi } from '@/composables/useFetchApi';

const polls = ref([]);
const error = ref(null);

export function usePollStore() {
  const { fetchApi } = useFetchApi();

  function setPolls(data) {
    polls.value = data;
  }

  async function createPoll(data) {
    error.value = null;
    try {
      const result = await fetchApi({ url: 'polls', method: 'POST', data });
      polls.value.unshift(result);
      return result;
    } catch (err) {
      error.value = err;
      return null;
    }
  }

  async function updatePoll(id, data) {
    error.value = null;
    try {
      const result = await fetchApi({ url: 'polls/' + id, method: 'PATCH', data });
      const index = polls.value.findIndex(p => p.id === id);
      if (index !== -1) polls.value[index] = result;
      return result;
    } catch (err) {
      error.value = err;
      return null;
    }
  }

  async function deletePoll(id) {
    error.value = null;
    try {
      await fetchApi({ url: 'polls/' + id, method: 'DELETE' });
      polls.value = polls.value.filter(p => p.id !== id);
    } catch (err) {
      error.value = err;
    }
  }

  return { polls, error, setPolls, createPoll, updatePoll, deletePoll };
}
