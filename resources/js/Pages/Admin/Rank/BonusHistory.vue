<template>

    <Head title="報酬履歴" />

    <AppLayout>

        <div v-if="loading"
            class="absolute top-0 bottom-0 left-0 right-0 flex justify-center items-center bg-neutral-300/75 z-[20]">
            <div class="w-16 h-16 bg-white p-4 rounded-full shadow-lg">
                <svg class="animate-spin h-8 w-8 text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
        </div>

        <div class="px-4 pt-4 pb-1">
            <h1 class="md:text-center text-lg font-bold">報酬履歴</h1>
            <Link :href="route('ranking.index')" class="text-sm border border-sky-500 rounded-full px-3 py-1 hover:text-white hover:bg-sky-500 absolute top-4 right-3">
                報酬履歴
            </Link>
        </div>

        <div class="flex flex-col px-4 md:px-8 text-xs md:text-sm font-[mplus2]">
            <div class="w-full flex pt-4 gap-2 items-center justify-between relative">
                <MonthPickerInput @change="showDate" lang="ja" :editableYear="true"></MonthPickerInput>
                <button @click="getHistory" class="text-sm border border-sky-500 rounded-full px-3 py-1 hover:text-white hover:bg-sky-500">履歴を見る</button>
            </div>
            
            <table>
                <template v-for="(item, index) in _history">
                    <template v-if="index == 0 || _history[index-1].startDate != item.startDate">
                        <tr>
                            <td :colspan="3" class="pt-8 pb-2">
                                <span class="md:text-lg font-semibold"><span>{{ periods[item.period] }}</span>&nbsp;&nbsp;&nbsp;<span>( {{item.startDate}} - {{ item.endDate }} )</span></span>
                            </td>
                        </tr>
                    </template>
                    <tr class="leading-7">
                        <td class="">
                            <span class="flex gap-3 text-nowrap">
                                <span>{{ item.rank }} 位</span>
                                <span class="flex gap-1 items-center justify-center text-sm">
                                    (<img src="/images/icon_cash.png" class="w-4 h-4" />
                                    {{ format_number(item.total_point) }} )
                                </span>
                            </span>
                        </td>
                        <td class="">
                            <Link :href="route('admin.users.detail', {id: item.user_id})" class="text-sky-600">{{ item.email }}</Link>
                        </td>
                        <td>
                            {{ item.ref_id > 0 ? `${format_number(item.description)} coin付与` : item.description }}
                        </td>
                    </tr>                        
                </template>

            </table>
        </div>

    </AppLayout>
</template>

<style>
.month-picker-input {
    width: 100%;
}
.month-picker__year {
    width: 100%;
}
.month-picker__year>button {
    width: 40px;
    padding-bottom: 0.5rem;
}
</style>
<script>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import { MonthPicker } from 'vue-month-picker'
import { MonthPickerInput } from 'vue-month-picker'

export default {
    components: { Head, AppLayout, Link, MonthPicker, MonthPickerInput },
    props: {
        history: Object,
    },
    data() {
        return {
            month: '',
            loading: false,
            _history: [],
            periods: {
                daily: '日次報酬',
                weekly: '週次報酬',
                monthly: '月次報酬'
            }
        }
    },
    mounted() {
        this._history = this.history;
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        },
        showDate(date) {
            this.month = date.year + '-' + ("0" + date.monthIndex).slice(-2);  
        },
        getHistory() {
            this.loading = true;
            axios.get(route('admin.ranking.get_history', { month: this.month })).then(response => {
                this.loading = false;
                this._history = response.data.history;
            }).catch(error => {

            });
        }
    },
    setup(props) {
        
    },
}
</script>