<template>
    <UserLayout>
        <Head title="ログイン"/>
        <div class="pt-6 px-4 mx-4">
            <h1 class="mb-8 text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent text-center">ログイン</h1>
            <form @submit.prevent="submit()" class="max-w-md mx-auto">
                <div class="mb-6">
                    <label for="text1" class="block text-base font-semibold mb-2 text-neutral-700">メールアドレス<span class="text-red-500 pl-1">*</span></label>
                    <input v-model="form.email" id="text1" type="email" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200" placeholder="例) user@torevegas.com"/>
                    <div v-if="errors.email" class="text-red-500 text-sm mt-1">
                        {{ errors.email }}
                    </div>
                </div>

                <div class="mb-6">
                    <label for="text2" class="block text-base font-semibold mb-2 text-neutral-700">パスワード<span class="text-red-500 pl-1">*</span></label>
                    <input v-model="form.password" id="text2" :type="passwordFieldType" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200" placeholder="半角英数字6～20文字"/>
                    <div v-if="errors.password" class="text-red-500 text-sm mt-1">
                        {{ errors.password }}
                    </div>
                </div>
                <div class="block mb-8 px-2">
                    <label class="flex items-center cursor-pointer gap-2">
                        <input type="checkbox" @click="switchVisibility()" class="w-5 h-5 rounded border-neutral-300 text-neutral-600 shadow-sm focus:ring-neutral-500"/>
                        <span class="text-base">パスワードを表示</span>
                    </label>
                </div>

                <div class="mb-8 text-center">
                    <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-block items-center px-14 py-3 bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 border border-transparent rounded-full font-semibold text-base text-white uppercase tracking-widest shadow-sm transition-all duration-200">
                        ログイン
                    </button>
                </div>
                <div class="flex flex-wrap gap-x-6 gap-y-4 justify-center text-base pt-2 pb-10">
                    <div class="flex items-center justify-center">
                        <Link :href="route('password.request')" class="text-sky-500 hover:text-sky-600 transition-colors duration-200">パスワードを忘れた方はこちら</Link>
                    </div>
                    <div class="flex items-center justify-center">
                        <Link :href="route('register')" class="text-sky-500 hover:text-sky-600 transition-colors duration-200">新規会員登録はこちら</Link>
                    </div>
                </div>
            </form>
        </div>
    </UserLayout>
</template>
<script>
    import UserLayout from '@/Layouts/UserLayout.vue';
    import { Head, Link, useForm, usePage } from '@inertiajs/inertia-vue3';
    
    export default {
        components: {  Head, UserLayout, Link },
        data: () => ({
            passwordFieldType: "password",
        }),
        props: {
            errors: Object,
            redirect_url: String,
        },
        methods: {
            submit () {
                this.form.post(route('login_post'), {
                    preserveScroll: true,
                    onFinish: () => {
                        this.form.reset('password');
                    },
                });
            },
            
            switchVisibility () {
                this.passwordFieldType = this.passwordFieldType==="password"?"text":"password";
            },
        },
        setup(props) {
            const form = useForm( {
                email:'',
                password:'',
                redirect_url: props.redirect_url
            })

            return { form }
        },
        mounted(){
        },
    }
</script>