<template>
    <Head title="クーポンの獲得" />

    <AppLayout>
        <div class="pt-6 md:px-6 px-4">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent">
                    クーポンの獲得
                </h1>
            </div>
            
            <!-- Coupon Form Section -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 mb-8 border border-neutral-100">
                <form @submit.prevent="submit()">
                    <div class="mb-4">
                        <label for="code" class="block font-medium text-sm text-neutral-700 mb-2 ml-1">
                            コードを入力してください! 
                            <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex gap-3 flex-wrap">
                            <div class="flex-1 relative">
                                <input 
                                    v-model="form.code" 
                                    id="code" 
                                    type="text" 
                                    class="w-full border-neutral-200 focus:border-sky-400 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-lg shadow-sm placeholder-neutral-400 pl-4 pr-4 py-2 transition-all duration-200"
                                    placeholder="クーポンコードを入力"
                                />
                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                            </div>
                            <button 
                                type="submit" 
                                class="px-8 py-2 text-white rounded-lg bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2"
                            >
                                <span>獲得</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                        <div v-if="errors.code" class="text-rose-500 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ errors.code }}
                        </div>
                    </div>
                </form>
            </div>

            <!-- Coupon History Section -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 border border-neutral-100">
                <h3 class="text-lg font-semibold text-neutral-700 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    獲得履歴
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead>
                            <tr>
                                <th class="px-2 py-3 text-left text-sm font-medium text-neutral-500 uppercase tracking-wider">名前</th>
                                <th class="px-2 py-3 text-left text-sm font-medium text-neutral-500 uppercase tracking-wider">ポイント</th>
                                <th class="px-2 py-3 text-left text-sm font-medium text-neutral-500 uppercase tracking-wider">獲得時間</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            <tr v-for="coupon in coupons" class="hover:bg-neutral-50 transition-colors duration-150">
                                <td class="px-2 py-3 text-sm text-neutral-800">{{ coupon.title }}</td>
                                <td class="px-2 py-3 text-sm text-neutral-800" v-if="coupon.type == 'NORMAL'">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                                        {{ coupon.point }}pt
                                    </span>
                                </td>
                                <td class="px-2 py-3 text-sm text-neutral-600" v-if="coupon.status == 1">{{ coupon.acquired_time }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import { Inertia } from '@inertiajs/inertia';

export default {
    components: {Head, AppLayout, Link},
    props: {
        coupons: Object,
        errors: Object,
        types: Object,
    },
    mounted() {
        
    },
    methods: {
        submit() {
            this.form.post(route('user.coupon.post'));
        }
    },
    setup(props) {
        const form = useForm({
            code: '',
        })
        return { form }
    }
}
</script>