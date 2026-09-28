<template>
    <Head title="ランク特典設定" />

    <AppLayout :closeModal="closeModal">
        <div class="pt-4 px-5 py-2">
            <h1 class="mb-2 text-lg font-bold">ランク特典設定</h1>
            <hr class="mb-2" />
            <div v-for="(item, key) in ranks" class="my-2 text-[0.6em] md:text-[1em] sm:text-[0.8em]" :key="key">
                <div class="flex flex-wrap border-neutral-200 items-center gap-[0.8em]">
                    <div class="w-[1em]">{{item.rank}}</div>
                    <img :src="item.badge" class="h-[8.5vw] max-h-[80px] inline-block object-contain"/>
                    <div class="flex-1 flex bg-neutral-200 rounded-lg justify-center relative">
                        <img :src="item.image" class="h-[8.5vw] max-h-[80px]"/>
                        <div class="absolute top-0 bottom-0 left-0 right-0 flex items-center">
                            <div class="text-center text-[0.9em] text-black font-black w-[30%] h-full flex flex-col items-center justify-center relative">
                                <span class="leading-[1.4em]" v-html="pt_rate_string(item)" :style="{ WebkitTextStrokeColor: 'white', WebkitTextStrokeWidth: '2px' }"></span>
                                <span class="absolute leading-[1.4em]" v-html="pt_rate_string(item)"></span>
                            </div>
                            <div class="h-full w-[0.3em] bg-white"></div>
                            <div class="text-center text-[0.8em] font-bold text-white w-[40%] h-full flex items-center justify-center">
                                <div class="text-center flex gap-[0.4em] items-end h-full">
                                    <div class="relative flex justify-center text-[1em]">
                                        <span class="leading-[1.2em]" :style="{ WebkitTextStrokeColor: 'black', WebkitTextStrokeWidth: '2.5px' }">( {{ item.title }} )</span>
                                        <span class="absolute leading-[1.2em]">( {{ item.title }} )</span>
                                    </div>
                                </div>
                            </div>
                            <div class="h-full w-[0.3em] bg-white"></div>
                            <div class="w-[30%] flex divide-x-[0.3em] divide-white h-full relative">
                                <div class="text-center text-[0.9em] text-black font-black w-[40%] h-full flex items-center justify-center">
                                    <span v-html="item.dp_rate > 0 ? item.dp_rate + '%' : `<span class='text-[1.2em]'>???</span>`" :style="{ WebkitTextStrokeColor: 'white', WebkitTextStrokeWidth: '2px' }"></span>
                                    <span class="absolute" v-html="item.dp_rate > 0 ? item.dp_rate + '%' : `<span class='text-[1.2em]'>???</span>`"></span>
                                </div>
                                <div class="text-center text-[0.9em] text-black font-black w-[60%] h-full flex items-center justify-center relative">
                                    <span class="leading-[1.4em]" v-html="bonus_pt_string(item)" :style="{ WebkitTextStrokeColor: 'white', WebkitTextStrokeWidth: '2px' }"></span>
                                    <span class="absolute leading-[1.4em]" v-html="bonus_pt_string(item)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm" v-if="item.limit > 0">消費基準量: {{ format_number(item.limit) }} PT</span>
                    <span v-else></span>
                    <div class="my-2 text-center">
                        <button type="button" @click="modalOpen(item)" class="inline-block items-center px-6 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 mr-2">
                            編集
                        </button>
    
                        <button type="button" @click="destroy_rank(item.id)" class="inline-block items-center px-6 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                            削除
                        </button>
                    </div>
                </div>
            
            </div>
            <div class="text-center mb-6">
                <button type="button" @click="modalOpen(0)" class="inline-block items-center px-10 py-2.5 bg-cyan-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150 mr-2">
                    追加
                </button>
            </div>
            
        </div>

        <template>
        <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="relative z-20" @close="open = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-20 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <DialogPanel class="p-3 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <form @submit.prevent="submit()">
                        <div class="mb-4">
                            <label for="rank" class="block font-medium text-sm text-neutral-700 mb-1">ランク（半角数字）<span class="text-red-500">*</span></label>
                            <input v-model="form.rank" id="rank" type="number" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" required/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.rank }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label  for="title" class="block font-medium text-sm text-neutral-700 mb-1">ランク名<span class="text-red-500">*</span></label>
                            <input v-model="form.title" id="title" type="text" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" required/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.title }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label  for="limit" class="block font-medium text-sm text-neutral-700 mb-1">消費基準量（半角数字）<span class="text-red-500">*</span></label>
                            <input v-model="form.limit" id="limit" type="number" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" required/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.limit }}
                            </div>
                        </div>


                        <div class="mb-4">
                            <label for="bonus" class="block font-medium text-sm text-neutral-700 mb-1">ボーナスPT（半角数字）<span class="text-red-500">*</span></label>
                            <input v-model="form.bonus" id="bonus" type="number" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" required/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.bonus }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="pt_rate" class="block font-medium text-sm text-neutral-700 mb-1">購入時PT付与特典 (%)<span class="text-red-500">*</span></label>
                            <input v-model="form.pt_rate" id="pt_rate" type="number" min="0" step="0.01" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" required/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.pt_rate }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="dp_rate" class="block font-medium text-sm text-neutral-700 mb-1">EP付与率 (%)<span class="text-red-500">*</span></label>
                            <input v-model="form.dp_rate" id="dp_rate" type="number" min="0" step="0.01" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" required/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.dp_rate }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="file1-1" class="block font-medium text-sm text-neutral-700 mb-1">ランクバー画像 <span class="text-red-500">*</span></label>
                            <div class="text-center w-full">
                                <img 
                                    v-if="url1"
                                    :src="url1"
                                    class="inline-block mt-4 h-24"
                                />
                            </div>
                            <input @change="previewImage1" ref="photo1" id="file1-1" type="file" class="w-full"/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.image }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="file1-1" class="block font-medium text-sm text-neutral-700 mb-1">ランクカード画像 <span class="text-red-500">*</span></label>
                            <div class="text-center w-full">
                                <img 
                                    v-if="url2"
                                    :src="url2"
                                    class="inline-block mt-4 h-24"
                                />
                            </div>
                            <input @change="previewImage2" ref="photo2" id="file1-1" type="file" class="w-full"/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.badge }}
                            </div>
                        </div>
                        <div class="mb-6 text-center">
                            <button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="inline-block items-center px-8 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 mr-2">
                                保存
                            </button>

                            <button type="button" @click="open = false" class="inline-block items-center px-8 py-2.5 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                                キャンセル
                            </button>
                        </div>
                        </form>
                    </DialogPanel>
                </TransitionChild>
                </div>
            </div>
            </Dialog>
        </TransitionRoot>
        </template>
    </AppLayout>
