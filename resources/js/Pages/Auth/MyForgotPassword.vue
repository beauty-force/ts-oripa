<template> 
    <AppLayout>
        <Head title="パスワードリセット" />
        <div class="pt-6 md:px-6 px-4">
            <h1 class="mb-10 text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent text-center">パスワードリセット</h1>
            
            <div v-if="status" class="mb-4 font-medium text-sm w-full text-center text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="max-w-md mx-auto">
                <div class="mb-6">
                    <label for="email" class="block text-base font-semibold mb-2 text-neutral-700">メールアドレス<span class="text-red-500 pl-1">*</span></label>
                    <input v-model="form.email" id="email" type="email" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200" placeholder="例) user@ts-oripa.com"/>
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div class="flex items-center justify-center mt-4">
                    <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="w-full bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 text-white font-semibold py-3 px-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-[1.02]">
                        パスワードリセットメールを送信
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
    import AppLayout from '@/Layouts/UserLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
    
    export default {
        components: {  Head, AppLayout, InputError, InputLabel, TextInput, Link, PrimaryButton },
        data: () => ({
            passwordFieldType: "password",
        }),
        props: {
            status: String,
        },
        methods: {
            submit () {
                    this.form.post(route('password.email'), {
                        onFinish: () => {
                            
                        },
                    });
            },
        },
        setup() {
            const form = useForm( {
                email:'',
            })

            return { form }
        },
        mounted(){
            
        },
    }
</script>
