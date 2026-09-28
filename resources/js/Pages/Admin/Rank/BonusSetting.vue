<template>

    <Head title="ランキング報酬管理" />

    <AppLayout>

        <div class="h-full flex flex-col px-4 md:px-8 pb-10 md:items-center text-sm md:text-base">
            <div class="pt-4 pb-4">
                <h1 class="md:text-center md:text-lg font-bold">ランキング報酬管理</h1>
            </div>
            <hr class="w-full mb-2"/>

            <div class="w-full flex flex-wrap justify-between items-center">
                <h1 class="px-2 py-2 text-base font-bold">日次ランキング</h1>
            </div>
            
            <table class="w-full">
                <thead>
                    <th class="border px-1 py-1">ランク</th>
                    <!-- <th class="border px-1 py-1">種類</th> -->
                    <th class="border px-1 py-1">ボーナス</th>
                </thead>
                <tbody class="leading-8">
                    <tr v-for="(item, index) in daily_bonus">
                        <td class="w-1/4">
                            <input type="text" class="w-full border-sky-200 h-8" v-model="item.rank" required />
                        </td>
                        <!-- <td class="">
                            <select v-model="item.type" class="h-8 py-0 w-full border-sky-200">
                                <option value="point" class="py-0">ポイント</option>
                                <option value="product" class="py-0">景品</option>
                            </select>
                        </td> -->
                        <td class=" font-bold">
                            <input type="text" class="w-full border-sky-200 h-8" v-model="item.value" />
                        </td>
                        <td>
                            <XMarkIcon class="h-5 w-5 mx-1" @click="daily_bonus.splice(index, 1)"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <button class="my-2 px-6 py-1 rounded-md bg-neutral-600 hover:bg-neutral-500 text-white text-sm" @click="add_item('daily')">追加</button>

            <div class="pt-10 w-full flex flex-wrap justify-between items-center">
                <h1 class="px-2 py-2 text-base font-bold">週次ランキング</h1>
            </div>
            
            <table class="w-full">
                <thead>
                    <th class="border px-1 py-1">ランク</th>
                    <!-- <th class="border px-1 py-1">種類</th> -->
                    <th class="border px-1 py-1">ボーナス</th>
                </thead>
                <tbody class="text-sm leading-8">
                    <tr v-for="(item, index) in weekly_bonus">
                        <td class="w-1/4">
                            <input type="text" class="w-full border-sky-200 h-8" v-model="item.rank" required />
                        </td>
                        <!-- <td class="">
                            <select v-model="item.type" class="h-8 py-0 w-full border-sky-200">
                                <option value="point" class="py-0">ポイント</option>
                                <option value="product" class="py-0">景品</option>
                            </select>
                        </td> -->
                        <td class=" font-bold">
                            <input type="text" class="w-full border-sky-200 h-8" v-model="item.value" />
                        </td>
                        <td>
                            <XMarkIcon class="h-5 w-5 mx-1" @click="weekly_bonus.splice(index, 1)"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="my-2 px-6 py-1 rounded-md bg-neutral-600 hover:bg-neutral-500 text-white text-sm" @click="add_item('weekly')">追加</button>

            <div class="pt-10 w-full flex flex-wrap justify-between items-center">
                <h1 class="px-2 py-2 text-base font-bold">月次ランキング</h1>
            </div>
            <table class="w-full">
                <thead>
                    <th class="border px-1 py-1">ランク</th>
                    <!-- <th class="border px-1 py-1">種類</th> -->
                    <th class="border px-1 py-1">ボーナス</th>
                </thead>
                <tbody class="text-sm leading-8">    
                    <tr v-for="(item, index) in monthly_bonus">
                        <td class="w-1/4">
                            <input type="text" class="w-full border-sky-200 h-8" v-model="item.rank" required />
                        </td>
                        <!-- <td class="">
                            <select v-model="item.type" class="h-8 py-0 w-full border-sky-200">
                                <option value="point" class="py-0">ポイント</option>
                                <option value="product" class="py-0">景品</option>
                            </select>
                        </td> -->
                        <td class=" font-bold">
                            <input type="text" class="w-full border-sky-200 h-8" v-model="item.value" />
                        </td>
                        <td>
                            <XMarkIcon class="h-5 w-5 mx-1" @click="monthly_bonus.splice(index, 1)"/>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
            <button class="my-2 px-6 py-1 rounded-md bg-neutral-600 hover:bg-neutral-500 text-white text-sm" @click="add_item('monthly')">追加</button>
            
            <div class="sticky bottom-6 w-full flex gap-2 justify-center">
                <button class="mt-8 px-12 py-2 rounded-md bg-sky-600 hover:bg-sky-500 text-white text-sm" @click="save_bonus_setting">保存</button>
                <Link :href="route('ranking.about')" class="mt-8 px-12 py-2 rounded-md bg-rose-600 hover:bg-rose-500 text-white text-sm">戻る</Link>

            </div>
        </div>

    </AppLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import { XMarkIcon } from '@heroicons/vue/24/solid'

export default {
    components: { Head, AppLayout, Link, XMarkIcon },
    props: {
        bonus: Object,
    },
    data() {
        return {
            daily_bonus: [],
            weekly_bonus: [],
            monthly_bonus: [],
        }
    },
    mounted() {
        this.daily_bonus = this.bonus.filter(item=>item.period == 'daily');
        this.weekly_bonus = this.bonus.filter(item=>item.period == 'weekly');
        this.monthly_bonus = this.bonus.filter(item=>item.period == 'monthly');
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        },
        add_item(period) {
            let bonus = period == 'daily' ? this.daily_bonus : (period == 'weekly' ? this.weekly_bonus : this.monthly_bonus);
            let item = {
                period,
                type: 'point',
                value: '',
                rank: bonus.length + 1,
            }
            bonus.push(item);
        },
        save_bonus_setting() {
            const form = useForm({
                bonus: [
                    ...this.daily_bonus,
                    ...this.weekly_bonus,
                    ...this.monthly_bonus
                ],
            });
            form.post(route('admin.ranking.bonus_update'), {
                preserveScroll: true,
            });
        }
    },
    setup(props) {
        let form_data = {
            
        };
        const form = useForm( form_data );

        return { form }
    },
}
</script>