</template>

<script>
import { Head, useForm, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import { Inertia } from '@inertiajs/inertia';

import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: {Head, AppLayout, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot},
    props: {
        errors: Object,
        ranks: Object
    },
    data() {
        return {
            url1: null,
            url2: null,
            open: false,
        }
    },
    
    methods: {
        destroy_rank(id) {
            confirm("削除してもいいですか？", '', 'error').then((result) => {
                if (result) {
                    Inertia.delete(route('admin.rank.destroy', {id:id}));
                }
            });
        },
        modalOpen(rank) {
            if (rank) {
                this.form.id = rank.id;
                this.form.rank = rank.rank;
                this.form.title = rank.title;
                this.form.limit = rank.limit;
                this.form.bonus = rank.bonus;
                this.form.pt_rate = rank.pt_rate;
                this.form.dp_rate = rank.dp_rate;
                this.form.image = "";
                this.form.badge = "";
                this.url1 = rank.image;
                this.url2 = rank.badge;
            } else {
                this.form.id = 0;
                this.form.rank = "";
                this.form.title = "";
                this.form.limit = "";
                this.form.bonus = "";
                this.form.pt_rate = 0;
                this.form.dp_rate = 1;
                this.form.image = "";
                this.form.badge = "";
                this.url1 = "";
                this.url2 = "";
            }
            this.open = true;
        },
        closeModal() {
            this.open = false;
        },
        submit() {
            if (this.$refs.photo1.files[0]) {
                this.form.image = this.$refs.photo1.files[0];
            }
            if (this.$refs.photo2.files[0]) {
                this.form.badge = this.$refs.photo2.files[0];
            }
            this.form.post(route('admin.rank.store'), {
                preserveScroll: true,
                onFinish: () => {
                },
            }); 
        },  

        previewImage1(e) {
            const file = e.target.files[0];
            this.url1 = URL.createObjectURL(file);
        },
        previewImage2(e) {
            const file = e.target.files[0];
            this.url2 = URL.createObjectURL(file);
        },
        pt_rate_string(item) {
            if (item.rank == 1) return '<span class="text-[1.5em]">—</span>'
            if (item.limit < 0 || item.pt_rate <= 0) return '<span class="text-[1.2em]">???</span>'
            return `${item.pt_rate}%付与<br/>(購入時PTの)`
        },
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
        bonus_pt_string(item) {
            if (item.rank == 1) return '<span class="text-[1.5em]">—</span>'
            if (item.limit < 0 || item.pt_rate <= 0) return '<span class="text-[1.2em]">???</span>'
            return `${this.format_number(item.bonus)}PT`
        },
    },
    setup(props) {
        let form_data = {
            id: "",
            rank: "",
            title: "",
            limit: "",
            bonus: "",
            pt_rate: "",
            dp_rate: "",
            image: "",
            badge: "",
        };
        const form = useForm( form_data );

        return { form }
    },
    mounted() {
    },
}
</script>