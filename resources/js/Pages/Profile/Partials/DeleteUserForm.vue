<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h3 class="text-lg font-semibold text-red-700 mb-4">アカウント削除</h3>
            <p class="mt-2 text-sm text-neutral-600">アカウントを削除すると、そのリソースとデータはすべて永久に削除されます。</p>
        </header>

        <div class="w-full text-right">
            <button 
                @click="confirmUserDeletion" 
                class="inline-flex items-center px-6 py-2 bg-red-600 hover:bg-red-700 rounded-xl font-medium text-white transition-colors duration-300 shadow-md hover:shadow-lg"
            >
                アカウントを削除
            </button>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-red-700">本当にアカウントを削除しますか？</h2>
                <p class="mt-2 text-sm text-neutral-600">アカウントを削除すると、そのリソースとデータはすべて永久に削除されます。</p>

                <div class="mt-6">
                    <InputLabel for="password" value="Password" class="sr-only" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-2 block w-full rounded-xl border-neutral-300 focus:border-red-500 focus:ring-red-500 shadow-sm"
                        placeholder="パスワード"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <button 
                        @click="closeModal"
                        class="inline-flex items-center px-6 py-2 bg-white border border-neutral-300 rounded-xl font-medium text-neutral-700 hover:bg-neutral-50 transition-colors duration-300"
                    >
                        取消
                    </button>

                    <button
                        @click="deleteUser"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                        class="inline-flex items-center px-6 py-2 bg-red-600 hover:bg-red-700 rounded-xl font-medium text-white transition-colors duration-300 shadow-md hover:shadow-lg"
                    >
                        削除
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
