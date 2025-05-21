<template>
    <Head title="マイページ" />

    <AppLayout>

        <div class="m-3 p-3 text-center h-[400px] border border-neutral-200 rounded-md">
            このページにアクセスする権限がありません。
        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';

export default {
    components: {Head, AppLayout, Link},
    props: {
        errors: Object,
        auth: Object,
    },
    data(){
        return {
            catagries: ['sdfads', 'abc', 'sdfads', 'sdfads'],
        }  
    },
}
</script>