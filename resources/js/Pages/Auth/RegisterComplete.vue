<template>
    <AppLayout>
        <Head title="登録完了" />

        <div class="h-full flex flex-col justify-center items-center bg-gray-100 px-4">
            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4">登録完了</h2>
                    <p class="text-gray-600 mb-6">ご登録ありがとうございます！</p>
                    <p class="text-gray-600 mb-8">アカウントの登録が完了しました。</p>
                    <Link
                        :href="route('main')"
                        class="inline-flex items-center px-4 py-2 bg-theme bg-theme-hover rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                    ガチャページへ
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/UserLayout.vue';
import { Link, Head } from '@inertiajs/inertia-vue3';
</script>