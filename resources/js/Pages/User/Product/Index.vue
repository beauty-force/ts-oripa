<template>
    <Head title="獲得した商品一覧" />

    <AppLayout>
        <div class="">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <ProductHeader :main_tab="main_tab" />

                <!-- Product Grid -->
                <div v-if="ready_delivery==0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="(item, key) in products.data" 
                        :key="key" 
                        class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100"
                    >
                        <div class="p-3 overflow-hidden" :class="{'bg-sky-50 border-sky-200 border': form.checks['id'+item.id]}">
                            <div class="flex items-start gap-4 relative">
                                <input :id="'checkbox' + item.id" 
                                    v-model="form.checks['id'+item.id]" 
                                    type="checkbox" 
                                    @change="changeCheck()" 
                                    class="mt-1 cursor-pointer rounded-full border-sky-300 text-sky-600 focus:ring-sky-500 absolute top-0 right-0"
                                />
                                
                                <label :for="'checkbox' + item.id" class="flex-1 cursor-pointer">
                                    <div class="flex gap-2">
                                        <div class="relative">
                                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-2">
                                                <img :src="item.image" class="h-full w-20 object-contain rounded-lg"/>
                                            </div>
                                            <img v-if="item.badge" 
                                                :src="item.badge" 
                                                class="absolute -top-2 -right-2 w-[35%] drop-shadow-lg" 
                                                :alt="item.badge" 
                                            />
                                        </div>
                                    
                                        <div class="flex-1 space-y-2">
                                            <div class="flex items-center gap-2">
                                                <img v-if="item.rank > 0" 
                                                    :src="`/images/badge${item.rank}.png`" 
                                                    class="h-6 drop-shadow-sm"
                                                />
                                                <div class="text-sm font-medium text-gray-700 px-2">{{item.name}}</div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="text-sm font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded-full inline-block">{{item.rare}}</div>
                                                <div v-if="item.is_lost_product!=2" 
                                                    class="inline-flex items-center bg-gradient-to-r from-sky-500 to-sky-400 text-white px-3 py-1 rounded-full text-sm font-medium shadow-sm"
                                                >
                                                    <img src="/images/icon_cash.png" class="h-4 mr-1.5" />
                                                    <span>{{item.point.toLocaleString()}}&nbsp;PT</span>
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded-lg border border-gray-100">
                                                <span class="font-medium text-gray-500">保有期限:&nbsp;</span> {{ item.expiration }}
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="sticky bottom-0 bg-white p-4">
                    <div class="bg-white">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                    v-model="isCheckAll" 
                                    @change="checkall()"
                                    class="h-5 w-5 cursor-pointer rounded border-sky-400 text-sky-600 focus:ring-sky-500" 
                                />
                                <span class="ml-2 font-medium text-sky-600">全てを選択する</span>
                            </label>

                            <div class="flex gap-3">
                                <button type="button" 
                                    @click="submit('point')" 
                                    :disabled="form.processing || (!hasCheck)"
                                    class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-sky-600 text-white rounded-lg font-medium text-sm transition-all duration-200 hover:from-sky-600 hover:to-sky-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    ポイントに換える
                                </button>
                                <button type="button" 
                                    @click="submit('delivery')"
                                    :disabled="form.processing || (!hasCheck)"
                                    class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg font-medium text-sm transition-all duration-200 hover:from-emerald-600 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    発送する
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="ready_delivery==1" class="pt-6 md:px-6 px-4">  
            <h2 class="mb-2 text-lg font-bold">配送内容の確認</h2>
            <hr class="mb-6" />
            <div class="mb-3 text-sm">配送商品 （計{{products_count}}点）</div>
            <div class="mb-8 grid md:grid-cols-2">
                <template v-for="(item, key) in products.data">
                    <div  v-if="form.checks['id'+item.id]" class="mb-0  border-neutral-200 border-b p-1">
                        <div class="flex border-neutral-200 items-center">
                            <img :src="item.image" class="h-20 inline-block object-contain"/>
                            <label :for="'checkbox' + item.id" class="cursor-pointer h-20 flex flex-col justify-evenly flex-1 text-xs py-1 px-2">
                                <div>{{item.name}}</div>
                                <div>{{item.rare}}</div>
                                <div class="text-red-500">{{item.point}}pt</div>
                            </label>
                        </div>
                    </div>
                </template>
            </div>
            <div class="mb-2 text-sm">配送先情報</div>
            <div class="mb-8 border border-neutral-200 rounded-md px-4 py-2 flex items-center justify-between">
                <div class="flex-1">
                    <template v-if="profile.id">
                        <p class="font-bold text-sm">{{ profile.first_name + profile.last_name }}</p>
                        <p class="text-sm">〒{{ profile.postal_code }}</p>
                        <p class="text-sm">{{ profile.address }}</p>
                    </template>
                </div>
                <div>
                    <Link :href="route('user.address')" type="button" @click="back_delivery()" :class="{ 'opacity-25': form.processing || (!hasCheck) }" :disabled="form.processing || (!hasCheck)" class="inline-block items-center px-5 py-1.5 bg-neutral-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150 m-1">
                        編集
                    </Link>
                </div>
            </div>
            
            <div class="mb-8 text-center text-neutral-50">
                <button type="button" @click="back_delivery()" :class="{ 'opacity-25': form.processing || (!hasCheck) }" :disabled="form.processing || (!hasCheck)" class="w-32 inline-block items-center px-1 py-2 rounded-md font-semibold text-sm text-white uppercase tracking-widest bg-neutral-600 hover:bg-neutral-700 transition ease-in-out duration-150 m-1">
                    戻る
                </button>
                <button type="button" @click="submit_delivery()" :class="{ 'opacity-25': form.processing || (!hasCheck) }" :disabled="form.processing || (!hasCheck)" class="w-32 inline-block items-center px-1 py-2 rounded-md font-semibold text-sm text-white uppercase tracking-widest bg-theme bg-theme-hover transition ease-in-out duration-150 m-1">
                    発送する
                </button>
            </div>
        </div>

        <div v-if="ready_delivery==2" class="pt-6 md:px-6 px-4">  
            <h2 class="mb-2 text-lg font-bold">配送手続き完了</h2>
            <hr class="mb-8" />
            <div class="mb-8 ">
                計{{products_count}}点の商品の配送手続きが完了しました。<br/><br/>
                商品の発送には<span class="font-bold">数日~数週間</span>かかる可能性がございます。<br/>
                ご了承のほどよろしくお願い致します。
            </div>
           
            
            <div class="mb-6 text-center text-neutral-50 flex flex-wrap justify-center gap-2">
                <Link :href="route('main')" class="w-48 inline-block items-center px-1 py-2 bg-red-600 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 m-1">
                    ガチャページへ
                </Link>

                <button type="button" @click="back_delivery()" class="w-48 inline-block items-center px-1 py-2 rounded-md font-semibold text-sm text-white uppercase tracking-widest bg-theme bg-theme-hover transition ease-in-out duration-150 m-1">
                    獲得した商品一覧へ
                </button>
                
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import ProductHeader from '@/Components/ProductHeader.vue';

