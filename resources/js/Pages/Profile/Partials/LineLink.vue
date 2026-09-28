<script setup>
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

// Get authenticated user
const user = usePage().props.value.auth.user;

const props = defineProps({
    line_link_url: String,
});

// Generate LINE Add Friend link
const lineReferralLink = `${props.line_link_url}`;

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
            <h3 class="text-lg font-semibold text-sky-700 mb-4">LINE 連携</h3>
            <p class="mt-2 text-sm text-neutral-600">
                LINEで友だち追加すると、お得なクーポンや最新の販売情報をいち早く受け取れます！
            </p>
        </header>

        <div class="mt-8 space-y-6">
            <div v-if="form.line_id" class="bg-green-50 border border-green-200 rounded-xl p-6">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-green-700 font-medium">LINEと連携済みです</p>
                    </div>
                    <button @click="disconnectLine" 
                            class="inline-flex items-center gap-2 rounded-xl bg-white border border-red-300 hover:bg-red-50 text-red-600 text-sm px-6 py-2 transition-all duration-300 shadow-sm hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        連携を解除する
                    </button>
                </div>
            </div>
            <div v-else class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-amber-700 font-medium">LINEと未連携です</p>
                    </div>
                    <a :href="lineReferralLink" target="_blank"
                        class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm px-6 py-2 transition-all duration-300 shadow-md hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                        </svg>
                        友だち追加する
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
