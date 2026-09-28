<template>
    <AppLayout>
        <Head title="お問い合わせ" />
        <div class="pt-6 md:px-6 px-4">  
            <h1 class="mb-2 text-lg font-bold">お問い合わせ</h1>
            <hr class="mb-8" />

            <div class="mb-2 text-sm">
                恐れ入りますが、 トレしるオリパのお問い合わせはトレしるオリパ公式TwitterのDMからお願い致します。<br/>
                (回答にはお時間をいただく場合がございます)<br/>
            </div>
            <div class="mb-8">
                公式Twitter: <a href="https://twitter.com/トレしるオリパ" class="text-cyan-600 underline "> @トレしるオリパ </a>
            </div>
        </div>
    </AppLayout>

</template>
<script>
    import { Link, Head } from '@inertiajs/inertia-vue3';
    import AppLayout from '@/Layouts/UserLayout.vue';
    export default {
        components: { Link, AppLayout, Head },
    }
</script>