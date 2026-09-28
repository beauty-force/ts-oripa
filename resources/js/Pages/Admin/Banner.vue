<template>
    <Head title="バナー設定" />

    <AppLayout>
        <div class="max-w-6xl mx-auto pt-6 px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-xl font-bold text-gray-900 mb-2">バナー設定</h1>
                <p class="text-gray-600">バナー画像とURLを管理できます</p>
            </div>

            <!-- Add New Banner Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">新規バナー追加</h2>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit()" class="space-y-6">
                        <!-- Banner Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">バナー画像</label>
                            <div class="flex items-center gap-4 flex-col md:flex-row">
                                <div class="w-full md:flex-1">
                                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>画像をアップロード</span>
                                                    <input @change="e => previewImage(e, -1)" ref="file" type="file" class="sr-only" accept="image/*"/>
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="banner.image != ''" class="flex-shrink-0 w-full md:w-2/3">
                                    <img :src="banner.image" class="w-full h-auto object-cover rounded-lg border border-gray-200" />
                                </div>
                            </div>
                        </div>

                        <!-- Banner URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">バナーURL</label>
                            <input 
                                v-model="banner.link_url" 
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                placeholder="https://example.com"
                            />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                バナーを追加
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Banners Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">既存バナー編集</h2>
                    <p class="text-sm text-gray-600 mt-1">ドラッグ&ドロップで順序を変更できます</p>
                </div>
                
                <div class="p-6">
                    <draggable class="grid grid-cols-1 lg:grid-cols-2 gap-6" :list="banners" @change="log" handle=".drag-handle">
                        <div
                            class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden"
                            v-for="(element, index) in banners"
                            :key="element.id">
                            
                            <!-- Drag Handle & Header -->
                            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50">
                                <div class="drag-handle flex items-center cursor-move">
                                    <div class="w-5 h-5 text-gray-400 hover:text-gray-600 transition-colors mr-2">
                                        <svg fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 6h8v2H8V6zm0 5h8v2H8v-2zm0 5h8v2H8v-2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">バナー #{{ index + 1 }}</span>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-1">
                                    <button @click.prevent="levelUp(element.id)" 
                                        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                        title="上に移動">
                                        <ArrowLongUpIcon class="w-4 h-4" />
                                    </button>
                                    <button @click.prevent="levelDown(element.id)" 
                                        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                        title="下に移動">
                                        <ArrowLongDownIcon class="w-4 h-4" />
                                    </button>
                                    <button @click="destroy(element.id)" 
                                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                        title="削除">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Banner Content -->
                            <div class="p-4 space-y-4">
                                <!-- Banner Image -->
                                <div>
                                    <div v-if="element.image != ''" class="w-full relative group">
                                        <img :src="element.image" class="w-full h-auto max-h-32 object-contain rounded-lg border border-gray-200 bg-gray-50" />
                                        <!-- Change Image Overlay -->
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center">
                                            <label class="cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <div class="bg-white rounded-lg px-4 py-2 shadow-lg flex items-center space-x-2">
                                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-700">画像を変更</span>
                                                    <input @change="e => previewImage(e, index)" ref="photo" type="file" class="sr-only" accept="image/*"/>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center h-48 border-2 border-gray-300 border-dashed rounded-lg bg-gray-50">
                                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <label class="cursor-pointer text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                            <span>画像をアップロード</span>
                                            <input @change="e => previewImage(e, index)" ref="photo" type="file" class="sr-only" accept="image/*"/>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- URL Input -->
                                <div>
                                    <input 
                                        v-model="element.link_url" 
                                        type="text" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" 
                                        placeholder="バナーURLを入力..."
                                    />
                                </div>
                            </div>
                        </div>
                    </draggable>

                    <!-- Save Order Button -->
                    <div class="mt-6 flex justify-center">
                        <button 
                            @click.prevent="submit_order()" 
                            :class="{ 'opacity-50 cursor-not-allowed': order_form.processing }" 
                            :disabled="order_form.processing" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                        >
                            <svg v-if="order_form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ order_form.processing ? '保存中...' : '変更を保存' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import { Inertia } from '@inertiajs/inertia';

import { VueDraggableNext } from 'vue-draggable-next';
import { ArrowLongUpIcon, ArrowLongDownIcon } from '@heroicons/vue/24/solid'
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: {Link, Head, AppLayout, draggable:VueDraggableNext, ArrowLongUpIcon, ArrowLongDownIcon},
    props: {
        errors: Object,
        auth: Object,
        banners: Object,
    },
    methods: {
        log() {

        },
        submit() {
            this.new_form.file = this.$refs.file.files[0];
            this.new_form.link_url = this.banner.link_url;
            this.new_form.post(route('admin.banner.create'),{ onSuccess:()=>this.new_form.reset(),preserveScroll: true});
        },
        destroy(id){
            confirm("削除してもいいですか？", '', 'error').then((result) => {
                if (result) {
                    Inertia.delete(route('admin.banner.destroy', id), {preserveScroll: true});
                }
            });
        },
        previewImage(e, index) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                if (index == -1) this.banner.image = URL.createObjectURL(file);
                else this.banners[index].image = URL.createObjectURL(file);
            }
        },
        levelUp(id) {
            let i;
            for(i=1; i<this.banners.length; i++) {
                if (this.banners[i].id==id) {
                    break;
                }
            }
            if (i<this.banners.length) {
                this.arrayMove(i, i-1);
            }
        },
        arrayMove(from, to) {
            this.banners.splice(to, 0, this.banners.splice(from, 1)[0]);
        },
        levelDown(id) {
            let i;
            for(i=0; i<this.banners.length-1; i++) {
                if (this.banners[i].id==id) {
                    break;
                }
            }
            if (i<(this.banners.length-1)) {
                this.arrayMove(i, i+1);
            }
        },
        submit_order() {
            this.order_form.files = this.$refs.photo.map(item => item.files[0]);
            this.order_form.banners = this.banners;
            this.order_form.post(route('admin.banner.store'), {
                preserveScroll: true,
                onFinish: () => {
                    this.order_form.reset();
                    this.banner.image = "";
                }
            });
        }
    },
    data(){
        return {
            open: true,
            dragging: false,
            banner: {
                link_url: "",
                image: ""
            }
        }
    },
    setup(props) {
        const new_form = useForm({
            file: null,
            link_url: null
        });
        const order_form = useForm( {
            files: [],
            banners: [],
        })
        return {new_form, order_form};
    },
    mounted() {
        
    }
}
</script>

