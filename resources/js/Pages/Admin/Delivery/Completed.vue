 <template>
    <Head title="配送商品" />

    <AppLayout>

        <div v-if="page_step==0" class="pt-2 md:px-6 px-4 mb-6">  
            <div class="w-full text-neutral-400 mb-3">
                <Link v-for="(item, key) in main_tab" :href="item.route_url" class="inline-block px-4 py-1.5" :class="{'border-b-2 border-red-600 text-red-600': item.is_active}">
                    {{item.title}}
                </Link>
            </div>
            
            <div class="mb-2 flex justify-between items-center">
                <p>
                    総商品数: {{ totalProducts.toLocaleString() }} 件<br/>
                    総ポイント: {{ totalPoints.toLocaleString() }} PT
                </p>
                <Pagination :search_cond="search_cond" route_name="admin.delivery.completed" :total="total"></Pagination>
            </div>
            
            <div class="flex items-center gap-2 w-full flex-wrap mb-2 py-2 justify-between text-sm">
                <div class="flex items-center gap-2 flex-1">
                    <input v-model="form_search.name" type="text" placeholder="名前、メールアドレス、電話番号、商品名"
                        class="h-9 flex-1 w-0 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" />

                </div>
                <!-- <div class="flex items-center gap-2">
                    <label class="w-16 text-right">レア度</label>
                    <input v-model="form_search.rare" type="text" class="h-9 flex-1 w-0 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"/>
                </div>
                <div class="flex items-center gap-2">
                    <label class="w-16 text-right">ポイント</label>
                    <input v-model="form_search.min" type="number" class="h-9 flex-1 w-16 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"/>
                    <span>~</span>
                    <input v-model="form_search.max" type="number" class="h-9 flex-1 w-16 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"/>
                </div> -->
                <button type="button" @click="search" class="rounded-md border bg-neutral-600 text-white px-4 py-2">検索</button>
            </div>
            <div class="mb-6">
                <div v-for="(item, key) in products.data" class="mb-0  border-neutral-200 border-b py-1">
                    <button @click="showProductDetail(item)" class="flex border-neutral-200 items-center w-full">
                        <img :src="item.image" class="w-20 h-20 inline-block object-contain"/>
                        <div class="cursor-pointer min-h-20 flex items-center justify-between flex-1 text-xs py-1 px-2 ">
                            <div class="text-left">
                                <div>{{item.name}}</div>
                                <div><span class="text-red-500">{{item.point.toLocaleString()}}pt</span> ({{item.rare}})</div>
                                <div>{{item.updated_at}}</div>
                                <div>{{item.address}}</div>
                                <div>追跡番号: {{ item.tracking_number }}</div>
                            </div>
                            <div class="text-neutral-300">
                                <ChevronRightIcon class="w-5"/>
                            </div>
                        </div>
                    </button>
                
                </div>
            </div>
            <Pagination :search_cond="search_cond" route_name="admin.delivery.completed" :total="total"></Pagination>
        </div>

        <div v-if="page_step==1" class="pt-6 md:px-6 px-4 mb-6">  
            <h2 class="mb-2 text-lg font-bold">配送済み商品詳細</h2>
            <hr class="mb-3" />
            <div class="mb-6">
                <div class="flex border-neutral-200 items-center w-full">
                    <img :src="product.image" class="w-20 h-20 inline-block object-contain"/>
                    <div class="cursor-pointer min-h-20 flex items-center justify-between flex-1 text-xs py-1 px-2 ">
                        <div class="text-left">
                            <div>{{product.name}}</div>
                            <div><span class="text-red-500">{{product.point.toLocaleString()}}pt</span> ({{product.rare}})</div>
                            <div>{{product.updated_at}}</div>
                            <div>{{product.address}}</div>
                            <div>追跡番号: {{ product.tracking_number }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-1 text-base font-semibold">配送先情報</div>
            <hr class="mb-3" />
            <div class="w-full mb-6">
                <table class="w-full text-sm">
                    <tr>
                        <td class="p-1 w-2/5 font-bold">アドレス</td>
                        <td class="p-1 w-3/5">{{user.email}}</td>
                    </tr>
                    <template v-if="profile"> 
                        <tr>
                            <td class="p-1 w-2/5 font-bold">名前</td>
                            <td class="p-1 w-3/5">{{profile.first_name}} {{profile.last_name}}</td>
                        </tr>
                        <tr>
                            <td class="p-1 w-2/5 font-bold">名前(カナ)</td>
                            <td class="p-1 w-3/5">{{profile.first_name_gana}} {{profile.last_name_gana}}</td>
                        </tr>
                        <tr>
                            <td class="p-1 w-2/5 font-bold">郵便番号</td>
                            <td class="p-1 w-3/5">{{profile.postal_code}}</td>
                        </tr>
                        <tr>
                            <td class="p-1 w-2/5 font-bold">都道府県</td>
                            <td class="p-1 w-3/5">{{profile.prefecture}}</td>
                        </tr>
                        <tr>
                            <td class="p-1 w-2/5 font-bold">住所</td>
                            <td class="p-1 w-3/5">{{profile.address}}</td>
                        </tr>
                        <tr>
                            <td class="p-1 w-2/5 font-bold">電話番号</td>
                            <td class="p-1 w-3/5">{{profile.phone}}</td>
                        </tr>
                        <tr>
                            <td class="p-1 w-2/5 font-bold">電話番号</td>
                            <td class="p-1 w-3/5">{{profile.phone}}</td>
                        </tr>
                    </template>
                </table>
            </div>
            <div class="mb-6 w-full text-center">
                <button type="button" @click="backToList()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="inline-block items-center px-8 py-2.5 bg-neutral-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150 m-1">
                    リストへ
                </button>
                <button type="button" @click="submit(product.id)" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="inline-block items-center px-8 py-2.5 bg-neutral-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150 m-1">
                    未発送に戻す
                </button>
                
            </div>
        </div>
        
        
    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import { ChevronLeftIcon, ChevronRightIcon, EllipsisHorizontalIcon } from "@heroicons/vue/24/outline"
import Pagination from '@/Parts/Pagination.vue';

export default {
    components: {Head, AppLayout, Link, ChevronLeftIcon, ChevronRightIcon, EllipsisHorizontalIcon, Pagination},
    props: {
        errors: Object,
        gacha: Object,
        category_share:Object,
        products: Object,
        search_cond: Object,
        total: Number,
        totalPoints: Number,
        totalProducts: Number,
    },
    data() {
        return {
            main_tab: [
                { title: "発送依頼中", route_url: route('admin.delivery'), is_active: false },
                { title: "発送済み", route_url: route('admin.delivery.completed'), is_active: true },
                { title: "取得済み", route_url: route('admin.delivery.acquired'), is_active: false },
            ],
            page_step: 0,
            is_busy: false,
            profile: {},
            product: {},
            user: {},
        }
    },
    
    setup(props) {
        let checks = {};
        let i;
        for(i=0; i<props.products.data.length; i++) {
            checks['id'+props.products.data[i]['id']] = false;            
        }
        
        const form_search = useForm(props.search_cond);
        const form = useForm( {
            id: 0,
        })
        return { form, form_search }
    },
    methods: {
        showProductDetail(item) {
            this.is_busy = true
            const post_data = { id: item.id };
            axios.post(route('admin.delivery.product_data'), post_data)
            .then(response=>{ 
                if(response.status == 201 || response.status == 200) {
                    if(response.data.status) {
                        this.profile = response.data.profile;
                        this.user = response.data.user;
                        this.product = item
                        this.page_step = 1;
                    }
                    
                }
                this.is_busy = false;
            }).catch(error=> {
                this.is_busy = true
                console.log(error);
            });
        },

        backToList() {
            this.page_step = 0;
        },  
        submit(id) {
            this.form.id = id;
            this.form.post(route('admin.delivery.un_delivery'), {
                onFinish: () => {
                    this.page_step = 0;
                },
            });
        },

        search() {
            this.form_search.get(route('admin.delivery.completed'));
        },
    },
}
</script>