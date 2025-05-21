<script setup>
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

// Get authenticated user
const user = usePage().props.value.auth.user;

const props = defineProps({
    line_link_url: String,
});

// Generate LINE Add Friend link
const lineReferralLink = `${props.line_link_url}?ref=${user.id}`;

// Form for storing LINE User ID
const form = useForm({
    line_id: user.line_id || null,
});

// Disconnect function
const disconnectLine = () => {
    form.post(route('line.disconnect'), {
        preserveScroll: true,
        onSuccess: () => {
            form.line_id = null; // Reset local state after disconnect
        },
    });
};

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-neutral-600">LINE 連携</h2>
            <p class="mt-1 text-sm text-neutral-600">
                LINEを連携すると限定クーポンや販売情報がGETできます！
            </p>
        </header>

        <div class="mt-6 flex flex-col items-center space-y-4">
            <div v-if="form.line_id" class="flex flex-wrap justify-between items-center gap-2 w-full">
                <p class="text-green-600 font-medium">LINEアカウントが連携されています ✅</p>
                <button @click="disconnectLine" 
                        class="rounded-full bg-red-500 hover:bg-red-600 text-white text-sm px-6 py-1">
                    LINE 連携解除
                </button>
            </div>
            <div v-else class="flex flex-wrap justify-between items-center gap-2 w-full">
                <p class="text-red-600 font-medium">LINEアカウントが未連携 ❌</p>
                <a :href="lineReferralLink" target="_blank"
                    class="rounded-full bg-green-500 hover:bg-green-600 text-white text-sm px-6 py-1">
                    LINE 連携する
                </a>
            </div>
        </div>
    </section>
</template>
