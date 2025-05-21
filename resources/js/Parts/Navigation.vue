<template>
    <div class="w-full bottom-0 bg-white border-t flex">
        <template v-for="item in main_menu">
            <Link :href="route(item.route_name)" :class="getActiveColor(item.route_name)" class="flex-1 text-center md:w-auto w-[100%] flex flex-col gap-1 justify-center items-center px-1 py-3 md:py-4">
                <i :class="item.icon" class="text-lg md:text-xl"></i>
                <span class="font-[mplus2] text-sm md:text-base">{{ item.title }}</span>
            </Link>
        </template>
    </div>
</template>

<style>
@import 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css';
</style>

<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';
import menu from '@/Store/navigation-menu';

export default {
    components: {Link},
    data () {
        return {
            main_menu: [],
            current_route: "",
        };
    },
    methods: {
        getActiveColor(route_name) {
            if(this.current_route.indexOf(route_name) !== -1) {
                return 'bg-sky-500 text-white';
            } else {
                return 'text-neutral-600 hover:text-sky-500 hover:bg-neutral-50';
            }
            
        },
    },
    mounted() {
        this.current_route = route().current();
        this.main_menu = menu;
    },
}
</script>

