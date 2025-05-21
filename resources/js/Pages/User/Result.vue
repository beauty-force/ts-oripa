<template>
    <Head title="ガチャ–結果" />

    <AppLayout :is_home="true">
        <div class="md:w-[760px] m-auto items-center px-6 h-full flex flex-col justify-center">
            <!-- Header Section -->
            <div class="sticky top-20 z-20 w-full pt-4 pb-3 bg-white">
                <h1 class="text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent text-center">
                    ガチャ – 結果
                </h1>
            </div>

            <form @submit.prevent="submit()">
                <!-- Products Grid -->
                <div class="mb-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                    <template v-for="(product, key) in products.data">
                        <div class="relative group">
                            <label :for="('checkbox' + product.id)" class="block cursor-pointer">
                                <!-- Product Card -->
                                <div class="transform transition-all duration-200 hover:scale-[1.02]">
                                    <!-- Product Image -->
                                    <div class="relative aspect-square mb-2">
                                        <img class="w-full h-full object-cover rounded-lg shadow-sm" :src="product.image" />
                                        <img v-if="product.badge" :src="product.badge" class="absolute bottom-2 right-2 w-[25%]" :alt="product.badge" />
                                        
                                        <!-- Last Item Effect -->
                                        <div v-if="product.is_last" class="absolute inset-0 z-0 flex items-center justify-center">
                                            <img src="/images/sparking_star.png" class="w-full h-full object-cover animate-pulse">
                                        </div>

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
                                                class="h-4 w-4 cursor-pointer rounded border-sky-400 text-sky-500 shadow-sm focus:ring-sky-500"/>
                                        </div>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="px-1">
                                        <div class="text-neutral-800 font-medium truncate text-center text-sm">
                                            {{product.name}}
                                        </div>
                                        <div class="text-neutral-500 text-xs truncate text-center mt-0.5">
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
                <div class="sticky bottom-0 z-10 py-4 mt-6 bg-white">
                    <!-- Select All -->
                    <div class="text-center mb-3">
                        <label class="cursor-pointer flex items-center justify-center">
                            <input type="checkbox" 
                                v-model="isCheckAll" 
                                @change="checkall()" 
                                class="h-4 w-4 rounded border-neutral-300 text-sky-500 shadow-sm focus:ring-sky-500"/>
                            <span class="ml-2 text-sm font-medium text-neutral-700">全てを選択する</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" 
                            :class="{ 'opacity-50': form.processing }" 
                            :disabled="form.processing" 
                            class="inline-flex items-center px-6 py-2.5 rounded-lg font-medium text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg">
                            <span class="text-base">選択した商品をポイントに変換</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Notice -->
                <div class="pb-6 pt-3 text-center text-xs text-neutral-500">
                    ※選択されなかった商品は「獲得済み 商品一覧」に移動されます。
                </div>
            </form>
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
        checkall() {
            let i;
            for(i=0; i<this.products.data.length; i++) {
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