<template>
    <div v-if="gacha.count_rest>0" class="w-full mx-auto flex flex-col justify-center gap-3">
        <div class="flex font-[mplus2] gap-3">
            <button @click="clickgacha(1)" 
                :class="{ 'opacity-50 cursor-not-allowed': processing }" 
                :disabled="processing" 
                class="flex-1 relative group overflow-hidden rounded-xl bg-gradient-to-br from-sky-400 to-sky-600 hover:from-sky-500 hover:to-sky-700 text-white transition-all duration-300 shadow-sm hover:shadow-md">
                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                <div class="relative px-6 py-2 flex items-center justify-center gap-2">
                    <span class="font-bold text-lg">1回</span>
                    <span class="font-medium text-sm">ガチャ</span>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                </div>
            </button>
        
            <button v-if="gacha.count_rest > 1 && gacha.gacha_limit == 0" 
                @click="clickgacha(10)" 
                :class="{ 'opacity-50 cursor-not-allowed': processing }" 
                :disabled="processing" 
                class="flex-1 relative group overflow-hidden rounded-xl bg-gradient-to-br from-rose-400 to-rose-600 hover:from-rose-500 hover:to-rose-700 text-white transition-all duration-300 shadow-sm hover:shadow-md">
                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                <div class="relative px-6 py-2 flex items-center justify-center gap-2">
                    <span class="font-bold text-lg">{{str_gacha10}}</span>
                    <span class="font-medium text-sm">ガチャ</span>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                </div>
            </button>
        </div>

        <!-- <div class="flex font-[mplus2] gap-1 flex-wrap">
            <button v-if="gacha.count_rest > 10 && gacha.gacha_limit == 0" @click="clickgacha(100)" :class="{ 'opacity-50': processing }" :disabled="processing" class="cursor-pointer rounded-md bg-gradient-to-br border border-sky-400/50 all_button text-white flex-1 text-center space-y-1 pt-1.5 pb-2">
                <div class="text-nowrap flex items-center justify-center">
                    <span class="font-extrabold text-[15px]">{{str_gacha100}}</span> 
                    <span class="font-semibold text-[11px]">ガチャ</span>
                    <img src="/images/icon_cash.png" class="ml-2 h-4" />
                    <span class="font-semibold text-[13px]">{{ format_number(point_100gacha) }}</span>
                </div>
            </button>

        </div> -->
            
            <!-- <button v-if="gacha.count_rest > 100" @click="clickgacha(500)" :class="{ 'opacity-50': processing }" :disabled="processing" class="cursor-pointer rounded-md bg-gradient-to-br border border-sky-400/50 from-red-500 to-red-700 hover:from-red-600 hover:to-red-800 text-white flex-1 text-center space-y-1 pt-1.5 pb-2">
                <div class="text-nowrap">
                    <span class="font-extrabold text-[15px]">{{str_gacha500}}</span> 
                    <span class="font-semibold text-[11px]">ガチャ</span>
                </div>
            </button> -->
    </div>
</template>

<script>
import { Link, usePage, useForm } from '@inertiajs/inertia-vue3';
import {PlayIcon} from "@heroicons/vue/24/solid";
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: {Link, PlayIcon},
    props: {
        gacha: Object,
    },
    data () {
        return {
            category_share: usePage().props.value.category_share,
            str_gacha10: "",
            str_gacha50: "",
            str_gacha100: "",
            str_gacha500: "",
            point_10gacha:0,
            point_100gacha:0,
            point_500gacha:0,
            processing: false,
        };
    }, 
    methods: {
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        }, 

        clickgacha(number) {
            let point = this.gacha.point * number;
            confirm('ガチャを引きますか？', number + '回分のポイントを' + point.toLocaleString() + 'PT消費します。').then((result) => {
                if (result) {
                    this.start_gacha(number);
                }
            })
        },

        start_gacha(number) {
            this.processing = true;
            if (this.url_edit) { return; }
            
            useForm({id:this.gacha.id, number, code: this.gacha.code}).post(route('user.gacha.start_post'), {
                onFinish: () => {
                    this.processing = false;
                }
            });
        },

        
    }, 
    mounted() {
        if(this.gacha.count_rest<=10) { 
            this.str_gacha10 = "全て";
            this.point_10gacha = this.gacha.count_rest * this.gacha.point;
        } else {
            this.str_gacha10 = "10連";
            this.point_10gacha = 10 * this.gacha.point;
        }
        if(this.gacha.count_rest<=100) { 
            this.str_gacha100 = "全て";
            this.point_100gacha = this.gacha.count_rest * this.gacha.point;
        } else {
            this.str_gacha100 = "100連";
            this.point_100gacha = 100 * this.gacha.point;
        }
        if(this.gacha.count_rest<=500) { 
            this.str_gacha500 = "全て";
            this.point_500gacha = this.gacha.count_rest * this.gacha.point;
        } else {
            this.str_gacha500 = "500連";
            this.point_500gacha = 500 * this.gacha.point;
        }
        if(!this.url_edit) {
            this.url_card = route('user.gacha', this.gacha.id) + this.category_share.cat_route_appendix;
        }
    }
}
</script>

<style>
/* Remove the old all_button styles as they're no longer needed */
</style>