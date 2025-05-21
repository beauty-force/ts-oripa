<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h3 class="text-lg font-semibold text-sky-700 mb-4">パスワード変更</h3>
            <p class="mt-2 text-sm text-neutral-600">安全を確保するには、アカウントで長くてランダムなパスワードを使用するようにしてください。</p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-8 space-y-6">
            <div class="space-y-6">
                <div>
                    <InputLabel for="current_password" value="現在のパスワード" class="text-neutral-700 font-medium pl-2" />
                    <TextInput
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        class="mt-2 block w-full rounded-xl border-neutral-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                        autocomplete="current-password"
                    />
                    <InputError :message="form.errors.current_password" class="ml-2 mt-2" />
                </div>

                <div>
                    <InputLabel for="password" value="新しいパスワード" class="text-neutral-700 font-medium pl-2" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-2 block w-full rounded-xl border-neutral-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                        autocomplete="new-password"
                    />
                    <InputError :message="form.errors.password" class="ml-2 mt-2" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="パスワードの確認" class="text-neutral-700 font-medium pl-2" />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-2 block w-full rounded-xl border-neutral-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm"
                        autocomplete="new-password"
                    />
                    <InputError :message="form.errors.password_confirmation" class="ml-2 mt-2" />
                </div>
            </div>

            <div class="flex gap-4 items-center justify-end pt-4">
                <Transition 
                    enter-from-class="opacity-0" 
                    leave-to-class="opacity-0" 
                    class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 font-medium">
                        保存されました。
                    </p>
                </Transition>
                <button 
                    type="submit" 
                    :class="{ 'opacity-50': form.processing }" 
                    :disabled="form.processing"
                    class="inline-flex items-center px-8 py-2 bg-sky-600 hover:bg-sky-700 rounded-xl font-semibold text-base text-white transition-colors duration-300 shadow-md hover:shadow-lg">
                    保存
                </button>
            </div>
        </form>
    </section>
</template>
