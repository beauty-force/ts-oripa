<template>
    <AppLayout>
        <Head title="管理者確認" />
 
        <div class="pt-6 px-4 mx-4">
            <form @submit.prevent="submit">
                <h2 class="underline underline-offset-8 mb-10 text-center text-lg font-bold">&nbsp;&nbsp;&nbsp;認証コード入力&nbsp;&nbsp;</h2>
                
                <div>
                    <div class="mb-8">
                        <label for="code" class="block text-base pl-2 mb-2">登録したメールに送信された6行の認証コードを入力してください。</label>
                        <input v-model="form.code" id="code" type="tel" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm " placeholder="認証コードを入力してください"/>
                        <InputError class="mt-2" :message="form.errors.code" />
                    </div>

                    <div class="mb-8 text-center border border-sky-600 rounded-full p-0.5 w-fit mx-auto">
                        <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-block items-center px-10 py-1 rounded-full font-semibold text-base text-white uppercase bg-theme bg-theme-hover">
                            次へ
                        </button>
                    </div>
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
                this.form.post(route('admin.email.verify'), {
                    onFinish: () => {
                        
                    },
                });
                
            },
            
        },
        setup(props) {
            const form = useForm( {
                code: '',
                email: props.email
            })

            return { form }
        },
        mounted(){
            
        },
    }
</script>