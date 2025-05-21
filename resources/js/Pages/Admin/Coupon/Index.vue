<template>
    <Head title="クーポン発行" />

    <AppLayout>

        <div class="md:px-4 px-2">
            <div class="border-b w-full px-2 pt-4 pb-3 mb-3 font-semibold">
                <h3>クーポン発行</h3>
            </div>
            <Link :href="route('admin.coupon.create')" class="rounded float-right px-8 py-2 bg-teal-600 hover:bg-teal-700 text-neutral-50">
                新規追加
            </Link>
            <div class="w-full mt-16 overflow-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border-b border-collapse text-nowrap font-bold text-base">
                            <th class="text-center px-1 py-2">名前</th>
                            <!-- <th class="text-center px-1 py-2">種類</th> -->
                            <th class="text-center px-1 py-2">コード</th>
                            <th class="text-center px-1 py-2">ポイント</th>
                            <th class="text-center px-1 py-2">有効期限</th>
                            <th class="text-center px-1 py-2">枚数</th>
                            <th class="text-center px-1 py-2">一人当上限</th>
                            <th class="text-center px-1 py-2">使用回数</th>
                            <th class="text-center px-1 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="coupon in coupons" class="border-b border-collapse">
                            <td class="text-center px-1 py-2">{{ coupon.title }}</td>
                            <!-- <td class="text-center px-1 py-2">{{ types[coupon.type] }}</td> -->
                            <td class="text-center px-1 py-2 text-red-600">{{ coupon.code }}</td>
                            <td class="text-center px-1 py-2" v-if="coupon.type == 'NORMAL'">{{ coupon.point + 'pt' }}</td>
                            <td class="text-center px-1 py-2" v-else >{{ '割引率: ' + coupon.min_rate + '~' + coupon.max_rate + '%' }}</td>
                            <td class="text-center px-1 py-2">{{ coupon.expiration }}</td>
                            <td class="text-center px-1 py-2">{{ coupon.count }}</td>
                            <td class="text-center px-1 py-2">{{ coupon.user_limit }}</td>
                            <td class="text-center px-1 py-2">{{ coupon.used_count }}</td>
                            <td>
                                <div class="flex justify-center items-center py-2">
                                    <Link :href="route('admin.coupon.edit', coupon.id)" class="rounded float-right px-3 py-1 mr-2 text-sm bg-cyan-600 hover:bg-cyan-700 text-neutral-50 text-nowrap">
                                        編集
                                    </Link>
                                    <button @click="delete_coupon(coupon.id)" class="rounded float-right px-3 py-1 text-sm bg-neutral-600 hover:bg-neutral-700 text-neutral-50 text-nowrap">
                                        削除
                                    </button> 

                                </div>
                            </td>
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
import { Inertia } from '@inertiajs/inertia';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: {Head, AppLayout, Link},
    props: {
        coupons: Object,
        types: Object
    },
    mounted() {
        console.log(this.coupons);
    },
    methods: {
        delete_coupon(id) {
            confirm("削除してもいいですか？", '', 'error').then((result) => {
                if (result) {
                    Inertia.delete(route('admin.coupon.delete', {id:id}));
                }
            });
        },
    }
}
</script>