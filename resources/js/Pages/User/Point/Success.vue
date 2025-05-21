<template>
    <Head title="ポイント購入完了" />

    <UserLayout>
        <div class="pt-6 md:px-6 px-4">
            <!-- Success Card -->
            <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden border border-neutral-100">
                <!-- Success Header -->
                <div class="p-6 text-center">
                    <!-- Success Icon -->
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-r from-sky-500 to-sky-600 flex items-center justify-center animate-bounce-subtle">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <!-- Title -->
                    <h1 class="text-xl md:text-2xl font-bold text-neutral-800 mb-2">
                        ポイント購入完了
                    </h1>

                    <!-- Success Message -->
                    <p class="text-neutral-600 mb-6">
                        ポイント購入に成功しました。
                    </p>

                    <!-- Action Button -->
                    <Link 
                        :href="route('main')" 
                        class="inline-flex items-center px-8 py-3 rounded-lg font-medium text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg"
                    >
                        <span>ガチャページへ</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                </div>

                <!-- Decorative Bottom Border -->
                <div class="h-1 bg-gradient-to-r from-sky-500 to-sky-600"></div>
            </div>
        </div>
    </UserLayout>
</template>

<script>
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

export default {
    components: {Head, UserLayout, Link},
    props: {
        errors: Object,
        auth: Object,
        category_share: Object,
        points: Object,
    },
    data() {
        return {}
    },
    methods: {},
    mounted() {}
}
</script>

<style>
.animate-bounce-subtle {
    animation: bounce-subtle 2s infinite;
}

@keyframes bounce-subtle {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}
</style>