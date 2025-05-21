<template>

    <Head title="ガチャ" />

    <AppLayout>
        <template v-if="gacha_log.length == 0">
            <div class="w-full min-h-full">
                <div class="md:w-[680px] w-full mx-auto bg-white">
                    <div class="mx-auto relative">
                        <!-- <div class="flex justify-end p-6 absolute top-0 w-full">
                            <button
                                class="rounded-full py-0.5 w-fit px-4 text-sm border-2 border-white bg-white items-center flex align-center">
                                <template v-if="gacha.count_card > 0">
                                    <span class="text-sm">残&nbsp;&nbsp;</span>
                                    <span class="font-semibold text-base">{{ format_number(gacha.count_rest)
                                        }}</span>
                                    <span class="text-sm">/{{ format_number(gacha.count_card) }}</span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;

                                </template>
                                <img src="/images/icon_cash.png" class="h-4" />&nbsp;&nbsp;
                                <span class="font-semibold text-base">{{ format_number(gacha.point) }}</span>
                                <span>PT</span>
                            </button>
                        </div> -->
                        <img v-if="gacha.image != ''" :src="gacha.image" class="block w-full" />
                        <img v-if="gacha.image == ''" :src="gacha.thumbnail" class="block w-full md:w-4/5 mx-auto" />
                        
                        
                    </div>
                </div>
                
    
                <div class="w-full bg-neutral-700 sticky bottom-0 mt-4">

                    <div class="mx-auto md:max-w-md px-1 py-2 space-y-2">
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
                        <GachaButtons :gacha="{ ...gacha, code }" />
                    </div>
                </div>
            </div>

        </template>
        <div v-else class="w-full">
            <div class="flex flex-wrap justify-between items-center px-4 py-3 sticky top-[79px] bg-white">
                <h3 class="underline underline-offset-4 text-lg">排出履歴</h3>
                <Pagination :search_cond="{ ...search_cond, id: gacha.id }" route_name="user.gacha"
                    :total="total">
                </Pagination>
            </div>
            <div class="overflow-auto px-1">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-collapse text-nowrap">
                            <th class="text-center py-2 px-2">番号</th>
                            <th class="text-center py-2 px-2">名前</th>
                            <th class="text-center py-2 px-2">画像</th>
                            <th class="text-center py-2 px-2">レア度</th>
                            <template v-if="is_admin">
                                <th class="text-center py-2 px-2">ポイント</th>
                                <th class="text-center py-2 px-2">ユーザー</th>
                                <th class="text-center py-2 px-2">時間</th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(log, index) in gacha_log" class="border-b border-collapse text-sm">
                            <td class="text-center py-2 px-2">{{ log.gacha_record_id }}</td>
                            <td class="text-center py-2 px-2">{{ log.name }}</td>
                            <td class="text-center py-1 px-2 flex justify-center min-w-20 items-center">
                                <img class="w-16" :src="log.image" />
                            </td>
                            <td class="text-center py-2 px-2">{{ log.rare }}</td>
                            <template v-if="is_admin">
                                <td class="text-center py-2 px-2">{{ format_number(log.point) }}</td>
                                <td class="text-center py-2 px-2">{{ log.email }}</td>
                                <td class="text-center py-2 px-2">{{ log.gacha_time }}</td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <TransitionRoot :show="isDialogMessage" class="h-full fixed inset-0 z-40 top-0">
            <TransitionChild enter="transition ease-in-out duration-100 transform" enter-from="scale-50"
                enter-to="scale-100" leave="transition ease-in-out duration-200 transform" leave-from="opacity-100"
                leave-to="opacity-0" as="template">

                <div class="relative z-20 w-full mx-auto md:w-[768px] h-screen flex items-center"
                    @click="isDialogMessage = false">
                    <img :src="image" class="mx-auto max-h-[80%] max-w-[80%] w-full object-contain" />
                </div>

            </TransitionChild>
            <TransitionChild enter="transition-opacity ease-linear duration-100" enter-from="opacity-0"
                enter-to="opacity-100" leave="transition-opacity ease-linear duration-100" leave-from="opacity-100"
                leave-to="opacity-0" as="template">

                <div class="fixed inset-0 bg-white h-full" @click="isDialogMessage = false"></div>
            </TransitionChild>
        </TransitionRoot>
    </AppLayout>
</template>

<script>
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import Footer from '@/Parts/Footer.vue';
import GachaButtons from '@/Parts/GachaButtons.vue';
import Pagination from '@/Parts/Pagination.vue';
import { TransitionRoot, TransitionChild, Dialog, DialogOverlay } from '@headlessui/vue';
import { XMarkIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

export default {
    components: { Head, AppLayout, Footer, Link, GachaButtons, Pagination, TransitionRoot, TransitionChild, Dialog, DialogOverlay, XMarkIcon, ExclamationTriangleIcon },
    props: {
        errors: Object,
        gacha: Object,
        category_share: Object,
        gacha_log: Array,
        is_admin: Boolean,
        search_cond: Object,
        total: Number,
        code: String,
    },
    data() {
        return {
            products: [
                this.gacha.products.filter(item => { return item.rank == 1 }),
                this.gacha.products.filter(item => { return item.rank == 2 }),
                this.gacha.products.filter(item => { return item.rank == 3 }),
                this.gacha.products.filter(item => { return item.rank == 4 }),
            ],
            last_product: this.gacha.products.filter(item => { return item.is_last == 1 }),
            isDialogMessage: false,
            dialogMessage: "",
            image: "",
        }
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
        openImage(image) {
            this.image = image;
            this.isDialogMessage = true;
        }
    },
    computed : {
        progress_value() {
            return Math.round(this.gacha.count_rest * 100.0 / this.gacha.count_card) + '%'
        },
        progress_background_width() {
            if (this.gacha.count_rest == 0) return '100%';
            return Math.round(this.gacha.count_card * 100.0 / this.gacha.count_rest) + '%'
        }
    },
}
</script>
