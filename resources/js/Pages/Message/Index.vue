<template>
    <div>
        <h1>Message</h1>
        <div
            v-for="msg in messages"
            class="flex w-24"
            :class="$page.props.auth.user.name == msg.user.name ? 'text-red-500' : 'text-blue-500'"
        >
            <div class="font-bold">{{ msg.user.name }} :</div>
            <div>
                {{ msg.text }}
            </div>
        </div>
        <input autofocus type="text" v-model="msg" />
        <button @keydown.enter="sendMessage" @click="sendMessage">Send</button>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';

const msg = ref('');

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    messages: {
        type: Object,
        required: true
    }
});

window.Echo.private('nexus').listen('GotMessage', async (e) => {
    form.get('messages')
});

const form = useForm({
    text: msg.value
});

function sendMessage() {
    form.text = msg.value;

    console.log('Form value in send', form.text);

    console.log('Message value in send', msg.value);

    form.post('message', {
        onSuccess: () => {
            form.reset();
        }
    });
}
</script>
