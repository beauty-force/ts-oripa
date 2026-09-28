<template>

    <Head title="ユーザー管理" />

    <AppLayout>

        <div class="md:px-4 px-2">
            <div class="border-b w-full p-2 my-2 font-semibold flex justify-between flex-wrap items-start gap-3 text-xs sm:text-sm md:text-base">
                <div class="flex gap-1 flex-col">
                    <span class="font-normal flex gap-1">合計ポイント: <span class="font-bold">{{ total_point.toLocaleString() }}</span>PT</span>
                    <span class="font-normal flex gap-1">合計購入金額: <span class="font-bold">{{ total_purchase.toLocaleString() }}</span>円</span>
                </div>
                <span class="font-normal flex gap-1">招待コードの総使用回数: <span class="font-bold">{{ total_invite.toLocaleString() }}</span>回</span>
                <!-- <a :href="route('admin.users.export')" 
                    class="rounded-md border bg-green-600 text-white px-4 py-1 hover:bg-green-700 text-sm">CSVエクスポート</a> -->
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
                            <option value="point">ポイント</option>
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
                                <th class="text-center py-2 px-2">
                                    <input type="checkbox" v-model="selectAll" @change="toggleSelectAll"
                                        class="rounded border-neutral-300 text-sky-600 shadow-sm focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50" />
                                </th>
                                <th class="text-center py-2">ユーザー名</th>
                                <th class="text-center py-2">購入金額</th>
                                <!-- <th class="text-center py-2">ランク</th> -->
                                <th class="text-center py-2">ポイント</th>
                                <!-- <th class="text-center py-2">line連携</th> -->
                                <th class="text-center py-2 w-24 sm:w-28">
                                    <button type="button" @click="deleteSelectedUsers" :disabled="selectedUsers.length === 0"
                                        class="rounded-md border px-4 py-1 transition-colors text-sm"
                                        :class="selectedUsers.length > 0 ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                        削除 <span v-if="selectedUsers.length > 0">({{ selectedUsers.length }})</span>
                                    </button>
                                </th>
                                <!-- <th class="text-center py-2">履歴</th> -->
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-for="user in users"
                                class="border border-sky-100 divide-x-2 divide-sky-50 border-collapse">
                                <td class="text-center py-2 px-2">
                                    <input type="checkbox" :value="user.id" v-model="selectedUsers"
                                        :disabled="user.status === 0"
                                        class="rounded border-neutral-300 text-sky-600 shadow-sm focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 disabled:opacity-40 disabled:cursor-not-allowed" />
                                </td>
                                
                                <td class="text-center py-2 px-1 text-red-600 text-nowrap">
                                    <Link class="text-base"
                                        :href="route('admin.users.detail', { id: user.id })">
                                        {{ user.name }}
                                    </Link>
                                </td>
                                <td class="text-center py-2 px-1 max-w-full overflow-hidden whitespace-nowrap text-ellipsis">{{
                                    format_number(user.amount) }}</td>
                                <td class="text-center py-2 px-1 max-w-full overflow-hidden whitespace-nowrap text-ellipsis">
                                    {{ user.point.toLocaleString() }} pt</td>
                                <!-- <td class="text-center py-2 px-1">{{ user.line_id ? '〇' : '✖' }}</td> -->
                                <td class="text-center py-1 whitespace-nowrap">
                                    <button v-if="user.status" @click="updateStatus(user.id, 0)" class="px-2 py-1 rounded-md border text-rose-500 hover:text-white hover:bg-rose-400">
                                        <TrashIcon class="w-5 h-5" />
                                    </button>
                                    <button v-else @click="updateStatus(user.id, 1)" class="px-2 py-1 rounded-md border text-sky-500 hover:text-white hover:bg-sky-400">
                                        <CheckCircleIcon class="w-5 h-5" />
                                    </button>
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
import { Inertia } from '@inertiajs/inertia';
import { useConfirm } from "@/composables/useConfirm";
import { EyeIcon, TrashIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const { confirm } = useConfirm();

export default {
    components: { Head, AppLayout, Link, Pagination, EyeIcon, TrashIcon, CheckCircleIcon },
    props: {
        errors: Object,
        users: Object,
        search_cond: Object,
        total: Number,
        total_point: Number,
        total_purchase: Number,
        total_invite: Number,
    },
    data() {
        return {
            selectedUsers: [],
            selectAll: false
        }
    },
    mounted() {
    },
    methods: {
        search() {
            this.form_search.get(route('admin.users'));
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedUsers = this.users.filter(user => user.status !== 0).map(user => user.id);
            } else {
                this.selectedUsers = [];
            }
        },
        deleteSelectedUsers() {
            if (this.selectedUsers.length === 0) return;
            
            const count = this.selectedUsers.length;
            confirm(
                `選択した${count}人のユーザーを削除しますか？`, 
                "獲得した商品と発送依頼中の商品は全て回収されます。この操作は取り消せません。", 
                "warning"
            ).then((result) => {
                if (result) {
                    useForm({ user_ids: this.selectedUsers, status: 0 }).post(route('admin.users.update'), {
                        onSuccess: () => {
                            this.selectedUsers = [];
                            this.selectAll = false;
                        }
                    });
                }
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
        format_number(n) {
            if (n == null) return 0;
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        },
    },
    watch: {
        selectedUsers(newVal) {
            const selectableUsers = this.users.filter(user => user.status !== 0);
            this.selectAll = newVal.length === selectableUsers.length && selectableUsers.length > 0;
        }
    },
    setup(props) {
        const form_search = useForm(props.search_cond);
        
        return { form_search }
    },
}
</script>