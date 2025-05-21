<template>
    <div class="cursor-pointer w-full overflow-hidden rounded-xl bg-gradient-to-br from-sky-50 to-white p-1.5 shadow-sm hover:shadow-lg transition-all duration-300"
        :class="{ 
            'grayscale-[80%]': !url_edit && gacha.count_rest == 0,
            'opacity-75': !url_edit && gacha.count_rest == 0
        }">
        <div class="bg-neutral-800 h-full flex flex-col rounded-lg overflow-hidden relative">
            <Link :href="url_card" class="relative group" :class="{ 'grayscale-[80%]': url_edit && gacha.count_rest == 0 }">
                <img :data-src=gacha.thumbnail class="lazy w-full transition-transform duration-300 group-hover:scale-105" />
            </Link>

            <template v-if="url_edit">
                <div v-if="(gacha.status == 1)"
                    class="absolute z-10 top-3 left-3 px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full font-medium text-sm text-white shadow-sm">
                    公開
                </div>
                <div v-else
                    class="absolute z-10 top-3 left-3 px-3 py-1.5 bg-gradient-to-r from-neutral-500 to-neutral-600 rounded-full font-medium text-sm text-white shadow-sm">
                    非公開
                </div>
                <div class="absolute z-10 top-3 right-3 flex flex-col gap-2">
                    <Link :href="url_edit"
                        class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full font-medium text-sm text-white shadow-sm hover:from-green-600 hover:to-emerald-600 transition-all duration-200">
                        編集する
                    </Link>

                    <button @click="destroyGacha(gacha.id)"
                        class="px-6 py-2 bg-gradient-to-r from-red-500 to-rose-500 rounded-full font-medium text-sm text-white shadow-sm hover:from-red-600 hover:to-rose-600 transition-all duration-200">
                        削除
                    </button>
                </div>
            </template>

            <div v-if="(gacha.count_rest == 0 && !url_edit)"
                class="absolute w-full h-full z-10 flex flex-col justify-center items-center gap-6 bg-neutral-900/90 backdrop-blur-sm">
                <div class="text-white text-4xl md:text-5xl w-full text-center font-black pb-2 font-[mplus2] tracking-wider">
                    SOLD OUT
                    <button v-if="user && user.type == 1"
                        class="block mx-auto mt-4 text-base font-medium text-white border border-white/30 w-fit rounded-full px-6 py-1.5 hover:bg-white hover:text-neutral-900 transition-all duration-200"
                        @click="clickCard()">排出履歴</button>
                </div>
            </div>

            <div v-if="(url_edit && gacha.count_rest != gacha.ableCount)"
                class="absolute bottom-0 w-full h-full bg-neutral-900/90 backdrop-blur-sm z-[5] flex items-center justify-center">
                <div class="text-white text-2xl md:text-[30px] text-center font-bold px-8 py-4 bg-gradient-to-r from-sky-500 to-sky-600 rounded-xl shadow-lg">現在回せません</div>
            </div>

            <div v-if="(gacha.rank_limit > user?.current_rank && !url_edit)"
                class="absolute bottom-0 w-full h-full bg-neutral-900/90 backdrop-blur-sm z-10 flex flex-col justify-center items-center gap-4">
                <div class="text-white text-xl w-full text-center font-black">ランク制限</div>
                <div class="text-white text-sm w-full text-center font-black"></div>
            </div>

            <div class="w-full flex flex-col justify-center flex-1 p-3">
                <div class="text-white font-[mplus2]">
                    <div class="flex justify-between items-center w-full">
                        <div class="flex items-center gap-2 w-[45%]">
                            <div class="bg-white/10 p-1.5 rounded-lg">
                                <img src="/images/icon_cash.png" class="h-5" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-white text-base md:text-lg font-bold">{{ format_number(gacha.point) }}</span>
                                <span class="text-gray-400 text-xs">/ 1回</span>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col items-end gap-2">
                            <span v-if="gacha.count_card > 0" class="text-white text-xs">
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
            </div>

            <div v-if="!url_edit" class="px-3 pb-3">
                <GachaButtons :gacha="gacha" />
            </div>
        </div>
    </div>
</template>

<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import { PlayIcon } from "@heroicons/vue/24/solid";
import VueCountdown from '@chenfengyuan/vue-countdown';
import GachaButtons from './GachaButtons.vue';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: { Link, PlayIcon, VueCountdown, GachaButtons },
    props: {
        gacha: Object,
        url_edit: String,
    },
    data() {
        return {
            category_share: usePage().props.value.category_share,
            url_card: "",
            url_10gacha: "",
            url_1gacha: "",
            str_gacha10: "",
            point_10gacha: 0,
            countdown: true,
            user: usePage().props.value.auth.user,
        };
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
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },

        getImageClass(image) {
            return "url('" + image + "')";
        },

        clickCard() {
            if (!this.url_edit) {
                window.location.href = this.url_card;
            }
        },

        destroyGacha(id) {
            confirm("削除してもいいですか？", '', 'error').then((result) => {
                if (result) {
                    Inertia.delete(route('admin.gacha.destroy', id));
                }
            });
        },

        // transform(props) {
        //     Object.entries(props).forEach(([key, value]) => {
        //         // Adds leading zero
        //         const digits = value < 10 && key != 'days' ? `0${value}` : value;

        //         // uses singular form when the value is less than 2
        //         const word = value < 2 ? key.replace(/s$/, '') : key;

        //         props[key] = `${digits}`;
        //     });

        //     return props;
        // },
    },
    mounted() {
        if (this.gacha.count_rest < 10) {
            this.str_gacha10 = "全て";
            this.point_10gacha = this.gacha.count_rest * this.gacha.point;
        } else {
            this.str_gacha10 = "10連";
            this.point_10gacha = 10 * this.gacha.point;
        }

        if (!this.url_edit) {
            this.url_card = route('user.gacha', this.gacha.id) + this.category_share.cat_route_appendix;
        }
    }
}
</script>