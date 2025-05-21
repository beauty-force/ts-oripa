<template>

    <Head title="ユーザー管理" />

    <AppLayout>

        <div class="md:px-4 px-2">
            <div class="border-b w-full p-2 my-2 font-semibold flex justify-between">
                <h3>ユーザー詳細 (ID-{{ user.id }})</h3>
                <button onclick="history.back()"
                    class="rounded-md px-2 text-sm font-normal text-white bg-red-500 hover:bg-red-400">
                    戻る
                </button>
            </div>

            <table class="w-full text-sm">
                <template v-if="user">
                    <tr>
                        <td class="p-1 w-2/5 font-bold">メールアドレス</td>
                        <td class="p-1 w-3/5">{{ user.email }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">電話番号</td>
                        <td class="p-1 w-3/5">{{ user.phone }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">ポイント</td>
                        <td class="p-1 w-3/5">
                            <input
                                class="bg-white/90 w-full px-3 py-2 border border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md placeholder-neutral-400"
                                type="number" v-model="form.point" />
                            <div class="flex gap-2 mt-2">
                                <button type="button" @click="add_point(500)" class="px-4 py-2 bg-gradient-to-r from-green-400 to-blue-500 text-white rounded-md shadow-md hover:from-green-500 hover:to-blue-600 transition duration-300 ease-in-out transform hover:scale-105">
                                    +500 PT
                                </button>
                                <button type="button" @click="add_point(1000)" class="px-4 py-2 bg-gradient-to-r from-purple-400 to-pink-500 text-white rounded-md shadow-md hover:from-purple-500 hover:to-pink-600 transition duration-300 ease-in-out transform hover:scale-105">
                                    +1000 PT
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">EP</td>
                        <td class="p-1 w-3/5">{{ user.dp }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">消費PT</td>
                        <td class="p-1 w-3/5">{{ user.consume_point }}</td>
                    </tr>
                </template>
                <template v-if="profile">
                    <tr>
                        <td class="p-1 w-2/5 font-bold">名前</td>
                        <td class="p-1 w-3/5">{{ profile.first_name }} {{ profile.last_name }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">名前(カナ)</td>
                        <td class="p-1 w-3/5">{{ profile.first_name_gana }} {{ profile.last_name_gana }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">郵便番号</td>
                        <td class="p-1 w-3/5">{{ profile.postal_code }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">都道府県</td>
                        <td class="p-1 w-3/5">{{ profile.prefecture }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">住所</td>
                        <td class="p-1 w-3/5">{{ profile.address }}</td>
                    </tr>

                    <tr>
                        <td class="p-1 w-2/5 font-bold">ランク</td>
                        <td class="p-1 w-3/5">{{ rank }}</td>
                    </tr>

                    <tr>
                        <td class="p-1 w-2/5 font-bold">招待コード</td>
                        <td class="p-1 w-3/5">{{ user.invite_code }}</td>
                    </tr>
                    <tr v-if="user.type != 1">
                        <td class="p-1 w-2/5 font-bold">アカウント承認</td>

                        <td class="p-1 w-3/5">
                            <button v-if="user.status" @click="updateStatus(user.id, 0)" class="px-2 py-1 rounded-md border text-rose-500 hover:bg-rose-100">削除</button>
                            <button v-else @click="updateStatus(user.id, 1)" class="px-2 py-1 rounded-md border text-sky-500 hover:bg-sky-100">承認</button>
                        </td>
                    </tr>
                </template>
            </table>
            <div class="my-6 text-center border border-sky-500 rounded-full p-0.5 w-fit mx-auto text-sm">
                <button type="button" @click="submit" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="inline-block items-center px-10 py-1 rounded-full font-semibold text-white uppercase bg-theme bg-theme-hover">
                    保存
                </button>
            </div>

            <div class="border-b w-full p-2 my-2 font-semibold flex justify-between">
                <h3>入金履歴</h3>
                <PaginationClientSide :callback="onPageChange1" :total="totalPage1"/>
            </div>

            <div class="w-full flex flex-col gap-4 mb-8 text-sm md:text-base">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border border-collapse divide-x-2">
                            <th class="text-center py-2">金額</th>
                            <th class="text-center py-2">ポイント</th>
                            <th class="text-center py-2">利用クーポン</th>
                            <th class="text-center py-2">購入時間</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="payment in paginatedPayments" class="border divide-x-2">
                            <td class="text-center py-2">{{ payment.amount.toLocaleString('jp-JP', { style: 'currency', currency: 'JPY'}) }}</td>
                            <td class="text-center py-2">{{ payment.point.toLocaleString() }} pt</td>
                            <td class="text-center py-2">{{ payment.coupon_name ?? '-' }}</td>
                            <td class="text-center py-2">{{ payment.updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-b w-full p-2 my-2 font-semibold flex justify-between">
                <h3>クーポン使用履歴</h3>
                <PaginationClientSide :callback="onPageChange2" :total="totalPage2"/>
            </div>

            <div class="w-full flex flex-col gap-4 mb-8 text-sm md:text-base">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border border-collapse divide-x-2">
                            <th class="text-center py-2">名前</th>
                            <th class="text-center py-2">コード</th>
                            <th class="text-center py-2">ポイント</th>
                            <th class="text-center py-2">使用時間</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="coupon in paginatedCoupons" class="border divide-x-2">
                            <td class="text-center py-2">{{ coupon.title }}</td>
                            <td class="text-center py-2">{{ format_number(coupon.code) }}</td>
                            <td class="text-center py-2">{{ format_number(coupon.point) }} pt</td>
                            <td class="text-center py-2">{{ coupon.updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-b w-full p-2 my-2 font-semibold flex justify-between items-center">
                <h3>ポイント履歴</h3>
                <PaginationClientSide :callback="onPageChange" :total="totalPage"/>
                <button @click="clear_history"
                    class="rounded-md px-2 py-1 text-sm font-normal text-white bg-red-500 hover:bg-red-400">
                    履歴削除
                </button>
            </div>

            <div class="w-full flex flex-col gap-4 mb-8 text-sm md:text-base overflow-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="border border-collapse divide-x-2">
                            <th class="text-center py-2">事由</th>
                            <th class="text-center py-2">ポイント（利用）</th>
                            <th class="text-center py-2">次のポイント（PT残高）</th>
                            <th class="text-center py-2">日時</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="log in paginatedLogs" class="border divide-x-2">
                            <td class="text-center py-2">{{ log.point_type }}</td>
                            <td class="text-center py-2">{{ log.point_diff }}</td>
                            <td class="text-center py-2">{{ log.point_before + log.point_diff }}</td>
                            <td class="text-center py-2">{{ log.updated_at1 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import PaginationClientSide from '@/Parts/PaginationClientSide.vue';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: { Head, AppLayout, Link, PaginationClientSide },
    props: {
        errors: Object,
        user: Object,
        profile: Object,
        payments: Object,
        coupons: Object,
        point_logs: Object,
        show_limit: Number,
        rank: String,
    },
    mounted() {
    },
    methods: {
        submit() {
            this.form.post(route('admin.users.update'), {
                preserveScroll: true,
                onFinish: () => { },
            });
        },
        clear_history() {
            confirm("ポイント履歴を削除しますか？ 期間ランクデータが初期化されます。").then((result) => {
                if (result) {
                    this.clear_form.post(route('admin.users.clear_history'), {
                        preserveScroll: true,
                        onFinish: () => { },
                    });
                }
            });
        },
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
        onPageChange(page) {
            this.current_page = page;
        },
        onPageChange1(page) {
            this.current_page1 = page;
        },
        onPageChange2(page) {
            this.current_page2 = page;
        },
        add_point(point) {
            useForm({ id: this.user.id, point }).post(route('admin.users.add_point'), {
                preserveScroll: true,
                onFinish: () => { 
                    this.form.point = this.user.point;
                },
            });
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
    },
    data() {
        return {
            current_page : 1,
            current_page1 : 1,
            current_page2 : 1,
        }
    },
    setup(props) {
        const form = useForm({
            id: props.user.id,
            point: props.user.point
        });

        const clear_form = useForm({
            id: props.user.id,
        });

        const show_limit1 = 10;
        const totalPage = Math.floor((props.point_logs.length + props.show_limit - 1) / props.show_limit);
        const totalPage1 = Math.floor((props.payments.length + show_limit1 - 1) / show_limit1);
        const totalPage2 = Math.floor((props.coupons.length + show_limit1 - 1) / show_limit1);
        return { form, totalPage, clear_form, totalPage1, totalPage2, show_limit1 }
    },
    computed: {
        paginatedLogs() {
            const start = (this.current_page - 1) * this.show_limit;
            const end = this.current_page * this.show_limit;
            return this.point_logs.slice(start, end);
        },
        paginatedPayments() {
            const start = (this.current_page1 - 1) * this.show_limit1;
            const end = this.current_page1 * this.show_limit1;
            return this.payments.slice(start, end);
        },
        paginatedCoupons() {
            const start = (this.current_page2 - 1) * this.show_limit1;
            const end = this.current_page2 * this.show_limit1;
            return this.coupons.slice(start, end);
        },
    }
}
</script>