<template>
    <AppLayout>
        <Head title="新規登録" />
 
        <div class="py-6 md:px-6 px-4">
            <form @submit.prevent="submit" class="max-w-md mx-auto">
                <h2 class="text-center text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent mb-8">{{step_titles[step_integer]}}</h2>
                
                <div v-if="step_integer==0" class="flex flex-col items-center">
                    <div class="mb-8 w-full">
                        <label for="email" class="block text-base font-semibold mb-2 text-neutral-700">メールアドレス<span class="text-red-500 pl-1">*</span></label>
                        <div class="relative">
                            <input v-model="form.email" id="email" type="email" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200 placeholder:text-neutral-400" placeholder="例) user@ts-oripa.com"/>
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                    </div>

                    <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="w-full bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 text-white font-semibold py-3 px-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-[1.02]">
                        認証へ進む
                    </button>
                </div>

                <div v-if="step_integer==1" class="flex flex-col items-center">
                    <div class="mb-8 w-full">
                        <label for="code" class="block text-base font-semibold mb-2 text-neutral-700">認証コード<span class="text-red-500 pl-1">*</span></label>
                        <div class="relative">
                            <input v-model="form.code" id="code" type="tel" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200" placeholder="メールに記載された4桁の認証コード"/>
                            <InputError class="mt-2" :message="form.errors.code" />
                        </div>
                    </div>

                    <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="w-full bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 text-white font-semibold py-3 px-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-[1.02]">
                        次へ
                    </button>
                </div>

                <div v-if="step_integer==2" class="flex flex-col items-center">
                    <div class="mb-6 w-full">
                        <label for="invite_code" class="block text-base font-semibold mb-2 text-neutral-700" title="あなたを招待した友達の招待コード">招待コード</label>
                        <div class="relative">
                            <input v-model="form.invite_code" id="invite_code" type="text" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200" placeholder="招待した友達の招待コードを入力します" autocomplete="off"/>
                        </div>
                    </div>

                    <div class="mb-6 w-full">
                        <label for="phone" class="block text-base font-semibold mb-2 text-neutral-700">電話番号<span class="text-red-500 pl-1">*</span></label>
                        <div class="relative">
                            <input v-model="form.phone" id="phone" type="tel" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200" placeholder="例) 09012345678"/>
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>
                    </div>

                    <div class="mb-6 w-full">
                        <label for="password" class="block text-base font-semibold mb-2 text-neutral-700">パスワード<span class="text-red-500 pl-1">*</span></label>
                        <div class="relative">
                            <input v-model="form.password" id="password" :type="passwordFieldType" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200" placeholder="半角英数字6～20文字"/>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>
                    </div>

                    <div v-if="passwordFieldType === 'password'" class="mb-6 w-full">
                        <label for="password_confirmation" class="block text-base font-semibold mb-2 text-neutral-700">パスワード（確認）<span class="text-red-500 pl-1">*</span></label>
                        <div class="relative">
                            <input v-model="form.password_confirmation" id="password_confirmation" :type="passwordFieldType" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-xl shadow-sm px-4 py-3 transition-all duration-200" placeholder="パスワードを再入力してください"/>
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            <div v-if="form.password_confirmation && form.password !== form.password_confirmation" class="mt-2 text-sm text-red-500">
                                パスワードが一致しません
                            </div>
                        </div>
                    </div>

                    <div class="mb-8 w-full px-2">
                        <label class="flex items-center cursor-pointer gap-2">
                            <input type="checkbox" @click="switchVisibility()" class="w-5 h-5 rounded border-sky-300 text-sky-500 shadow-sm focus:ring-sky-500"/>
                            <span class="text-base text-neutral-700">パスワードを表示</span>
                        </label>
                    </div>
                    
                    <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="w-full bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 text-white font-semibold py-3 px-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-[1.02]">
                        登録
                    </button>
                </div>

                <div class="flex items-center justify-center text-sm mt-8">
                    <Link :href="route('login')" class="text-neutral-600 hover:text-sky-500 transition-colors duration-200">すでにアカウントをお持ちの方はこちら</Link>
                </div>
            </form>
        </div>
        
    </AppLayout>

</template>
<script>
    import axios from 'axios';
    import Checkbox from '@/Components/Checkbox.vue';
    import AppLayout from '@/Layouts/UserLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    
    import TextInput from '@/Components/TextInput.vue';
    import { Head, Link, useForm, usePage } from '@inertiajs/inertia-vue3';
    import { ref } from 'vue';


    export default {
        components: { Checkbox, Head, AppLayout, InputError, InputLabel, TextInput, Link },
        data: () => ({
            passwordFieldType: "password",
            step_integer: 0,    
            step_titles: ['アカウント登録', '認証コード入力', 'ご本人認証'],
            is_processing: false,
            data: {},
        }),
        props: {
            invitation_code: String,
            errors: Object
        },
        methods: {
            submit () {
                if (this.step_integer==0) {
                    this.form.post(route('register.email.send'), {
                        onFinish: () => {
                            this.data = usePage().props.value.flash;
                            
                            if (this.data.data) {
                                if (this.data.data.status==1) {
                                    this.step_integer = 1;
                                }
                            }
                            
                        },
                    });
                } else if (this.step_integer==1) {
                    this.form.post(route('register.email.verify'), {
                        onFinish: () => {
                            this.data = usePage().props.value.flash;
                            if (this.data.data) {
                                if (this.data.data.status==1) {
                                    this.step_integer = 2;
                                }
                            }
                        },
                    });
                } else {
                    if (this.passwordFieldType === "password" && this.form.password !== this.form.password_confirmation) {
                        return;
                    }
                    this.form.post(route('register.phone.register'), {
                        onFinish: () => {
                           
                        },
                    });
                }
                
            },
            submit_phone () {
                const data = { phone: this.form.phone };
                this.is_processing = true;
                axios.post(route('register.phone.send'), data). 
                    then(response => {
                        if(response.status == 201 || response.status == 200) {
                            
                        }
                        this.is_processing = false;
                    }).catch( error=> {
                        this.is_processing = false;
                    });
            },
            switchVisibility () {
                this.passwordFieldType = this.passwordFieldType==="password"?"text":"password";
                if (this.passwordFieldType === "text") {
                    this.form.password_confirmation = this.form.password;
                }
            },
        },
        setup(props) {
            const form = useForm( {
                phone:'',
                code:'',
                email: '',
                password: '',
                password_confirmation: '',
                invite_code: props.invitation_code,
            })

            return { form }
        },
        mounted(){
            
        },
    }
</script>