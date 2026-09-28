<template>
    <AppLayout>
        <Head title="認証コード入力" />

        <div class="pt-6 px-4 mx-4">
            <h1 class="mb-8 text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent text-center">認証コード入力</h1>
            <form @submit.prevent="submit" class="max-w-md mx-auto">
                <div class="mb-6">
                    <label for="code" class="block text-base font-semibold mb-2 text-neutral-700 px-2">
                        認証コードを入力してください。
                        <span class="text-red-500 pl-1">*</span>
                    </label>
                    <input
                        v-model="form.code"
                        id="code"
                        type="tel"
                        class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200 placeholder:text-gray-400"
                        placeholder="認証コードを入力してください"
                    />
                    <InputError class="text-red-500 text-sm mt-1" :message="form.errors.code" />
                </div>

                <div class="mb-8 text-center">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        :class="{ 'opacity-50': form.processing }"
                        class="inline-block items-center px-14 py-3 bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 border border-transparent rounded-full font-semibold text-base text-white uppercase tracking-widest shadow-sm transition-all duration-200"
                    >
                        認証
                    </button>
                </div>

                <div class="flex justify-center text-sm text-gray-500 pt-2">
                    メールアドレス:
                    <span class="ml-1 text-sky-500 font-semibold">{{ email }}</span>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<script>
    import AppLayout from '@/Layouts/UserLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    
    import TextInput from '@/Components/TextInput.vue';
    import { Head, Link, useForm, usePage } from '@inertiajs/inertia-vue3';

    import FingerprintJS from '@fingerprintjs/fingerprintjs';

    export default {
        components: { Head, AppLayout, InputError, InputLabel, TextInput, Link },
        data: () => ({
            is_processing: false,
            data: {},
        }),
        props: {
            email: String,
            errors: Object
        },
        methods: {
            submit () {
                this.form.post(route('email.verify'), {
                    onFinish: () => {
                        
                    },
                });
                
            },
            
        },
        setup(props) {
            const form = useForm( {
                code: '',
                email: props.email,
                fingerprint: ''
            })

            return { form }
        },
        mounted(){
            FingerprintJS.load().then(fp => {
                fp.get().then(result => {
                    this.form.fingerprint = result.visitorId;
                });
            });
        },
    }
</script>