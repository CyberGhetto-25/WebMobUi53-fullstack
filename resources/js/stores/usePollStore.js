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
    const poll = polls.value.find(p => p.id === id);
    if (poll) {
      // Met à jour les champs sans remplacer l'objet proxy ni toucher aux options
      Object.keys(result).forEach(key => {
        if (key !== 'options') poll[key] = result[key];
      });
    }
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

  async function addOption(pollId, label) {
    error.value = null;
    try {
      const result = await fetchApi({ url: 'polls/' + pollId + '/options', method: 'POST', data: { label } });
      const poll = polls.value.find(p => p.id === pollId);
      if (poll) {
        if (!poll.options) poll.options = [];
        poll.options.push(result);
      }
      return result;
    } catch (err) {
      error.value = err;
      return null;
    }
  }

  async function updateOption(pollId, optionId, label) {
    error.value = null;
    try {
      const result = await fetchApi({ url: 'polls/' + pollId + '/options/' + optionId, method: 'PATCH', data: { label } });
      const poll = polls.value.find(p => p.id === pollId);
      if (poll && poll.options) {
        const index = poll.options.findIndex(o => o.id === optionId);
        if (index !== -1) poll.options[index] = result;
      }
      return result;
    } catch (err) {
      error.value = err;
      return null;
    }
  }

  async function deleteOption(pollId, optionId) {
    error.value = null;
    try {
      await fetchApi({ url: 'polls/' + pollId + '/options/' + optionId, method: 'DELETE' });
      const fresh = await fetchApi({ url: 'polls', method: 'GET' });
      polls.value = fresh;
    } catch (err) {
      error.value = err;
    }
  }

  return { polls, error, setPolls, createPoll, updatePoll, deletePoll, addOption, updateOption, deleteOption };
}
