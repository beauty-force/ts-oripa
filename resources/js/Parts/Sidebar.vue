<template>
    <div class="h-full flex flex-col bg-gradient-to-b from-neutral-50 to-white font-[mplus2]">
        <!-- Header -->
        <div class="px-6 py-5">
            <h2 class="text-xl font-bold bg-gradient-to-r from-sky-600 to-sky-400 bg-clip-text text-transparent">
                {{menu_title}}
            </h2>
        </div>

        <!-- Menu Items -->
        <nav class="flex-1 px-3">
            <ul class="space-y-1.5">
                <template v-for="item in main_menu">
                    <li v-if="item.type=='link'" :class="getActiveColor(item.route_name)" class="rounded-lg transition-all duration-200">
                        <Link :href="route(item.route_name)" as="button" 
                            class="w-full text-start py-3 px-4 flex items-center gap-3 group relative overflow-hidden"
                        >
                            <!-- Background effect on hover -->
                            <div class="absolute inset-0 bg-gradient-to-r from-sky-500/0 to-sky-500/0 group-hover:from-sky-500/5 group-hover:to-sky-500/10 transition-all duration-300"></div>
                            
                            <!-- Icon with hover effect -->
                            <span v-if="item.icon" class="w-5 h-5 text-center transition-all duration-200 group-hover:scale-110 group-hover:rotate-3">
                                {{ item.icon }}
                            </span>
                            
                            <!-- Text with hover effect -->
                            <span class="text-sm font-medium transition-all duration-200 group-hover:translate-x-0.5">
                                {{item.title}}
                            </span>

                            <!-- Active indicator -->
                            <div v-if="current_route.indexOf(item.route_name) !== -1" 
                                class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-sky-500 to-sky-400 rounded-l-full">
                            </div>
                        </Link>
                    </li>
                </template>
            </ul>
        </nav>

        <!-- Logout Button -->
        <div class="p-3 border-t border-neutral-100">
            <Link :href="route('logout')" method="get" 
                class="w-full text-start py-3 px-4 flex items-center gap-3 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 group relative overflow-hidden" 
                as="button"
            >
                <!-- Background effect on hover -->
                <div class="absolute inset-0 bg-gradient-to-r from-red-500/0 to-red-500/0 group-hover:from-red-500/5 group-hover:to-red-500/10 transition-all duration-300"></div>
                <span class="w-5 h-5 text-center transition-all duration-200 group-hover:scale-110 group-hover:rotate-3">🚪</span>
                <!-- Text with hover effect -->
                <span class="text-sm font-medium transition-all duration-200 group-hover:translate-x-0.5">ログアウト</span>
            </Link>
        </div>
    </div>
</template>

<script>
    import { Link, usePage } from '@inertiajs/inertia-vue3';
    import admin_menu from '@/Store/admin-menu';
    import user_menu from '@/Store/user-menu';
    import staff_menu from '@/Store/staff-menu';

    import { PlusIcon } from '@heroicons/vue/24/outline'

    export default {
        components: {Link, PlusIcon},
        data(){
            return {
                main_menu: [],
                current_route: "",
                menu_title: "管理者メニュー",
                user: null,
                rank_badge: null,
                point_link: route('user.point'),
            }
        }, 
        mounted(){
            this.current_route = route().current();
            this.user = usePage().props.value.auth.user;
            this.rank_badge = usePage().props.value.auth.rank_badge;
            if (this.user) {
                if (this.user.type==1) {  // if is admin, then ...
                    this.point_link = route('admin.point');
                }
            }
            if (this.user) {
                if (this.user.type==1) {
                    this.main_menu = admin_menu;
                    this.menu_title = "管理者メニュー";
                } else if (this.user.type==2) {
                    this.main_menu = staff_menu;
                    this.menu_title = "職員ページ";
                } else {
                    this.main_menu = user_menu;
                    this.menu_title = "マイページ";
                }
                this.main_menu = [
                    {
                        title: "プロフィール",
                        route_name: "profile.edit",
                        type: "link",
                    },
                    ...this.main_menu
                ];
            } else {
                this.main_menu = [];
            }
        },
        methods: {
            getActiveColor(route_name) {
                if(this.current_route.indexOf(route_name) !== -1) {
                    return 'bg-gradient-to-r from-sky-500/10 to-sky-400/10 text-sky-600 shadow-sm';
                } else {
                    return 'text-neutral-600 hover:text-sky-600';
                }
            },
            format_number(n) {
                return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
            },
        },  
    }
</script>