import { useConfirm } from "@/composables/useConfirm";

const { confirm, alert } = useConfirm();

export default {
    components: {
        Head, 
        AppLayout, 
        Link,
        ProductHeader
    },
    props: {
        errors: Object,
        gacha: Object,
        category_share:Object,
        products: Object,
        profile: Object,
        delivery_limit: Number,
    },
    data() {
        return {
            hasCheck: false,
            main_tab : [
                {title:"未選択", route_url: route('user.products'), is_active:true},
                {title:"発送待ち", route_url: route('user.products.wait'), is_active:false},
                {title:"発送済み", route_url: route('user.products.delivered'), is_active:false},
            ],
            ready_delivery: 0,
            products_count: 0,
            isCheckAll: false,
            _products: this.products.data.filter(p => p.status == 1)
        }
    },
    setup(props) {
        let checks = {};
        
        const form = useForm( {
            checks: checks,
        })
        return { form }
    },
    methods: {
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        },
        changeCheck() {
            let i; let hasCheckLocal = false;
            this._products = this.products.data.filter(p => p.status == 1)
            this.isCheckAll = this._products.length > 0;
            for(i=0; i<this._products.length; i++) {
                if (this.form.checks['id'+this._products[i]['id']]) {
                    hasCheckLocal = true;
                }
                else {
                    this.isCheckAll = false;
                }
            }
            this.hasCheck = hasCheckLocal;
        },
        submit(submit_type) {
            let i; let products_count = 0; var point = 0;
            let point_product_count = 0;
            for(i=0; i<this._products.length; i++) {
                if (this.form.checks['id'+this._products[i]['id']]) {
                    products_count++; point = parseInt(point) + parseInt(this._products[i]['point']);
                    if(this._products[i]['rare'] == 'ポイント') {
                        point_product_count++;
                    }
                }
            }
            
            let need_submit = false;

            if(submit_type=="point") {
                
                if(point>0) {
                    confirm('交換しますか？ 選択した'+products_count+'点の商品を'+ this.format_number(point) +'ptと交換します。').then((result) => {
                        if (result) {
                            this.form.post(route('user.products.point.exchange'), {
                                onFinish: () => {
                                    this.form.checks = {};
                                    this.hasCheck = false;
                                    this.isCheckAll = false;
                                },
                            });
                        }
                    });
                } 
            } else {
                if (point_product_count > 0) {
                    alert('ポイントは発送できません');
                    return ;
                }
                if(products_count>0) {
                    if (point < this.delivery_limit) {
                        alert('発送依頼ができる最低ポイントは'+ this.format_number(this.delivery_limit) +'ptです。');
                        return;
                    }
                    this.products_count = products_count;
                    this.ready_delivery = 1;
                    
                } 
            }
        },
        back_delivery() {
            this.ready_delivery = 0;
        },  
        submit_delivery() {
            if(this.profile.address) {
                confirm(''+ this.products_count + '点の商品を配送しますか？').then((result) => {
                    if (result) {
                        this.form.post(route('user.delivery.post'), {
                            onFinish: () => {
                                this.ready_delivery = 2;
                                this.form.checks = {};
                                this.hasCheck = false;
                                this.isCheckAll = false;
                            },
                        });
                    }
                });
            } else {
                alert('入力エラー、配送先情報を入力してください。')
            }
            
        },
        checkall() {
            let i;
            this.hasCheck = false;
            
            for(i=0; i<this._products.length; i++) {
                this.form.checks['id'+this._products[i]['id']] = this.isCheckAll;
                this.hasCheck = this.isCheckAll;
            }
        },
        
    },
}
</script>