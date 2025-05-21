<template>
    <Head title="ガチャ完了" />

    <AppLayout>
        <div class="pt-6 md:px-6 px-4 max-w-3xl mx-auto">  
            <h1 class="mb-8 text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent text-center">ガチャ完了</h1>

            <!-- Result Message -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-neutral-100">
                <div v-if="point" class="text-center space-y-2">
                    <p class="text-lg font-semibold text-neutral-800">
                        計{{number_products}}点の商品を計{{point.toLocaleString()}}PTに交換しました
                    </p>
                    <p class="text-sm text-neutral-600">
                        その他の商品は「獲得した商品一覧」にお送りしています
                    </p>
                </div>
                <div v-else class="text-center space-y-2">
                    <p class="text-lg font-semibold text-neutral-800">
                        全ての商品を「獲得した商品一覧」にお送りしました
                    </p>
                </div>
            </div>

            <!-- Gacha Again Section -->
            <div v-if="(gacha.ableCount>0)" class="py-6 px-2 mb-8 max-w-xl mx-auto">
                <h3 class="text-xl font-bold text-neutral-800 text-center mb-6">もう一度ガチャる！</h3>
                <img :src="gacha.thumbnail" class="block w-full max-w-48 mx-auto mb-6 rounded-lg shadow-sm" />

                <div class="w-full mt-4 py-2">

                    <div class="mx-auto md:max-w-md px-1 py-2 space-y-4">
                        <div class="text-neutral-600 font-[mplus2]">
                            <div class="flex justify-between items-center w-full">
                                <div class="flex items-center gap-2 w-[45%]">
                                    <div class="bg-sky-400/50 p-1.5 rounded-lg">
                                        <img src="/images/icon_cash.png" class="h-6" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-base md:text-lg font-bold">{{ format_number(gacha.point) }}</span>
                                        <span class="text-xs">/ 1回</span>
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col items-end gap-2">
                                    <span v-if="gacha.count_card > 0" class="text-xs">
                                        残り {{ format_number(gacha.count_rest) }} / {{ format_number(gacha.count_card) }}
                                    </span>
                                    <div v-else class="flex items-center">
                                        <span class="font-medium text-gray-400">- / -</span>
                                    </div>
                                    <div class="w-full">
                                        <div class="w-full h-2.5 rounded-full overflow-hidden bg-neutral-400/50">
                                            <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-sky-500 transition-all duration-500"
                                                :style="gacha.count_card > 0 ? { width: progress_value } : {}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <GachaButtons :gacha="{ ...gacha, code }" />
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap justify-center gap-3 mb-12 whitespace-nowrap">
                <Link :href="route('main')" 
                    class="flex-1 items-center flex justify-center max-w-56 px-4 py-3 rounded-full font-semibold text-base text-white bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 transition-all duration-200 shadow-sm hover:shadow-md">
                    <span>ガチャページへ</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </Link>
                <Link :href="route('user.products')" 
                    class="flex-1 items-center flex justify-center max-w-56 px-4 py-3 rounded-full font-semibold text-base text-white bg-gradient-to-r from-neutral-600 to-neutral-500 hover:from-neutral-700 hover:to-neutral-600 transition-all duration-200 shadow-sm hover:shadow-md">
                    <span>獲得した商品一覧へ</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import GachaButtons from '@/Parts/GachaButtons.vue';

export default {
    components: {Head, AppLayout, Link, GachaButtons}, 
    props: {
        errors: Object,
        auth: Object,
        number_products: Number,
        point: Number,
        gacha: Object,
    },
    computed: {
        progress_value() {
            return Math.round(this.gacha.count_rest * 100.0 / this.gacha.count_card) + '%'
        },
        progress_background_width() {
            if (this.gacha.count_rest == 0) return '100%';
            return Math.round(this.gacha.count_card * 100.0 / this.gacha.count_rest) + '%'
        }
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
    }
}
</script>