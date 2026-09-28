<template>
    <div class="flex min-h-screen text-neutral-700 text-base underline-offset-2 font-medium">
        <TransitionRoot :show="sidebarOpened" class="h-full">
            <TransitionChild enter="transition ease-in-out duration-200 transform" enter-from="translate-x-full"
                enter-to="translate-x-0" leave="transition ease-in-out duration-200 transform"
                leave-from="translate-x-0" leave-to="translate-x-full" as="template">
                <div class="fixed h-full right-0 z-40">

                    <div class="flex flex-col relative h-full w-64 bg-gray-100 border-l border-neutral-200 ml-auto">
                        <button @click="sidebarOpened = false" 
                            class="z-20 absolute top-3 md:right-4 right-2 flex items-center justify-center w-9 h-9 rounded-full focus:outline-none bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 text-white shadow-sm transition-all duration-200"
                            type="button" 
                            value="Close sidebar">
                            <XMarkIcon class="h-5 w-5" />
                        </button>
                        
                        <div class="flex-1 overflow-y-auto">
                            <Sidebar />
                        </div>
                    </div>
                </div>
            </TransitionChild>
            <TransitionChild enter="transition-opacity ease-linear duration-200" enter-from="opacity-0"
                enter-to="opacity-100" leave="transition-opacity ease-linear duration-200" leave-from="opacity-100"
                leave-to="opacity-0" class="fixed z-30 w-full h-full">
                <div class="bg-neutral-600 h-full bg-opacity-50" @click="sidebarOpened = false"></div>
            </TransitionChild>
        </TransitionRoot>

        <div class="w-full flex flex-col max-w-[760px] mx-auto shadow-md">
            <div class="flex-shrink-0 w-full max-w-[760px] sticky top-0 z-[15] bg-white">
                <div class="flex justify-center">
                    <div class="md:w-[760px] w-full flex md:px-3 px-2 h-20 mx-auto justify-center border-x border-b border-neutral-200 gap-1 items-center z-10">
                        <div class="flex items-center flex-1">
                            <Logo/>
                        </div>
                        <div v-if="this.user" class="flex-shrink-0 flex items-center gap-1 h-8 md:h-9 text-white text-sm">
                            <Link :href="point_link" class="text-center bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 rounded-full px-4 h-full shadow-sm transition-all duration-200"> 
                                <div class="flex items-center h-full gap-2">
                                    <img src="/images/icon_cash.png" class="md:h-5 h-4 drop-shadow-sm"/>
                                    <span class="font-semibold">{{format_number(point_value)}}</span>
                                    <span class="text-xs opacity-90">PT</span>
                                    <span class="border-l border-white/30 h-4"></span>
                                    <span class="text-xs">+</span>
                                </div>
                            </Link>
                        </div>
                        <Link v-if="!user" :href="route('register')" class="rounded-full bg-gradient-to-r from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500 text-sm md:px-4 px-2 md:py-2 py-1.5 text-white shadow-sm transition-all duration-200" as="button">新規登録</Link>
                        <div class="relative flex-shrink-0 flex items-center">
                            <div v-if="this.user" @click="sidebarOpened=true" class="overflow-hidden cursor-pointer w-10 md:w-11 h-10 md:h-11 rounded-full border border-neutral-300">
                                <img :src="user.avatar ? '/images/avatars/' + user.avatar : '/images/icon_user.png'" class="w-full h-full object-cover"/>
                            </div>
                            <Link :href="route('login')" v-else class="rounded-full bg-gradient-to-r from-neutral-700 to-neutral-600 hover:from-neutral-800 hover:to-neutral-700 text-sm md:px-4 px-2 md:py-2 py-1.5 text-white shadow-sm transition-all duration-200" as="button">ログイン</Link>
                        </div>
                    </div>
                </div>
            </div>
            
            <main class="w-full flex-1 relative bg-repeat flex flex-col">
                <Branch />
                <!-- Moved coupon banner to branch.vue
                <div v-if="show_banner" class="md:w-[760px] w-full flex justify-center mx-auto">
                    <a :href="'//' + show_banner.link_url" target="_blank">
                        <img class="w-full" :src="show_banner.image" />
                    </a>
                </div>
                -->
                <Category v-if="!hide_cat_bar"/>
                
                <div class="w-full flex flex-1 flex-col" >
                    <div class="relative flex-1" >
                        <div class="md:w-[760px] h-full flex flex-col w-full mx-auto bg-white">
                            <div class="bg-no-repeat bg-cover bg-center flex-1">
                                <slot />
                            </div>
                        </div>
                    </div>
                    <Footer />
                </div>
            </main>
        </div>
    </div>
</template>

<script>

import {TransitionRoot, TransitionChild, Dialog, DialogOverlay, Menu, MenuButton, MenuItems, MenuItem} from '@headlessui/vue';
import Logo from '@/Parts/Logo.vue';
import Sidebar from '@/Parts/Sidebar.vue';
import Footer from '@/Parts/Footer.vue';
import Branch from '@/Parts/Branch.vue';
import { usePage, Link } from '@inertiajs/inertia-vue3';

import Category from '@/Parts/Category.vue';

import { XMarkIcon, ChevronLeftIcon, Bars3Icon, PlusIcon } from '@heroicons/vue/24/outline'

import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { useConfirm } from "@/composables/useConfirm";
const { alert } = useConfirm();

export default {
    components: {Category, Branch, Sidebar, Footer, TransitionRoot, TransitionChild, Dialog, DialogOverlay, XMarkIcon, ChevronLeftIcon, Bars3Icon, Link, Menu, MenuButton, MenuItems, MenuItem, Logo, PlusIcon },
    data() {
        return {
            sidebarOpened: false,
            page_title: "",
            user: {},
            point_value: 0,
            dp_value: 0,
            point_link: route('user.point'),
            hide_cat_bar: "",
            hide_back_btn: "",
            show_result_bg: "",
            show_notification: 0,
        }
    },
    props: {
        is_home: Boolean
    },
    mounted(){        
        this.user = usePage().props.value.auth.user;
        if (this.user) {
            this.point_value = this.user.point;
            this.dp_value = this.user.dp;
            if (this.user.type==1) {  // if is admin, then ...
                this.point_link = route('test.user.point');
            }
        }

        this.gacha_button = usePage().props.value.gacha_button;

        this.hide_cat_bar = usePage().props.value.hide_cat_bar;
        this.hide_back_btn = usePage().props.value.hide_back_btn;
        this.show_result_bg = usePage().props.value.show_result_bg;
        this.show_notification = usePage().props.value.show_notification;

        if (usePage().props.value.flash.type=="notification") {
            this.notification(usePage().props.value.flash.message);
        }
    },
    computed : {
        flash() {
            return usePage().props.value.flash;
        } 
    },
    watch : {
        flash: function(newVal, oldVal) {
            if (newVal.type=="notification") {
                this.notification(newVal.message);
            }
            if (newVal.type=="dialog") {
                alert(newVal.title, newVal.message);
            }
            if (newVal.type=="dialog_success") {
                alert(newVal.title, newVal.message, 'success');
            }
            if (newVal.type=="dialog_error") {
                alert(newVal.title, newVal.message, 'error');
            }
            if (newVal.data && newVal.data.user) {
                this.point_value = newVal.data.user.point;
                this.dp_value = newVal.data.user.dp;
                this.user = usePage().props.value.auth.user;
            }
        }
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        }, 
        notification(message) {
            if(message){
                toast(message, {
                    autoClose: 3000
                });
            }
        },
        back() {
            window.history.back();
        },
    }
}
</script>