<template>
    <Head title="ポイント設定" />

    <AppLayout>
        <div class="px-3 fixed w-full bg-white pt-3">
            <div class="w-full text-neutral-400 mb-3">
                <Link href="/admin/bank_payments?status=0" class="inline-block md:px-8 px-4 py-1.5" :class="{'bg-blue-900 text-white': status == 0}">
                    未振込
                </Link>
                <Link href="/admin/bank_payments?status=1" class="inline-block md:px-8 px-4 py-1.5" :class="{'bg-blue-900 text-white': status == 1}">
                    振込済み
                </Link>
            </div>
        </div>
        <div class="mx-2 pt-[60px]">
            <div class=" overflow-auto w-full">
                <table class="border-collapse table-auto w-full text-sm ">
                    <thead>
                        <tr class="border-b dark:border-slate-600 font-medium">
                            <th class="border p-3 text-left">ID</th>
                            <th class="border p-3 text-left">UserID</th>
                            <th class="border p-3 text-left">口座名</th>
                            <th class="border p-3 text-left">銀行名</th>
                            <th class="border p-3 text-left">Point</th>
                            <th class="border p-3 text-left">金額</th>
                            <th class="border p-3 text-left">割引率</th>
                            <th class="border p-3 text-left">日付</th>
                            <th class="border p-3 text-left">確認</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800">
                        <tr v-for="(p, key) in _payments" class="border-b border-slate-100 text-slate-500">
                            <td class="px-3 py-2 border">{{ key + 1 }}</td>
                            <td class="px-3 py-2 border">{{ p.user_id }}</td>
                            <td class="px-3 py-2 border">{{ p.account }}</td>
                            <td class="px-3 py-2 border">{{ p.bank_name }}</td>
                            <td class="px-3 py-2 border">{{ p.point * (100 + p.pt_rate) / 100 }}</td>
                            <td class="px-3 py-2 border">{{ p.amount * (100 - p.discount_rate) / 100 }}</td>
                            <td class="px-3 py-2 border">{{ p.discount_rate }}%</td>
                            <td class="px-3 py-2 border">{{ p.updated_at }}</td>
                            <td class="px-3 py-2 border"><button class="border-b" @click="Confirm(p.id)">
                                <span v-if="status==0">確認</span>
                                <span v-else>削除</span>
                            </button></td>
                        </tr> 
                    </tbody>
                </table>
            </div>

        </div>
    
    </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import axios from 'axios';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: {Head, AppLayout, Link},
    props: {
        payments: Object,
        status: Number
    },
    data() {
        return {
            _payments : this.payments
        };
    },
    methods : {
        Confirm(id) {
            confirm('本当に大丈夫ですか？', '', 'question').then((result) => {
                if (result) {
                    axios.post(
                        route("admin.update_bank_payment"),
                        {
                            id,
                            status : this.status
                        }
                    ).then((data) => {
                        if (data.data.result == 'success') {
                            this._payments = this._payments.filter(p => p.id != id);
                        }
                    });
                }
            });
        },
    },
}
</script>