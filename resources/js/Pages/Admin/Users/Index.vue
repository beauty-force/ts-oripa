<template>

    <Head title="ユーザー管理" />

    <AppLayout>

        <div class="md:px-4 px-2">
            <div class="border-b w-full p-2 my-2 font-semibold flex justify-between flex-wrap items-center gap-3">
                <h3>ユーザー管理</h3>
                <span class="font-normal flex gap-2">合計ポイント: <span class="font-bold">{{ format_number(total_point) }}</span>PT</span>
                <a :href="route('admin.users.export')" 
                    class="rounded-md border bg-green-600 text-white px-4 py-1 hover:bg-green-700 text-sm">CSVエクスポート</a>
            </div>
            <div class="w-full flex flex-col gap-4 mb-8">
                <div class="w-full flex justify-between gap-4">
                    <div class="flex-1 flex flex-wrap gap-2">
                        <input v-model="form_search.keyword" type="text"
                            class="text-sm flex-1 md:min-w-96 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                            placeholder="ユーザー名、メールアドレス、電話番号を入力します。" />
                        <select v-model="form_search.order_by" id="order_by"
                        class="bg-white/90 flex-1 px-3 py-2 border border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md placeholder-neutral-400">
                            <option value="amount">購入金額</option>
                            <option value="rank">ランク</option>
                            <option value="status">状態</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="search"
                            class="rounded-md border bg-neutral-600 text-white px-4 py-1">検索</button>
                        
                    </div>
                </div>

                <div class="w-full overflow-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-collapse border-sky-100 whitespace-nowrap">
                                <th class="text-center py-2">詳細</th>
                                <th class="text-center py-2">ユーザー名</th>
                                <th class="text-center py-2">購入金額</th>
                                <th class="text-center py-2">ランク</th>
                                <!-- <th class="text-center py-2">line連携</th> -->
                                <th class="text-center py-2"></th>
                                <!-- <th class="text-center py-2">ポイント</th>
                                <th class="text-center py-2">履歴</th> -->
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-for="user in users"
                                class="border border-sky-100 divide-x-2 divide-sky-50 border-collapse">
                                <td class="text-center py-2 px-1">
                                    <Link class="px-2 py-0.5 border border-sky-300 rounded-lg hover:bg-sky-300 text-nowrap"
                                        :href="route('admin.users.detail', { id: user.id })">詳細</Link>
                                </td>
                                <td class="text-center py-2 px-1 text-red-600 text-nowrap">{{ user.name }}</td>
                                <td class="text-center py-2 px-1 max-w-full overflow-hidden whitespace-nowrap text-ellipsis">{{
                                    format_number(user.amount) }}</td>
                                <td class="text-center py-2 px-1 max-w-full overflow-hidden whitespace-nowrap text-ellipsis">{{
                                    user.rank }}</td>
                                <!-- <td class="text-center py-2 px-1">{{ user.line_id ? '〇' : '✖' }}</td> -->
                                <td class="text-center py-1 whitespace-nowrap">
                                    <button v-if="user.status" @click="updateStatus(user.id, 0)" class="px-2 py-1 rounded-md border text-rose-500 hover:bg-rose-100">削除</button>
                                    <button v-else @click="updateStatus(user.id, 1)" class="px-2 py-1 rounded-md border text-sky-500 hover:bg-sky-100">承認</button>
                                </td>
                                <!-- <td class="text-center py-2 px-1">{{ user.point }} pt</td>
                                <td>
                                    <div class="flex justify-center items-center py-2">
                                        <Link :href="route('admin.users.purchase_log', user.id)" class="rounded float-right px-3 py-1 mr-2 text-sm bg-cyan-600 hover:bg-cyan-700 text-neutral-50">
                                            購入
                                        </Link>
                                        <Link :href="route('admin.users.gacha_log', user.id)" class="rounded float-right px-3 py-1 text-sm bg-red-500 hover:bg-red-700 text-neutral-50">
                                            ガチャ
                                        </Link>

                                    </div>
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :search_cond="search_cond" route_name="admin.users" :total="total"></Pagination>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import Pagination from '@/Parts/Pagination.vue';

import GachaCard from '@/Parts/GachaCard.vue';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: { Head, AppLayout, Link, GachaCard, Pagination },
    props: {
        errors: Object,
        users: Object,
        search_cond: Object,
        total: Number,
        total_point: Number,
    },
    mounted() {
    },
    methods: {
        search() {
            this.form_search.get(route('admin.users'));
        },
        updateStatus(id, status) {
            if (status == 0) {
                confirm("アカウントをブロックしますか？", "獲得した商品と発送依頼中の商品は全て回収されます。", "warning").then((result) => {
                    if (result) {
                        useForm({id, status}).post(route('admin.users.update'));
                    }
                });
            } else {
                confirm("アカウントを承認しますか？").then((result) => {
                    if (result) {
                        useForm({id, status}).post(route('admin.users.update'));
                    }
                });
            }
        },
        format_number(n) {
            if (n == null) return 0;
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        },
    },
    setup(props) {
        const form_search = useForm(props.search_cond);
        
        return { form_search }
    },
}
</script>