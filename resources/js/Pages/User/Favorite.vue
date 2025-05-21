<template>
    <Head title="お気に入り一覧" />

    <AppLayout>
        <div class="md:px-6 px-4 pt-4">
            <h1 class="underline underline-offset-8 mb-8 text-center text-lg font-bold">&nbsp;&nbsp;&nbsp;お気に入り一覧&nbsp;&nbsp;&nbsp;</h1>
            <div v-if="products.data.length" class="mb-8 grid md:grid-cols-2 gap-2">
                <template v-for="(item, key) in products.data">
                    <Link :href="route('user.dp.detail', {id: item.product.id})"  class="py-2 px-3 border rounded-md hover:bg-slate-100 flex items-center justify-between mb-1 h-max">
                        <img :src="item.product.image" class="h-24 object-contain"/>
                        <div class="flex-1 text-sm px-2 flex flex-col justify-evenly h-24 items-start">
                            <div class="text-base font-bold px-3">{{item.product.name}}</div>
                            <div class="text-sm px-3">{{item.product.rare}}</div>
                            <div class="py-0.5 bg-red-500 text-white rounded-full text-center mx-3 min-w-24 px-3 md:px-4">{{format_number(item.product.dp)}} EP</div>
                        </div>
                        <div class="text-neutral-500">
                            <ChevronRightIcon class="w-5"/>
                        </div>
                    </Link>
                </template>
            </div>
            <div v-else class="mb-8">
                商品はありません。
            </div>
        </div>

    </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import { ChevronRightIcon } from "@heroicons/vue/24/outline"

export default {
    components: {Head, AppLayout, Link, ChevronRightIcon},
    props: {
        errors: Object,
        auth: Object,
        category_share: Object,
        products: Object,
    },
    data(){
        return {
            // catagries: ['sdfads', 'abc', 'sdfads', 'sdfads'],
        }  
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        }, 
    },
}
</script>