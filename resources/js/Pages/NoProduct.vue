<template>
    <Head title="マイページ" />

    <AppLayout>

        <div class="m-3 px-3 pt-10 text-center h-[400px] border border-neutral-200 rounded-md">
            <div v-html="text" class="whitespace-pre-line"> </div>
            

            <div class="mt-8 mb-8 text-center">
                <Link :href="route('main')" type="submit" class="inline-block items-center px-14 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                    ガチャページへ
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';

export default {
    components: {Head, AppLayout, Link},
    props: {
        errors: Object,
        auth: Object,
        text: String,
    },
}
</script>