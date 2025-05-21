<template>
    <Head title="ポイントを購入する" />

    <UserLayout>
        <div class="w-full max-w-3xl mx-auto px-4 md:px-6 py-6">  
            <div class="text-center mb-6">
                <h1 class="text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent">
                    ポイント購入
                </h1>
            </div>

            <div class="space-y-4">
                <template v-for="(point, key) in points.data">
                    <a 
                        :href="route(purchase_uri, {id: point.id, coupon_id: coupon_id})" 
                        class="group flex items-center cursor-pointer bg-white text-center relative rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border-2 border-sky-100 hover:border-sky-300"
                    >
                        <div class="relative w-24 h-24 flex-shrink-0 bg-gradient-to-br from-sky-50 to-white">
                            <img 
                                class="w-full h-full object-contain" 
                                :src="point.image"
                            /> 
                            
                            <span 
                                v-if="point.discount_rate > 0"
                                class="absolute top-2 left-2 bg-rose-500 font-bold text-xs text-white px-3 py-1 rounded-full shadow-md"
                            >
                                -{{ point.discount_rate }}% OFF
                            </span>
                        </div>
                        
                        <div class="flex-1 flex items-center justify-between p-4">
                            <div class="flex flex-col items-start gap-2">
                                <span class="text-neutral-800 text-base font-bold">
                                    {{ point.title }}
                                </span>
                                <div v-if="rank && rank.pt_rate > 0" class="flex items-center">
                                    <span class="bg-rose-100 text-rose-600 text-sm px-3 py-1 rounded-full font-medium border border-rose-200">
                                        +{{ format_number(point.point * rank.pt_rate / 100) }} ボーナス
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex flex-col items-end">
                                    <span class="text-sky-600 font-bold text-xl">
                                        &yen;{{ point.amount.toLocaleString() }}
                                    </span>
                                    <span class="text-xs text-neutral-500">税込</span>
                                </div>
                                <div class="w-10 h-10 flex items-center justify-center text-sky-600 bg-sky-50 rounded-full group-hover:bg-sky-100 transition-colors duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </UserLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

export default {
    components: {Head, UserLayout, Link},
    data() {
        return {
            is_busy: false,
            is_admin: false,
            purchase_uri: 'user.point.purchase',
        }
    },
    props: {
        errors: Object,
        auth: Object,
        category_share:Object,
        points:Object,
        rank:Object,
        coupon_id: Number,
    },
    methods : {
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
    },
    mounted() {
        if (this.auth.user) {
            if (this.auth.user.type==1) {
                this.is_admin = true;
                this.purchase_uri = 'test.user.point.purchase';
            }
        }
    }
}
</script>