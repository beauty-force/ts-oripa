<template>
    <Head title="獲得した商品一覧" />

    <AppLayout>
        <div class="">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <ProductHeader :main_tab="main_tab" />

                <!-- Product Grid -->
                <div v-if="products.data.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="(item, key) in products.data" 
                        :key="key" 
                        class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100"
                    >
                        <div class="p-3 overflow-hidden">
                            <div class="flex items-start gap-4 relative">
                                <div class="flex-1">
                                    <div class="flex gap-2">
                                        <div class="relative">
                                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-2">
                                                <img :src="item.image" class="h-full w-20 object-contain rounded-lg"/>
                                            </div>
                                            <img v-if="item.badge" 
                                                :src="item.badge" 
                                                class="absolute -top-2 -right-2 w-[35%] drop-shadow-lg" 
                                                :alt="item.badge" 
                                            />
                                        </div>
                                    
                                        <div class="flex-1 space-y-0.5">
                                            <div class="flex items-center gap-2">
                                                <img v-if="item.rank > 0" 
                                                    :src="`/images/badge${item.rank}.png`" 
                                                    class="h-6 drop-shadow-sm"
                                                />
                                                <div class="text-sm font-medium text-gray-700 px-2">{{item.name}}</div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="text-sm font-medium text-gray-500 bg-gray-50 px-2 rounded-full inline-block">{{item.rare}}</div>
                                                <div v-if="item.is_lost_product!=2" 
                                                    class="inline-flex items-center bg-gradient-to-r from-sky-500 to-sky-400 text-white px-3 py-0.5 rounded-full text-sm font-medium shadow-sm"
                                                >
                                                    <img src="/images/icon_cash.png" class="h-4 mr-1.5" />
                                                    <span>{{item.point.toLocaleString()}}&nbsp;PT</span>
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded-lg border border-gray-100 space-y-0.5">
                                                <div><span class="font-medium text-gray-500">追跡番号:</span> {{item.tracking_number}}</div>
                                                <div><span class="font-medium text-gray-500">発送依頼:&nbsp;</span> {{ item.updated_at }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12">
                    <div class="bg-white rounded-lg shadow-sm p-8 max-w-md mx-auto border border-gray-200">
                        <div class="text-gray-500 text-lg">商品はありません。</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import ProductHeader from '@/Components/ProductHeader.vue';

export default {
    components: {
        Head, 
        AppLayout, 
        Link,
        ProductHeader
    },
    props: {
        errors: Object,
        gacha: Object,
        category_share:Object,
        products: Object,
    },
    data() {
        return {
            main_tab : [
                {title:"未選択", route_url: route('user.products'), is_active:false},
                {title:"発送待ち", route_url: route('user.products.wait'), is_active:false},
                {title:"発送済み", route_url: route('user.products.delivered'), is_active:true},
            ],
        }
    },
}
</script>