
<template> 
    <AppLayout>
        <Head title="パスワードリセット" />
        <div class="pt-6 md:px-20 px-4">  
            <h1 class="mb-10 text-xl text-center font-bold">パスワードリセット</h1>

            <div v-if="status" class="mb-4 font-medium text-sm w-full text-center text-green-600">
                {{ status }}
            </div>

            <div class="pl-4 mb-2 font-semibold text-gray-600">
                登録時のメールアドレスを入力してください！
            </div>

            <form @submit.prevent="submit">
                <div class="mb-6">
                    <!-- <label  for="email" class="block font-medium text-sm text-neutral-600 mb-1">メールアドレス</label> -->
                    <input v-model="form.email" id="email" type="email" class="h-12 w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="例) user@トレしるオリパ.com"/>
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>


                <div class="flex items-center justify-center mt-4 pb-7">
                    <button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="inline-block items-center px-10 py-2.5 rounded-md font-semibold text-sm text-white uppercase tracking-widest bg-theme bg-theme-hover transition ease-in-out duration-150">
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
