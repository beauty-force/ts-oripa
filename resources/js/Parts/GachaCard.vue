<template>
    <div class="cursor-pointer w-full overflow-hidden rounded-xl bg-gradient-to-br from-sky-50 to-white p-1.5 shadow-sm hover:shadow-lg transition-all duration-300"
        :class="{ 
            'grayscale-[80%]': !url_edit && gacha.count_rest == 0,
            'opacity-75': !url_edit && gacha.count_rest == 0
        }">
        <div class="bg-neutral-800 h-full flex flex-col rounded-lg overflow-hidden relative">
            <Link :href="url_card" class="relative group" :class="{ 'grayscale-[80%]': url_edit && gacha.count_rest == 0 }">
                <img :data-src=gacha.thumbnail class="lazy w-full transition-transform duration-300 group-hover:scale-105" />

                <!-- Countdown Label - Top Right Corner -->
                <div v-if="countdown && gacha.remaining > 0 && gacha.timeStatus <= 1"
                    :class="{'absolute top-14 left-3 z-20': url_edit, 'absolute top-3 right-3 z-20': !url_edit}">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r text-white shadow-xl border-2 transition-all duration-300"
                        :class="{'from-red-500 to-red-600 border-red-300': gacha.timeStatus == 1, 'from-teal-500 to-teal-600 border-teal-300': gacha.timeStatus == 0}">
                        <svg class="w-6 h-6 mr-2 animate-[spin_2s_linear_infinite]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <div class="flex flex-col items-center">
                            <span v-if="gacha.timeStatus == 0" class="text-xs font-medium mb-0.5">開始まで</span>
                            <span v-if="gacha.timeStatus == 1" class="text-xs font-medium mb-0.5">終了まで</span>
                            <vue-countdown :time="gacha.remaining * 1000" :transform="transform"
                                v-slot="{ days, hours, minutes, seconds }"
                                class="text-base font-black tracking-wider"
                                @end="countdown = false">
                                <span v-if="days > 0" class="text-base mr-2">{{ days }}日</span>
                                <span class="font-mono text-base">{{ hours }}:{{ minutes }}:{{ seconds }}</span>
                            </vue-countdown>
                        </div>
                    </div>
                </div>
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

                    <button @click="submit_copy(gacha.id)"
                        class="px-6 py-2 bg-gradient-to-r from-blue-500 to-sky-500 rounded-full font-medium text-sm text-white shadow-sm hover:from-blue-600 hover:to-sky-600 transition-all duration-200">
                        コピー
                    </button>

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
                class="absolute bottom-0 w-full h-full bg-neutral-900/90 z-[5] flex items-center justify-center">
                <div class="text-white text-2xl md:text-[30px] text-center font-bold px-8 py-4 bg-gradient-to-r from-sky-500 to-sky-600 rounded-xl shadow-lg">現在回せません</div>
            </div>

            <div v-if="(gacha.rank_limit > user?.current_rank && !url_edit)"
                class="absolute bottom-0 w-full h-full bg-neutral-900/90 z-10 flex flex-col justify-center items-center gap-4">
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
import { Link, usePage, useForm } from '@inertiajs/inertia-vue3';
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
        reinitializeLazyLoading: Function
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
        submit_copy() {
            confirm("このガチャをコピーしますか？").then((result) => {
                if (result) {
                    useForm({id: this.gacha.id}).post(route('admin.gacha.copy'));
                }
            });
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
                    Inertia.delete(route('admin.gacha.destroy', id), {
                        onSuccess: () => {
                            if (this.reinitializeLazyLoading) {
                                this.reinitializeLazyLoading();
                            }
                        }
                    });
                }
            });
        },

        transform(props) {
            Object.entries(props).forEach(([key, value]) => {
                // Adds leading zero
                const digits = value < 10 && key != 'days' ? `0${value}` : value;

                // uses singular form when the value is less than 2
                const word = value < 2 ? key.replace(/s$/, '') : key;

                props[key] = `${digits}`;
            });

            return props;
        },
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