<template>
    <Head title="個人情報登録" />

    <AppLayout>
        <div class="pt-6 md:px-6 px-4">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent">
                    個人情報登録
                </h1>
            </div>

            <!-- Form Container -->
            <div class="w-full max-w-3xl mx-auto bg-white shadow-sm p-4 md:p-6">
                <form @submit.prevent="submit()" class="space-y-6">
                    <!-- Name Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="first_name" class="block text-sm font-medium text-neutral-700 mb-1.5">姓</label>
                            <div class="relative">
                                <input v-model="form.first_name" id="first_name" type="text" 
                                    class="h-12 w-full px-4 rounded-lg border border-neutral-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all duration-200"
                                />
                                <div v-if="errors.first_name" class="absolute -bottom-5 left-0 text-red-500 text-sm">
                                    {{ errors.first_name }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name" class="block text-sm font-medium text-neutral-700 mb-1.5">名</label>
                            <div class="relative">
                                <input v-model="form.last_name" id="last_name" type="text" 
                                    class="h-12 w-full px-4 rounded-lg border border-neutral-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all duration-200"
                                />
                                <div v-if="errors.last_name" class="absolute -bottom-5 left-0 text-red-500 text-sm">
                                    {{ errors.last_name }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kana Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="first_name_gana" class="block text-sm font-medium text-neutral-700 mb-1.5">姓 (カナ)</label>
                            <div class="relative">
                                <input v-model="form.first_name_gana" id="first_name_gana" type="text" 
                                    class="h-12 w-full px-4 rounded-lg border border-neutral-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all duration-200"
                                />
                                <div v-if="errors.first_name_gana" class="absolute -bottom-5 left-0 text-red-500 text-sm">
                                    {{ errors.first_name_gana }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name_gana" class="block text-sm font-medium text-neutral-700 mb-1.5">名 (カナ)</label>
                            <div class="relative">
                                <input v-model="form.last_name_gana" id="last_name_gana" type="text" 
                                    class="h-12 w-full px-4 rounded-lg border border-neutral-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all duration-200"
                                />
                                <div v-if="errors.last_name_gana" class="absolute -bottom-5 left-0 text-red-500 text-sm">
                                    {{ errors.last_name_gana }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Postal Code -->
                    <div class="form-group">
                        <label for="postal_code" class="block text-sm font-medium text-neutral-700 mb-1.5">
                            郵便番号
                            <span class="text-xs text-neutral-500 ml-1">(ハイフンなし半角数字)</span>
                        </label>
                        <div class="relative">
                            <input v-model="form.postal_code" id="postal_code" type="text" 
                                class="h-12 w-full px-4 rounded-lg border border-neutral-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all duration-200 placeholder:text-neutral-300"
                                placeholder="NNN-NNNN"
                            />
                            <div v-if="errors.postal_code" class="absolute -bottom-5 left-0 text-red-500 text-sm">
                                {{ errors.postal_code }}
                            </div>
                        </div>
                    </div>

                    <!-- Prefecture -->
                    <div class="form-group">
                        <label for="prefecture" class="block text-sm font-medium text-neutral-700 mb-1.5">都道府県</label>
                        <div class="relative">
                            <select v-model="form.prefecture" id="prefecture" 
                                class="h-12 w-full px-4 rounded-lg border border-neutral-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all duration-200 appearance-none bg-white"
                            >
                                <option v-for="(item, key) in prefectures" :value="item">{{item}}</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div v-if="errors.prefecture" class="absolute -bottom-5 left-0 text-red-500 text-sm">
                                {{ errors.prefecture }}
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address" class="block text-sm font-medium text-neutral-700 mb-1.5">住所</label>
                        <div class="relative">
                            <input v-model="form.address" type="text" id="address" 
                                class="h-12 w-full px-4 rounded-lg border border-neutral-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all duration-200 placeholder:text-neutral-300"
                                placeholder="例) 渋谷区神宮前1-2-3 DPビル501号室"
                            />
                            <div v-if="errors.address" class="absolute -bottom-5 left-0 text-red-500 text-sm">
                                {{ errors.address }}
                            </div>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone" class="block text-sm font-medium text-neutral-700 mb-1.5">
                            電話番号
                            <span class="text-xs text-neutral-500 ml-1">(ハイフンなし半角数字)</span>
                        </label>
                        <div class="relative">
                            <input v-model="form.phone" id="phone" type="tel" 
                                class="h-12 w-full px-4 rounded-lg border border-neutral-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 transition-all duration-200 placeholder:text-neutral-300"
                                placeholder="09012345678"
                            />
                            <div v-if="errors.phone" class="absolute -bottom-5 left-0 text-red-500 text-sm">
                                {{ errors.phone }}
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 text-center">
                        <button type="submit" 
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                            :disabled="form.processing" 
                            class="inline-flex items-center px-8 py-3 rounded-lg font-semibold text-white bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 transition-all duration-200 shadow-sm hover:shadow-md"
                        >
                            <span v-if="form.processing" class="mr-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            保存する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import prefectures from '@/Store/prefectures';


export default {
    components: {Head, AppLayout, Link},
    props: {
        errors: Object,
        gacha: Object,
        category_share:Object,
        profiles: Object,
    },
    data() {
        return {
            prefectures: prefectures,
        }
    },
    methods: {
        submit () {
            this.form.post(route('user.address.post'), {
                onFinish: () => {},
            });
        },
    },
    setup(props) {
        let profile = {
            first_name:'',
            last_name:'',
            first_name_gana:'',
            last_name_gana:'',
            postal_code:'',
            prefecture: prefectures[0],
            address:'',
            phone:'',
        };
        if (props.profiles.length) {
            let item = props.profiles[0]
            profile = {
                first_name: item.first_name,
                last_name: item.last_name,
                first_name_gana: item.first_name_gana,
                last_name_gana: item.last_name_gana,
                postal_code: item.postal_code,
                prefecture: item.prefecture,
                address: item.address,
                phone: item.phone,
            };
        }

        const form = useForm( profile );
        return { form }
    },
    mounted() {
        
    },
}
</script>