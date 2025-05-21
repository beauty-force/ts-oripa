<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.value.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    avatar: null
});

const imagePreview = ref(user.avatar ? '/images/avatars/' + user.avatar : '/images/icon_user.png');
const avatarRef = ref(null);

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        imagePreview.value = URL.createObjectURL(file);
        form.avatar = file;
    }
}

const onChooseAvatar = () => {
    if (avatarRef.value) {
        avatarRef.value.click();
    }
}

const resetAvatar = () => {
    imagePreview.value = user.avatar ? '/images/avatars/' + user.avatar : '/images/icon_user.png';
    form.avatar = null;
    if (avatarRef.value) {
        avatarRef.value.value = '';
    }
}

</script>

<template>
    <section>
        <header>
            <h3 class="text-lg font-semibold text-sky-700 mb-4">プロフィール情報</h3>
            <p class="mt-2 text-sm text-neutral-600">アカウントのプロフィール情報と電子メール アドレスを更新します。</p>
        </header>

        <form @submit.prevent="form.post(route('profile.update'))" class="mt-8 space-y-6">
            <div class="flex flex-col items-center justify-center gap-4">
                <div class="relative group">
                    <div class="relative">
                        <img :src="imagePreview" class="h-32 w-32 rounded-2xl object-cover shadow-lg transition-all duration-300 group-hover:scale-105 group-hover:shadow-xl" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-2">
                            <span class="text-white text-sm font-medium">画像を変更</span>
                        </div>
                    </div>
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button 
                            type="button" 
                            class="bg-white rounded-full p-2 shadow-lg hover:shadow-xl transition-shadow duration-300"
                            @click="onChooseAvatar"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <input type="file" accept="image/*" hidden ref="avatarRef" @change="onFileChange" />
                <div class="flex gap-2">
                    <button 
                        type="button" 
                        class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm px-6 py-2 transition-all duration-300 shadow-md hover:shadow-lg"
                        @click="onChooseAvatar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                        画像を選択
                    </button>
                    <button 
                        type="button" 
                        class="inline-flex items-center gap-2 rounded-xl bg-white border border-neutral-300 hover:bg-neutral-50 text-neutral-700 text-sm px-6 py-2 transition-all duration-300 shadow-sm hover:shadow-md"
                        @click="resetAvatar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                        リセット
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <InputLabel for="name" value="名前" class="text-neutral-700 font-medium pl-2" />
                    <TextInput 
                        id="name" 
                        type="text" 
                        class="mt-2 block w-full rounded-xl border-neutral-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm" 
                        v-model="form.name" 
                        required 
                        autofocus
                        autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="メールアドレス" class="text-neutral-700 font-medium pl-2" />
                    <TextInput 
                        id="email" 
                        type="email" 
                        class="mt-2 block w-full rounded-xl border-neutral-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm" 
                        v-model="form.email" 
                        required
                        autocomplete="email" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div v-if="user.email_verified_at === null" class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <p class="text-sm text-amber-700">
                        メールアドレスは認証されていません。
                        <Link 
                            :href="route('verification.send')" 
                            method="post" 
                            as="button"
                            class="mt-2 underline text-sm text-amber-700 hover:text-amber-800 rounded-md">
                            確認メールを再送信するには、ここをクリックしてください。
                        </Link>
                    </p>
                    <div v-show="props.status === 'verification-link-sent'"
                        class="mt-2 font-medium text-sm text-sky-600">
                        新しい確認リンクがあなたの電子メール アドレスに送信されました。
                    </div>
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
