<template>
    <Head title="ガチャ–結果" />

    <AppLayout :is_home="true">
        <div class="sticky top-20 z-20 w-full py-4 bg-white">
            <h1 class="text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent text-center">
                ガチャ – 結果
            </h1>
        </div>
        <div class="md:w-[760px] m-auto items-center py-2 flex flex-col justify-center">
            <!-- Header Section -->

            <div>
                <!-- Products Grid -->
                <div class="px-6 mb-4 grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-6">
                    <template v-for="(product, key) in products.data">
                        <div class="relative group">
                            <label :for="('checkbox' + product.id)" class="block cursor-pointer">
                                <!-- Product Card -->
                                <div class="transform transition-all duration-200 hover:scale-[1.02] relative">
                                    <!-- Product Image Container -->
                                    <div class="relative mb-2 rounded-lg overflow-hidden border-2 transition-all duration-200"
                                         :class="{
                                             'border-sky-500 shadow-lg shadow-sky-200 scale-[1.02]': form.checks['id'+product.id],
                                             'border-transparent hover:border-sky-300': !form.checks['id'+product.id] && product.status == 1,
                                             'border-gray-200': product.status != 1
                                         }">
                                        <img class="w-full h-full object-cover" :src="product.image" />
                                        
                                        <!-- Selection Overlay -->
                                        <div v-if="form.checks['id'+product.id]" 
                                             class="absolute inset-0 bg-sky-500 bg-opacity-20 flex items-center justify-center">
                                            <div class="bg-white rounded-full p-1 shadow-lg">
                                                <svg class="w-5 h-5 text-sky-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <img v-if="product.badge" :src="product.badge" class="absolute bottom-2 right-2 w-[25%]" :alt="product.badge" />
                                        
                                        <!-- Points Badge -->
                                        <div class="absolute top-2 right-2">
                                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-sky-500 to-sky-600 text-white shadow-sm">
                                                {{format_number(product.point)}} PT
                                            </div>
                                        </div>

                                        <!-- Checkbox -->
                                        <div v-if="product.status == 1" class="absolute top-2 left-2">
                                            <input :id="('checkbox' + product.id)" 
                                                type="checkbox" 
                                                v-model="form.checks['id'+product.id]" 
                                                @change="checkone" 
                                                class="h-4 w-4 cursor-pointer rounded border-sky-400 text-sky-500 shadow-sm focus:ring-sky-500"
                                                :class="{'hidden': product.status == 3 || form.checks['id'+product.id]}"
                                                />
                                        </div>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="px-1">
                                        <div class="text-neutral-800 font-medium truncate text-center text-sm"
                                             :class="{ 'text-sky-700 font-semibold': form.checks['id'+product.id] }">
                                            {{product.name}}
                                        </div>
                                        <div class="text-neutral-500 text-xs truncate text-center mt-0.5"
                                             :class="{ 'text-sky-600': form.checks['id'+product.id] }">
                                            {{product.rare}}
                                        </div>

                                        <!-- Shipping Notice -->
                                        <div v-if="product.status == 3" class="text-xs text-orange-500 text-center">
                                            発送依頼済み
                                        </div>
                                        
                                    </div>

                                </div>
                            </label>
                        </div>
                    </template>
                </div>

                <!-- Bottom Action Bar -->
                <div class="sticky bottom-0 z-10 py-3 mt-6 bg-white">
                    <!-- Top Line: Select All + Points -->
                    <div class="max-w-md m-auto flex items-center justify-between px-4 mb-2">
                        <!-- Select All -->
                        <label class="cursor-pointer inline-flex items-center px-3 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 hover:border-gray-300 transition-all duration-200 shadow-sm hover:shadow-md">
                            <input type="checkbox" 
                                v-model="isCheckAll" 
                                @change="checkall()" 
                                class="h-4 w-4 rounded border-neutral-300 text-sky-500 shadow-sm focus:ring-sky-500"/>
                            <span class="ml-2 text-xs font-medium text-neutral-700">全て選択</span>
                            <svg class="w-4 h-4 ml-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </label>

                        <!-- Points Display -->
                        <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-gradient-to-r from-sky-50 to-blue-50 border border-sky-200 shadow-sm">
                            <svg class="w-4 h-4 text-sky-600 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xs font-medium text-sky-700">選択:</span>
                            <span class="ml-1 text-sm font-bold text-sky-600">{{format_number(selectedPoints)}} PT</span>
                        </div>
                    </div>

                    <!-- Bottom Line: Action Buttons -->
                    <div class="max-w-md m-auto flex justify-center gap-x-3 px-4">
                        <button type="button" 
                            @click="submit()"
                            :class="{ 'opacity-50': form.processing }" 
                            :disabled="form.processing" 
                            class="flex-1 inline-flex items-center justify-center py-2 rounded-lg font-medium text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg gap-2">
                            <span>ポイント変換</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                        <button type="button" 
                            @click="deliver()"
                            :class="{ 'opacity-50': form.processing }" 
                            :disabled="form.processing" 
                            class="flex-1 inline-flex items-center justify-center py-2 rounded-lg font-medium text-white bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg gap-2">
                            <span>発送依頼</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Notice -->
                <div class="pb-6 pt-3 text-center text-xs text-neutral-500">
                    ※選択されなかった商品は「獲得済み 商品一覧」に移動されます。
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import { PlayIcon } from '@heroicons/vue/24/solid';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: {Head, AppLayout, Link, PlayIcon},
    props: {
        errors: Object,
        auth: Object,
        category_share: Object,
        products: Object,
        token: String,
        show_review: Boolean,
    },
    data() {
        return {
            isCheckAll: false,
        }  
    },
    setup(props) {
        let checks = {};
        let i;
        for(i=0; i<props.products.data.length; i++) {
            checks['id'+props.products.data[i]['id']] = false;            
        }
        const form = useForm({
            checks: checks,
            token: props.token,
        })

        return { form }
    },
    computed: {
        selectedPoints() {
            let total = 0;
            this.products.data.forEach(product => {
                if (this.form.checks['id'+product.id]) {
                    total += parseInt(product.point);
                }
            });
            return total;
        }
    },
    methods: {
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        }, 
        submit() {
            let i; let products_count = 0; var point = 0;
            for(i=0; i<this.products.data.length; i++) {
                if (this.form.checks['id'+this.products.data[i]['id']]) {
                    products_count++; point = point + parseInt(this.products.data[i]['point']);
                }
            }

            let content = '';
            if(point>0) {
                content = '選択した'+products_count+'点の商品を'+ point +'ptと交換します。';
            } else {
                content = '全ての商品が「獲得済み商品一覧」に移動します。';
            }
            confirm(content).then((result) => {
                if (result) {
                    this.form.post(route('user.gacha.result.exchange'), {
                        onFinish: () => {
                        },
                    });
                }
            });
        },
        deliver() {
            let i; let products_count = 0;
            for(i=0; i<this.products.data.length; i++) {
                if (this.form.checks['id'+this.products.data[i]['id']]) {
                    products_count++;
                }
            }
            confirm('選択した'+products_count+'点の商品を発送依頼します。').then((result) => {
                if (result) {
                    this.form.post(route('user.gacha.result.deliver'), {
                        onFinish: () => {
                        },
                    });
                }
            });
        },
        checkall() {
            let i;
            for(i=0; i<this.products.data.length; i++) if(this.products.data[i]['status'] == 1) {
                this.form.checks['id'+this.products.data[i]['id']] = this.isCheckAll;
            }
        },
        checkone() {
            this.isCheckAll = this.products.data.length > 0;
            this.products.data.forEach(item => {
                if(this.form.checks['id'+item['id']] != true) {
                    this.isCheckAll = false;
                }
            })
        }
    }
}
</script>