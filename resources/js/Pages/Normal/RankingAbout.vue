<template>

    <Head title="ランキング報酬について" />

    <AppLayout>

        <div class="h-full flex flex-col px-4 md:px-8 pb-10 md:items-center">
            <div class="pt-4 pb-4">
                <h1 class="md:text-center md:text-lg font-bold">ランキング報酬について</h1>
                <Link v-if="user && user.type == 1" :href="route('admin.ranking.bonus_edit')" class="text-sm border border-sky-500 rounded-full px-3 py-1 hover:text-white hover:bg-sky-500 absolute top-4 right-3">
                    報酬管理
                </Link>
                <Link v-else :href="route('ranking.index')" class="text-xs md:text-sm border border-sky-500 rounded-full px-3 py-1 hover:text-white hover:bg-sky-500 absolute top-4 right-3">
                    ガチャランキング
                </Link>
            </div>

            <div class="bg-sky-100 border-l-4 border-sky-500 p-4 mb-4 text-sm text-sky-700 w-full md:w-4/5">
                <p class="font-bold">⚠ ランキングについて</p>
                <p class="mt-2">1. ガチャを回した分だけランキングに反映！ 使用したコインの総額がそのままランキングスコアになります。上位に入れば、ガチャに使える豪華ポイントをGET！ ただし、一部対象外のガチャもあるのでご注意ください。</p>
                <p class="mt-2">2. 毎日0:00にランキング更新！ どこまで上がれるか？ ランキングバトルを制して頂点を目指せ！</p>
            </div>

            <hr class="w-full mb-2"/>

            <h1 class="md:w-96 px-2 py-2 text-base font-bold">日次ランキング</h1>
            <table class="w-full md:w-96">
                <tbody class="text-sm leading-8">
                    <tr v-for="item in daily_bonus">
                        <td class="border px-4">
                            {{ item.rank }}位
                        </td>
                        <td class="border px-4 font-bold">
                            {{ item.value }}
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-4">
                            報酬付与タイミング
                        </td>
                        <td class="border px-4">
                            翌日未明
                        </td>
                    </tr>
                </tbody>
            </table>

            <h1 class="md:w-96 pt-10 px-2 py-2 text-base font-bold">週次ランキング</h1>
            <table class="w-full md:w-96">
                <tbody class="text-sm leading-8">
                    <tr v-for="item in weekly_bonus">
                        <td class="border px-4">
                            {{ item.rank }}位
                        </td>
                        <td class="border px-4 font-bold">
                            {{ item.value }}
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-4">
                            報酬付与タイミング
                        </td>
                        <td class="border px-4">
                            翌月曜日未明
                        </td>
                    </tr>
                </tbody>
            </table>

            <h1 class="md:w-96 pt-10 px-2 py-2 text-base font-bold">月次ランキング</h1>
            <table class="w-full md:w-96">
                <tbody class="text-sm leading-8">
                    <tr v-for="item in monthly_bonus">
                        <td class="border px-4">
                            {{ item.rank }}位
                        </td>
                        <td class="border px-4 font-bold">
                            {{ item.value }}
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-4">
                            報酬付与タイミング
                        </td>
                        <td class="border px-4">
                            翌月未明
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

<script>
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';

export default {
    components: { Head, AppLayout, Link },
    props: {
        bonus: Object
    },
    data() {
        return {
            daily_bonus: [],
            weekly_bonus: [],
            monthly_bonus: [],
            user: null,
        }
    },
    mounted() {
        this.user = usePage().props.value.auth.user;
        this.daily_bonus = this.getRankedOutput(this.bonus.filter(item=>item.period == 'daily'));
        this.weekly_bonus = this.getRankedOutput(this.bonus.filter(item=>item.period == 'weekly'));
        this.monthly_bonus = this.getRankedOutput(this.bonus.filter(item=>item.period == 'monthly'));
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        },
        getRankedOutput(values) {
            const result = [];
            
            for (let i = 0; i < values.length; i++) {
                let start = values[i].rank;
                if (values[i].value == 0) continue;
                let end = start;
                if (i < values.length - 1) end = values[i + 1].rank - 1;
                let rank = start === end ? `${start}` : `${start} ~ ${end}`;
                result.push({
                    rank,
                    value: this.format_number(values[i].value) + ' coin'
                });
            }
            return result;
        }
    },
}
</script>