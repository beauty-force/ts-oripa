<template>

    <Head title="ランキング" />

    <AppLayout>

        <div class="h-full flex flex-col">
            <div class="px-4 pt-4 pb-1">
                <h1 class="md:text-center text-lg font-bold">ランキング</h1>
                <Link v-if="is_admin" :href="route('admin.ranking.history')" class="text-sm border border-sky-500 rounded-full px-3 py-1 hover:text-white hover:bg-sky-500 absolute top-4 right-3">
                    報酬履歴
                </Link>
                <Link v-else :href="route('ranking.about')" class="text-sm border border-sky-500 rounded-full px-3 py-1 hover:text-white hover:bg-sky-500 absolute top-4 right-3">
                    報酬について
                </Link>
            </div>
            <div class="pt-2 w-full">
                <div class="w-full text-neutral-500 space-x-1 border-b-4 border-sky-500 px-4 flex justify-center">
                    <button @click="onPeriodSelection(item.period)" v-for="(item, key) in periods"
                        class="-mb-[1px] flex-1 inline-block text-center py-1.5 rounded-t-md border-sky-500 border-x border-t"
                        :class="{ 'bg-sky-500 text-white': item.period == period, 'hover:bg-sky-400 hover:text-white': item.period != period }">
                        {{ item.title }}
                    </button>
                </div>
            </div>

            <div class="flex-1 relative">
                <div v-if="loading"
                    class="absolute top-0 bottom-0 left-0 right-0 flex justify-center items-center bg-neutral-300/75 z-[20]">
                    <div class="w-16 h-16 bg-white p-4 rounded-full shadow-lg">
                        <svg class="animate-spin h-8 w-8 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                </div>

                <div class="p-3">
                    <div v-for="(user, index) in users">
                        <div v-if="user.total_points == users[0].total_points" class="flex justify-center flex-col items-center gap-1 py-2 border-b">
                            <div class="relative flex justify-center w-full pb-4">
                                <img :src="users[0].avatar ? '/images/avatars/' + users[0].avatar : '/images/icon_user.png'"
                                    class="w-24 h-24 rounded-full border-[#EE4444] border-2" />
                                <img src="/images/top_one.svg" class="w-28 absolute bottom-0" />
                            </div>
                            <Link v-if="is_admin" :href="route('admin.users.detail', { id: users[0].id })" class="flex font-bold text-base hover:text-sky-500">
                                {{ users[0].name }}
                            </Link>
                            <span v-else class="flex font-bold text-base">
                                {{ users[0].name }}
                            </span>
                            <span class="flex gap-1 items-center justify-center text-sm">
                                <img src="/images/icon_cash.png" class="w-4 h-4" />
                                {{ parseInt(users[0].total_points).toLocaleString() }}
                            </span>
                        </div>
                        <div v-else class="flex items-center gap-2.5 border-b py-2">
                            <span class="w-8 text-center text-xl font-[mplus2]">
                                {{ users[index - 1].total_points != user.total_points ? index + 1 : '' }}
                            </span>
                            <img :src="user.avatar ? '/images/avatars/' + user.avatar : '/images/icon_user.png'"
                                class="w-14 h-14 rounded-full border" />
                            <div class="space-y-0.5">
                                <Link v-if="is_admin" :href="route('admin.users.detail', { id: user.id })" class="flex font-bold text-base hover:text-sky-500">
                                    {{ user.name }}
                                </Link>
                                <span v-else class="flex font-bold text-base">
                                    {{ user.name }}
                                </span>
                                <span class="flex gap-1 items-center text-sm">
                                    <img src="/images/icon_cash.png" class="w-4 h-4" />
                                    {{ parseInt(user.total_points).toLocaleString() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';

export default {
    components: { Head, AppLayout, Link },
    props: {
    },
    data() {
        return {
            user: null,
            period: '',
            users: [],
            loading: false,
            periods: [
                { title: '本日', period: 'daily' },
                { title: '週間', period: 'weekly' },
                { title: '月間', period: 'monthly' },
                { title: '全期間', period: 'total' },
            ]
        }
    },
    setup(props) {

    },
    computed: {
        is_admin() {
            return this.user && this.user.type == 1;
        }
    },
    mounted() {
        this.user = usePage().props.value.auth.user;
        this.onPeriodSelection('daily');
    },
    methods: {
        onPeriodSelection(selection) {
            if (this.period == selection) return;
            this.loading = true;
            this.users = [];
            this.period = selection;
            axios.get(route('ranking.data', { period: this.period })).then(response => {
                this.loading = false;
                this.users = response.data.users;
            }).catch(error => {

            });
        }
    },
}
</script>