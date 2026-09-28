<template>

    <Head title="マイページ" />

    <AppLayout>
        <div class="px-2 md:px-10">
            <p class="py-3 px-6 -mx-2 md:-mx-10 font-bold align-middle text-base md:text-xl border">マイページ</p>
            <div v-if="current_rank" class="py-4 md:py-6 px-3 md:px-5 mx-2 md:mx-4 bg-neutral-200 rounded-b-xl shadow-inner text-xs md:text-sm">
                <div class="mx-4 text-center">
                    <span class="font-bold text-lg md:text-xl">{{ current_rank.title }}</span>
                    <img :src="current_rank.badge" class="p-1 w-full max-w-48 mx-auto"/>
                </div>
                <div v-if="current_rank.rank < ranks[0].rank - 1" class="w-full flex justify-end mt-3 -mb-3">
                    <span class="rounded-full border font-bold px-2 py-0.5 text-xs -mr-1 bg-neutral-400/20 border-neutral-400">ランクUP</span>
                </div>
                <div class="flex border-t border-b mt-4 relative h-5 md:h-6 px-2">
                    <span class="h-full rounded-l-md border border-neutral-800 px-4 bg-neutral-400/20 font-bold z-10 flex items-center">消費 PT</span>
                    <div class="flex-1 relative">
                        <div class="h-full bg-gray-50 rounded-r-xl overflow-hidden border-t border-b border-r border-neutral-800">
                            <div class="h-full bg-gradient-to-b from-[#01b4ef] via-[#00bffa] to-[#0488c3]" :style="{ width: `${current_pos}%` }"></div>
                        </div>
                        <div v-if="current_rank.rank > 1" class="absolute z-10 h-[130%] top-[-15%]" :style="{ left: `${mark_pos}%`}">
                            <img :src="succeed ? 'images/mark_red.svg' : 'images/mark_black.svg'" class="h-full mx-[-50%]"/>
                        </div>
                    </div>
                </div>
                <div v-if="current_rank.rank > 1" class="w-full flex justify-end my-3 px-2">
                    <div class="w-fit flex px-3 py-0.5 items-center bg-neutral-400/20 border border-neutral-400 rounded-full">
                        <img src="images/mark_red_icon.svg" class="w-4 md:w-5" />
                        <span class="text-xs md:text-sm">ランク維持達成</span>
                        <img src="images/mark_black_icon.svg" class="w-4 md:w-5 ml-3" />
                        <span class="text-xs md:text-sm">ランク維持未達成</span>
                    </div>
                </div>
                <div class="text-center mt-6">
                    <span class="text-sm md:text-lg text-black font-extrabold border bg-neutral-400/20 border-neutral-400 px-3 py-0.5">■ 現在受けられる特典一覧 ■</span>
                </div>
                <div class="rounded-lg bg-neutral-50 drop-shadow-[0px_0px_10px_rgba(0,0,0,0.1)] mt-5 mx-1 py-2 px-4 font-bold text-xs md:text-sm leading-5 md:leading-7">
                    <p>・ランクアップボーナス &nbsp;&nbsp;&nbsp;{{ current_rank.bonus > 0 ? format_number(current_rank.bonus) : '—' }} pt</p>
                    <p>{{ current_rank.pt_rate > 0 ? `・購入金額ごとに &nbsp;&nbsp;&nbsp;${current_rank.pt_rate} %ボーナスPT付与` : '・購入金額ごとのPT付与 &nbsp;&nbsp;&nbsp;通常'}}</p>
                    <p>・EP付与率 &nbsp;&nbsp;&nbsp;{{ current_rank.dp_rate > 0 ? `${current_rank.dp_rate} %` : '通常' }}</p>
                    
                </div>
            </div>
            <p class="mt-6 px-6 font-bold align-middle text-sm md:text-lg"><span class="text-[0.6em]">■</span> 会員ランクについて</p>
            <div class="px-6 mt-1 mb-4 font-bold text-xs md:text-sm">
                <p class="pb-1 leading-5 md:leading-7">
                    ボーナスPTやEP付与率上昇、 ランク限定オリパなど、さまざまな特典が受け られます。
                </p>
            </div>
            <hr class="mt-2 md:mt-4 mb-5 md:mb-6 mx-4 md:mx-6 border-neutral-300 border" />

            <p class="px-6 font-bold align-middle text-sm md:text-lg"><span class="text-[0.6em]">■</span> ランクアップ条件</p>
            <div class="px-6 mt-1 font-bold text-xs md:text-sm">
                <p class="pb-1 leading-5 md:leading-7">
                    ランクは累積のPT消費数によって変動します。<br/>
                    各ランク条件を満たすと、 即時に次のランクへと移行します。<br/>
                    <span class="text-[#FF0000]">
                        ※PTの累積は毎月1日0:00でリセットされます。
                    </span>
                </p>
            </div>

            <!-- <p class="font-bold text-sm md:text-lg pb-1 pl-4 md:pl-6">【例】</p>
            <div class="py-5 px-3 md:px-5 mx-2 md:mx-4 bg-neutral-200 rounded-xl shadow-inner text-xs md:text-sm">
                <div class="text-center">
                    <span class="text-base md:text-xl text-black font-extrabold border bg-neutral-400/20 border-neutral-400 px-5">即時昇格</span>
                </div>
                <div class="flex font-bold items-center mt-2">
                    <div class="flex-1">
                        <img :src="ranks[ranks.length - 1].badge" class="p-1 w-full max-w-48 mx-auto"/>
                    </div>
                    <div class="px-1">
                        <PlayIcon class="w-5 h-5 text-black "></PlayIcon>
                    </div>
                    <div class="flex-1">
                        <img :src="ranks[ranks.length - 2].badge" class="p-1 w-full max-w-48 mx-auto"/>
                    </div>
                </div>
                <div class="w-full flex justify-end mt-3">
                    <span class="rounded-full border font-bold px-2 py-0.5 text-xs -mr-1 bg-neutral-400/20 border-neutral-400">ランクUP</span>
                </div>
                <div class="flex border-t border-b mt-1 relative h-5 md:h-6 px-2">
                    <span class="h-full rounded-l-md border border-neutral-800 px-4 bg-neutral-400/20 font-bold z-10 flex items-center">消費 PT</span>
                    <div class="flex-1 relative">
                        <div class="h-full bg-gray-50 rounded-r-xl overflow-hidden border-t border-b border-r border-neutral-800">
                            <div class="h-full bg-gradient-to-b from-[#01b4ef] via-[#00bffa] to-[#0488c3] w-full"></div>
                        </div>
                    </div>
                </div>
                <div class="rounded-lg bg-neutral-50 drop-shadow-[0px_0px_10px_rgba(0,0,0,0.1)] mt-5 mx-1 py-2 text-center font-bold text-sm md:text-lg">
                    ランク条件達成後、 翌1ヶ月間はランク維持
                </div>
            </div> -->

            <div class="px-6 mt-8 font-bold text-xs md:text-sm">
                <p class="pb-1 leading-5 md:leading-7">
                    <span class="text-green-400 font-extrabold text-sm md:text-base">{{ ranks[ranks.length - 2].title }}</span>以上のランクに関しては、 前月までに達成したランク条件であるPT消費数の50%以上を1ヶ月以内に達成していれば、そのランクが維持されます。
                </p>
                <p class="pb-1 leading-5 md:leading-7">
                    1ヶ月間で50%未満の場合は現在のランクから1つランクダウンします。
                </p>

            </div>
            <!-- <p class="font-bold text-sm md:text-lg pb-1 pl-4 md:pl-6">【例】</p>
            <div class="py-5 px-3 md:px-5 mx-2 md:mx-4 bg-neutral-200 rounded-xl shadow-inner text-xs md:text-sm">
                <div class="text-center">
                    <span class="text-sm md:text-lg text-[#FF0000] font-extrabold border bg-neutral-400/20 border-neutral-400 px-3 py-0.5">累計PT消費数50%以上達成</span>
                </div>
                <div class="flex border-t border-b mt-5 relative h-5 md:h-6 px-2">
                    <span class="h-full rounded-l-md border border-neutral-800 px-4 bg-neutral-400/20 font-bold z-10 flex items-center">消費 PT</span>
                    <div class="flex-1 relative">
                        <div class="h-full bg-gray-50 rounded-r-xl overflow-hidden border-t border-b border-r border-neutral-800">
                            <div class="h-full bg-gradient-to-b from-[#01b4ef] via-[#00bffa] to-[#0488c3] w-[55%]"></div>
                        </div>
                        <div class="absolute left-[40%] z-10 h-[130%] top-[-15%]">
                            <img src="images/mark_red.svg" class="h-full mx-[-50%]"/>
                        </div>
                    </div>
                </div>
                <div class="w-full flex justify-end my-3 px-2">
                    <div class="w-fit flex px-3 py-0.5 items-center bg-neutral-400/20 border border-neutral-400 rounded-full">
                        <img src="images/mark_red_icon.svg" class="w-4 md:w-5" />
                        <span class="text-xs md:text-sm">ランク維持達成</span>
                        <img src="images/mark_black_icon.svg" class="w-4 md:w-5 ml-3" />
                        <span class="text-xs md:text-sm">ランク維持未達成</span>
                    </div>
                </div>
                <div class="flex font-bold items-center">
                    <div class="flex-1">
                        <div class="text-center">当月</div>
                        <img :src="ranks[ranks.length - 2].badge" class="p-1 w-full max-w-48 mx-auto"/>
                    </div>
                    <div class="px-1 pt-4">
                        <PlayIcon class="w-5 h-5 text-black "></PlayIcon>
                    </div>
                    <div class="flex-1">
                        <div class="text-center">翌月</div>
                        <img :src="ranks[ranks.length - 2].badge" class="p-1 w-full max-w-48 mx-auto"/>
                    </div>
                </div>
                <div class="text-center mt-6">
                    <span class="text-sm md:text-lg text-black font-extrabold border bg-neutral-400/20 border-neutral-400 px-3 py-0.5">累計PT消費数50%未満</span>
                </div>
                <div class="flex border-t border-b mt-5 relative h-5 md:h-6 px-2">
                    <span class="h-full rounded-l-md border border-neutral-800 px-4 bg-neutral-400/20 font-bold z-10 flex items-center">消費 PT</span>
                    <div class="flex-1 relative">
                        <div class="h-full bg-gray-50 rounded-r-xl overflow-hidden border-t border-b border-r border-neutral-800">
                            <div class="h-full bg-gradient-to-b from-[#01b4ef] via-[#00bffa] to-[#0488c3] w-[25%]"></div>
                        </div>
                        <div class="absolute left-[40%] z-10 h-[130%] top-[-15%]">
                            <img src="images/mark_black.svg" class="h-full mx-[-50%]"/>
                        </div>
                    </div>
                </div>
                <div class="w-full flex justify-end my-3 px-2">
                    <div class="w-fit flex px-3 py-0.5 items-center bg-neutral-400/20 border border-neutral-400 rounded-full">
                        <img src="images/mark_red_icon.svg" class="w-4 md:w-5" />
                        <span class="text-xs md:text-sm">ランク維持達成</span>
                        <img src="images/mark_black_icon.svg" class="w-4 md:w-5 ml-3" />
                        <span class="text-xs md:text-sm">ランク維持未達成</span>
                    </div>
                </div>
                <div class="flex font-bold items-center">
                    <div class="flex-1">
                        <div class="text-center">当月</div>
                        <img :src="ranks[ranks.length - 2].badge" class="p-1 w-full max-w-48 mx-auto"/>
                    </div>
                    <div class="px-1 pt-4">
                        <PlayIcon class="w-5 h-5 text-black "></PlayIcon>
                    </div>
                    <div class="flex-1">
                        <div class="text-center">翌月</div>
                        <img :src="ranks[ranks.length - 1].badge" class="p-1 w-full max-w-48 mx-auto"/>
                    </div>
                </div>
            </div> -->

            <hr class="mt-5 md:mt-8 mb-8 md:mb-10 mx-4 md:mx-6 border-neutral-300 border" />

            <p class="mb-6 px-4 font-bold align-middle text-sm md:text-lg"><span class="text-[0.6em]">■</span> ランク特典</p>

            <div class="w-full flex items-center mb-[-0.4em] text-[0.7em] sm:text-[1em]">
                <div class="w-[30%] text-center font-bold text-[1.1em]">ランク</div>
                <div class="w-[30%] text-center font-bold text-[0.9em]">購入時PT付与特典</div>
                <div class="w-[16%] text-center font-bold text-[0.8em]">EP付与率</div>
                <div class="w-[24%] text-center font-bold text-[0.8em] leading-[1.2em] sm:leading-[1.5em]">
                    ランクアップ<br />ボーナスPT</div>
            </div>
            <div v-for="(item, key) in ranks" class="my-[0.8em] md:my-[1.1em] text-[0.7em] sm:text-[1em]" :key="key">
                <div class="flex flex-wrap border-neutral-200 items-center gap-[0.8em]">
                    <div class="flex-1 flex bg-neutral-200 rounded-lg justify-center relative">
                        <div class="w-full flex items-center">
                            <div class="text-center text-[0.8em] font-bold text-white w-[30%] h-full flex items-center justify-center">
                                <img :src="item.image" class="h-[15vw] max-h-[90px] -my-1" :class="{'py-1.5': key > 4}"/>
                                <div class="text-center flex gap-[0.4em] absolute items-end h-full">
                                    <div class="relative flex justify-center text-[0.9em] drop-shadow-xl">
                                        <span class="leading-[1.2em]" :style="{ WebkitTextStrokeColor: 'black', WebkitTextStrokeWidth: '2px' }">( {{ item.title }} )</span>
                                        <span class="absolute leading-[1.2em]">( {{ item.title }} )</span>
                                    </div>
                                </div>
                            </div>
                            <div class="h-full w-[0.3em] bg-white"></div>
                            <div class="text-center text-[1em] text-black font-black w-[30%] h-full flex flex-col items-center justify-center relative">
                                <span class="leading-[1.4em]" v-html="pt_rate_string(item)" :style="{ WebkitTextStrokeColor: 'white', WebkitTextStrokeWidth: '2px' }"></span>
                                <span class="absolute leading-[1.4em]" v-html="pt_rate_string(item)"></span>
                            </div>
                            <div class="h-full w-[0.3em] bg-white"></div>
                            <div class="w-[40%] flex divide-x-[0.3em] divide-white h-full relative">
                                <div class="text-center text-[1em] text-black font-black w-[40%] h-full flex items-center justify-center">
                                    <span v-html="item.dp_rate > 0 ? item.dp_rate + '%' : `<span class='text-[1.2em]'>???</span>`" :style="{ WebkitTextStrokeColor: 'white', WebkitTextStrokeWidth: '2px' }"></span>
                                    <span class="absolute" v-html="item.dp_rate > 0 ? item.dp_rate + '%' : `<span class='text-[1.2em]'>???</span>`"></span>
                                </div>
                                <div class="text-center text-[1em] text-black font-black w-[60%] h-full flex items-center justify-center relative">
                                    <span class="leading-[1.4em]" v-html="bonus_pt_string(item)" :style="{ WebkitTextStrokeColor: 'white', WebkitTextStrokeWidth: '2px' }"></span>
                                    <span class="absolute leading-[1.4em]" v-html="bonus_pt_string(item)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-4 px-2 md:px-8">
                <p class="font-bold text-sm md:text-lg">【購入時PT特典について】</p>
                <p class="text-xs md:text-sm px-2 py-2">
                    購入する金額ごとのボーナスPTです。<br />
                    購入時点のランクに応じて付与されます。
                </p>
                <p class="font-bold text-sm md:text-lg pt-2">
                    【ランクアップボーナスPTについて】
                </p>
                <p class="text-xs md:text-sm  px-2 py-2">
                    ランク昇格時にボーナスPTが付与されます。<br />
                    ※昇格時ボーナスは自動で加算されます。
                </p>
                <p class="font-bold text-sm md:text-lg pt-2">
                    【ランク限定オリパ解放について】
                </p>
                <p class="text-xs md:text-sm  px-2 py-2">
                    各ランク毎に限定オリパが解放されます。
                </p>

                <hr class="my-4 mx-2 border-neutral-300 border" />
                
                <p class="px-2 font-bold text-sm md:text-lg"><span class="text-[0.6em]">■</span> 注意事項</p>
                <p class="text-xs md:text-sm  px-2 py-2">
                    ログインオリパ、一部限定ガチャなどはランクポイントおよびガチャランキングの増減対象にはなりません。<br/>
                    会員ランクの昇格条件、 維持条件、 昇格条件、 特典内容は予告なく変更となる場合がございます。 あらかじめご了承ください。<br/>
                    ランク昇格時にゲージは初期の位置に戻ります。
                </p>

            </div>

        </div>

    </AppLayout>
</template>

<script>
import { Head, useForm, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { PlayIcon } from '@heroicons/vue/24/solid'

export default {
    components: { Head, AppLayout, PlayIcon },
    props: {
        ranks: Object,
        mark_pos: Number,
        current_pos: Number,
        succeed: Boolean,
    },
    data() {
        return {
            user: null,
            current_rank: null,
        }
    },

    methods: {
        pt_rate_string(item) {
            if (item.limit < 0 || item.pt_rate <= 0) return '<span class="text-[1.2em]">???</span>'
            return `${item.pt_rate}%付与<br/>(購入時PTの)`
        },
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
        bonus_pt_string(item) {
            if (item.limit < 0 || item.pt_rate <= 0) return '<span class="text-[1.2em]">???</span>'
            return `${this.format_number(item.bonus)}PT`
        },
    },
    mounted() {
        this.user = usePage().props.value.auth.user;
        this.current_rank = this.ranks.filter(rank => {
            return rank.rank == this.user.current_rank;
        })[0];
    },
}
</script>