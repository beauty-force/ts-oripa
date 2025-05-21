<template>
    <Head title="ガチャ管理" />

    <AppLayout>

        <div>
            <div class="text-center my-3 gap-3 flex justify-center">
                <Link :href="(route('admin.lost_product') + category_share.cat_route_appendix)" class="inline-block items-center w-36 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                    カード編集
                </Link>

                <Link :href="(route('admin.gacha.sorting') + category_share.cat_route_appendix)" class="inline-block items-center w-36 py-2.5 bg-cyan-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150">
                    並び替え
                </Link>
            </div>
            <div class="sm:px-4 px-6 mb-8 grid sm:grid-cols-2 grid-cols-1 gap-6">
                <Link :href="route('admin.gacha.create')+category_share.cat_route_appendix" class="rounded-lg border-2 border-green-500 shadow w-full flex items-center justify-center py-4 sm:min-h-80">
                    <span class="rounded-full bg-green-500 flex items-center justify-center text-4xl text-neutral-100 h-12 sm:h-16 w-12 sm:w-16" >
                        +
                    </span>
                </Link>

                <template v-for="(gacha, key) in gachas.data">
                    <GachaCard v-if="gacha.count_rest" :url_edit="route('admin.gacha.edit', gacha.id)+category_share.cat_route_appendix"  :gacha="gacha"/>
                </template> 

                <template v-for="(gacha, key) in gachas.data">
                    <GachaCard v-if="!gacha.count_rest" :url_edit="route('admin.gacha.edit', gacha.id)+category_share.cat_route_appendix"  :gacha="gacha"/>
                </template> 
            </div>
        </div>
    </AppLayout>
    
</template> 

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';

import GachaCard from '@/Parts/GachaCard.vue';

export default {
    components: {Head, AppLayout, Link,  GachaCard},
    props: {
        errors: Object,
        auth: Object,
        gachas:Object,
        category_share:Object,
    },
    data() {
        return {
            open: false
        }
    },
    methods: {

    },
    
}
</script>