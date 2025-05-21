<template>
    <div class="flex min-h-screen text-neutral-700 text-base underline-offset-2 font-medium">
        <TransitionRoot :show="sidebarOpened" class="h-full">
            <TransitionChild enter="transition ease-in-out duration-200 transform" enter-from="translate-x-full"
                enter-to="translate-x-0" leave="transition ease-in-out duration-200 transform"
                leave-from="translate-x-0" leave-to="translate-x-full" as="template">
                <div class="fixed h-full right-0 z-40">

                    <div class="flex flex-col relative h-full w-64 bg-gray-100 border-l border-neutral-200 ml-auto">
                        <button @click="sidebarOpened = false"
                            class="z-20 absolute top-3 md:right-4 right-2 flex items-center justify-center w-9 h-9 rounded-full focus:outline-none bg-theme bg-theme-hover"
                            type="button" value="Close sidebar">
                            <XMarkIcon class="h-5 w-5 rounded-full text-white" />
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

        <TransitionRoot :show="isDialogMessage" class="h-full">
            
            <Dialog :open="isDialogMessage" @close="{ closeModal?.call(); isDialogMessage=false; }"  class="fixed h-full inset-0 z-40">
                <TransitionChild  
                    enter="transition ease-in-out duration-200 transform"
                    enter-from="translate-y-full"
                    enter-to="translate-y-0"
                    leave="transition ease-in-out duration-200 transform"
                    leave-from="translate-y-0"
                    leave-to="translate-y-full"
                    as="template">
                    <div class="flex flex-col text-neutral-700 rounded relative z-20 top-20 w-fit min-w-64 px-4 bg-neutral-50 border-l border-neutral-200 m-auto max-w-[90%]">
                        <div class="pt-3 pb-1 px-1 text-center font-bold">
                            {{dialogTitle}}
                        </div>
                        
                        <div class="px-4 text-sm text-center" v-html="dialogMessage">
                        </div>

                        <hr class="mt-3"/>
                        <button @click="{ closeModal?.call(); isDialogMessage=false; }" class="cursor-pointer p-2 text-cyan-500 text-center text-base focus-visible:outline-0">
                            OK
                        </button>
                    </div>
                </TransitionChild>
                <TransitionChild 
                    enter="transition-opacity ease-linear duration-200"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="transition-opacity ease-linear duration-200"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                    as="template">
                    <DialogOverlay class="fixed inset-0 bg-neutral-600 h-full bg-opacity-50"></DialogOverlay>
                </TransitionChild>
            </Dialog>
        </TransitionRoot>

        <div class="w-full max-w-[760px] mx-auto flex flex-col bg-white shadow-[0px_0px_5px_0px_#fac239aa]">
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
            <main class="w-full flex-1 relative">
                <Category v-if="!hide_cat_bar" :is_admin="true"/>
                <slot />
            </main>
            
        </div>
        
    </div>
</template>

<script>
import {TransitionRoot, TransitionChild, Dialog, DialogOverlay, Menu, MenuButton, MenuItems, MenuItem} from '@headlessui/vue';
import Logo from '@/Parts/Logo.vue';
import Sidebar from '@/Parts/Sidebar.vue';
import Footer from '@/Parts/Footer.vue';
import { usePage, Link } from '@inertiajs/inertia-vue3';

import Category from '@/Parts/Category.vue';

import { XMarkIcon, ChevronLeftIcon, Bars3Icon, PlusIcon } from '@heroicons/vue/24/outline'

import Toastify from "toastify-js";
import { useConfirm } from "@/composables/useConfirm";
const { alert } = useConfirm();

export default {
    components: {Category, Sidebar, Footer, TransitionRoot, TransitionChild, Dialog, DialogOverlay, XMarkIcon, ChevronLeftIcon, Bars3Icon, Link, Menu, MenuButton, MenuItems, MenuItem,Logo, PlusIcon},
    props: {
        closeModal: Function
    },
    data() {
        return {
            sidebarOpened: false,
            isDialogMessage: false,
            dialogMessage: "",
            dialogTitle: "",
            page_title: "",
            user: {},
            point_value: 0,
            dp_value: 0,
            point_link: route('user.point'),            
            hide_cat_bar: "",
            gacha_button: {},
        }
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

        const images = document.querySelectorAll('img.lazy');
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const loadImage = (image) => {
            image.src = image.dataset.src;
            image.classList.remove('lazy');
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadImage(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, options);

        images.forEach(image => {
            observer.observe(image);
        });
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
                alert(newVal.title, newVal.message, 'info', this.closeModal);
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
                Toastify({
                    text: message,
                    duration: 30000,
                    newWindow: true,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: "right", // `left`, `center` or `right`
                    stopOnFocus: false, // Prevents dismissing of toast on hover
                    className: "toastify-content newClass",
                    onClick: function(){} // Callback after click
                }).showToast();
            }
        },
        back() {
            window.history.back();
        },
    }
}
</script>