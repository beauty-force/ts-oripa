<template>

    <Head title="配送商品" />

    <AppLayout>
        <div class="pt-2 md:px-6 px-4 mb-6">
            
            <div class="w-full text-neutral-400 mb-3">
                <Link v-for="(item, key) in main_tab" :href="item.route_url" class="inline-block px-4 py-1.5"
                    :class="{ 'border-b-2 border-red-600 text-red-600': item.is_active }">
                {{ item.title }}
                </Link>
            </div>
            <div class="flex items-center gap-2 w-full flex-wrap mb-2 py-2 justify-between text-sm">
                <div class="flex items-center gap-2 flex-1">
                    <input v-model="form_search.name" type="text" placeholder="名前、メールアドレス、電話番号、商品名"
                        class="h-9 flex-1 w-0 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" />

                </div>
                <button type="button" @click="search"
                    class="rounded-md border bg-neutral-600 text-white px-4 py-2">検索</button>
            </div>
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2">
                <template v-for="(item, key) in products.data">

                    <hr v-if="key > 0 && products.data[key - 1].user_id != item.user_id"
                        class="-mx-4 my-2 col-span-1 md:col-span-2" />

                    <button v-if="key == 0 || products.data[key - 1].user_id != item.user_id"
                        class="flex border-neutral-600 border-l-8 border-b border items-center w-full py-3 px-4 my-1 -ml-1 hover:bg-slate-100 col-span-1 md:col-span-2 shadow">
                        {{ item.user_name }} ( {{ item.email }} ) {{ item.address }}
                    </button>
                    <button class="flex border-neutral-200 items-center w-full hover:bg-slate-100 py-0.5">
                        <img :src="item.image" class="w-20 h-20 inline-block object-contain" />
                        <div
                            class="cursor-pointer min-h-20 flex items-center justify-between flex-1 text-xs py-1 px-2 my-0.5">
                            <div class="text-left">
                                <div>{{ item.name }}</div>
                                <div>{{ item.rare }}</div>
                                <div class="text-red-500">{{ item.point.toLocaleString() }}pt</div>
                                <div>{{ item.updated_at }}</div>
                            </div>
                            <!-- <div class="text-neutral-300">
                                <ChevronRightIcon class="w-5" />
                            </div> -->
                        </div>
                    </button>

                </template>
            </div>
        </div>

    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import { ChevronLeftIcon, ChevronRightIcon, EllipsisHorizontalIcon } from "@heroicons/vue/24/outline"
import Pagination from '@/Parts/Pagination.vue';

export default {
    components: { Head, AppLayout, Link, ChevronLeftIcon, ChevronRightIcon, EllipsisHorizontalIcon, Pagination },
    props: {
        errors: Object,
        gacha: Object,
        category_share: Object,
        products: Object,
        search_cond: Object,
    },
    data() {
        return {
            hasCheck: false,
            main_tab: [
                { title: "発送依頼中", route_url: route('admin.delivery'), is_active: false },
                { title: "発送済み", route_url: route('admin.delivery.completed'), is_active: false },
                { title: "取得済み", route_url: route('admin.delivery.acquired'), is_active: true },
            ],
            is_busy: false,
            profile: {},
            user_products: [],
            user: {},
            hasCheck: false,
            isCheckAll: false,
        }
    },
    setup(props) {
        const form_search = useForm(props.search_cond);
        
        return { form_search }
    },
    methods: {
        
        search() {
            this.form_search.get(route('admin.delivery'));
        },
        
    },
}
</script>