<template>
    <Head title="EP 設定" />

    <AppLayout>
        <div class="pt-6 md:px-6 px-4">  
            <h1 class="mb-2 text-lg font-bold">EP 設定</h1>
            <div class="flex items-center gap-2 w-full flex-wrap mb-2 justify-between border p-2 text-sm">
                <div class="flex flex-col gap-2 flex-1">
                    <div class="flex items-center gap-2">
                        <label class="w-16 text-right">名前</label>
                        <input v-model="form_search.name" type="text"
                            class="h-9 flex-1 w-0 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" />
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-16 text-right">EP</label>
                        <input v-model="form_search.min" type="number"
                            class="h-9 flex-1 w-16 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" />
                        <span>~</span>
                        <input v-model="form_search.max" type="number"
                            class="h-9 flex-1 w-16 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" />
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <button type="button" @click="clear"
                        class="rounded-md border bg-neutral-600 text-white px-4 py-2">初期化</button>
                    <button type="button" @click="search"
                        class="rounded-md border bg-neutral-600 text-white px-4 py-2">検索</button>
                </div>
            </div>
            <Pagination :search_cond="search_cond" route_name="admin.dp" :total="total"></Pagination>
            <hr class="my-4" />
            <div class="flex flex-wrap justify-evenly">

                <Link :href="route('admin.dp.create') + category_share.cat_route_appendix" class="mb-4 bg-white text-center relative rounded-lg shadow-md flex justify-center items-center" style="width:160px;height:220px">
                    <div class="rounded-full bg-green-500 text-3xl   text-neutral-100 h-10 w-10" >
                        +
                    </div>
                </Link>
                <div v-for="(dp, key) in products.data" class="mb-4 bg-white text-center relative rounded-lg shadow-md" style="width:160px;height:220px">
                    <div class="text-center h-3/5 pt-2 px-1 flex justify-center" style="height:63%">
                        <img class="inline-block object-contain" :src="dp.image"/> 
                    </div>
                    <div class="text-sm  px-3 flex items-center justify-center font-bold" style="height:20%">
                        {{dp.name}}
                    </div>
                    <div class="p-1" style="height:17%">
                        <button class="rounded-full py-0.5 bg-neutral-800 text-neutral-100 w-[100px] text-xs h-5/6" >
                            {{ format_number(dp.dp) }}EP
                        </button>
                    </div>
                    <Link :href="route('admin.dp.edit', {'id':dp.id}) + category_share.cat_route_appendix" class="rounded absolute top-0 right-0 px-4 py-2 bg-green-500 text-xs text-neutral-50">
                        編集する
                    </Link>

                    <button @click="destroyDp(dp.id)" class="rounded absolute top-0 left-0 px-4 py-2 bg-red-500 text-xs text-neutral-50">
                        削除
                    </button>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import { Inertia } from '@inertiajs/inertia';
import Pagination from '@/Parts/Pagination.vue';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: {Head, AppLayout, Link, Pagination, useForm},
    props: {
        errors: Object,
        auth: Object,
        category_share:Object,
        products:Object,
        search_cond: Object,
        total: Number
    },
    data(){
        return {
            // catagries: ['sdfads', 'abc', 'sdfads', 'sdfads'],
        }  
    }, 
    methods : {
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        }, 
        destroyDp(id) {
            confirm("削除してもいいですか？", '', 'error').then((result) => {
                if (result) {
                    Inertia.delete(route('admin.dp.destroy', {'id': id}), {preserveScroll: true});
                }
            });
        },
        search() {
            this.form_search.get(route('admin.dp'));
        },
        clear() {
            this.form_search.name = "";
            this.form_search.min = "";
            this.form_search.max = "";
            this.search();
        },
    },
    setup(props) {
        
        const form_search = useForm(props.search_cond);

        return { form_search }
    },
}
</script>