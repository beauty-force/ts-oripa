<template>
    <Head title="設定" />

    <AppLayout>
        <div class="mt-6 md:px-5 px-3">  

            <h3 class="mb-2 md:text-lg font-bold">メンテナンスの設定</h3>
            <hr class="mb-4" />
            <div class="mb-6">
                <table>
                    <tbody>
                        <tr>
                            <td>メンテナンス</td>
                            <td class="px-3  text-cyan-500 text-sm">
                                <div @click="changeMaintainance('1')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-l-md w-16 py-1" :class="{'bg-cyan-500 text-white':form.maintainance=='1'}">On</div> 
                                <div @click="changeMaintainance('0')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-r-md w-16 py-1" :class="{'bg-cyan-500 text-white':form.maintainance!='1'}">Off</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center mb-6">
                <button type="button" @click="submit()" class="inline-block items-center px-10 py-2.5 bg-cyan-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150 mr-2">
                    保存する
                </button>
            </div>
    
            <h3 class="mb-2 md:text-lg font-bold">決済の設定</h3>
            <hr class="mb-4" />
            <div class="mb-6">
                <table>
                    <tbody>
                        <tr>
                            <td>決済モード</td>
                            <td class="px-3  text-cyan-500 text-sm">
                                <div @click="changeTestOrLive('live')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-l-md w-16 py-1" :class="{'bg-cyan-500 text-white':form_payment.testOrLive=='live'}">ライブ</div> 
                                <div @click="changeTestOrLive('test')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-r-md w-16 py-1" :class="{'bg-cyan-500 text-white':form_payment.testOrLive!='live'}">テスト</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-6">
                <table>
                    <tbody>
                        <tr>
                            <td>3Dセキュア</td>
                            <td class="px-3  text-cyan-500 text-sm">
                                <div @click="change3DSecure('1')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-l-md w-16 py-1" :class="{'bg-cyan-500 text-white':form_payment.is3DSecure=='1'}">適用</div> 
                                <div @click="change3DSecure('0')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-r-md w-16 py-1" :class="{'bg-cyan-500 text-white':form_payment.is3DSecure!='1'}">非適用</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-6 text-center">
                テスト用決済ページ: <a target="_blank" :href="route('test.user.point')" class="text-cyan-600 hover:text-cyan-900"> {{ route('test.user.point') }}</a>
            </div>
            <div class="text-center mb-6">
                <button type="button" @click="submit_payment()" class="inline-block items-center px-10 py-2.5 bg-cyan-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150 mr-2">
                    保存する
                </button>
            </div>

            <h3 class="mb-2 md:text-lg font-bold">友達紹介ボーナス設定</h3>
            <hr class="mb-4" />
            <div class="mb-6">
                <table class="w-full text-xs md:text-base">
                    <tbody>
                        <tr>
                            <td>紹介者への報酬</td>
                            <td class="pl-3  text-cyan-500 text-sm py-1">
                                <input type="number" v-model="form_invitation.invite_bonus" class="text-neutral-700 h-10 w-full border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"/>
                            </td>
                        </tr>
                        <tr>
                            <td>登録者への報酬</td>
                            <td class="pl-3 text-cyan-500 text-sm py-1">
                                <input type="number" v-model="form_invitation.invited_bonus" class="text-neutral-700 h-10 w-full border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center mb-6">
                <button type="button" @click="submit_invitation()" class="inline-block items-center px-10 py-2.5 bg-cyan-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150 mr-2">
                    保存する
                </button>
            </div>

            <!-- <h3 class="mb-2 md:text-lg font-bold">発送設定</h3>
            <hr class="mb-4" />
            <div class="mb-6">
                <table class="w-full text-xs md:text-base">
                    <tbody>
                        <tr>
                            <td>発送最低ｐｔ</td>
                            <td class="pl-3  text-cyan-500 text-sm py-1">
                                <input type="number" v-model="form_delivery.delivery_limit" class="text-neutral-700 h-10 w-full border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center mb-6">
                <button type="button" @click="submit_delivery()" class="inline-block items-center px-10 py-2.5 bg-cyan-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150 mr-2">
                    保存する
                </button>
            </div> -->

        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';

import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';

export default {
    components: {Head, AppLayout, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot},
    props: {
        errors: Object,
        maintainance:String,
        testOrLive:String,
        is3DSecure:String,
        has3DChallenge:String,
        invite_bonus:Number,
        invited_bonus:Number,
        delivery_limit:Number,
    },
    data() {
        return {
        }
    },
    methods: {
        changeMaintainance(value) {
            this.form.maintainance = value;
        },
        changeTestOrLive(value) {
            this.form_payment.testOrLive = value;
        },
        change3DSecure(value) {
            this.form_payment.is3DSecure = value;
        },
        change3DChallenge(value) {
            this.form_payment.has3DChallenge = value;
        },
        submit() {
            this.form.post(route('admin.settings.maintenance_store'), {
                onFinish: () => {
                    
                },
            }); 
        },  
        submit_payment() {
            this.form_payment.post(route('admin.settings.payment_store'), {
                onFinish: () => {
                    
                },
            }); 
        },
        submit_invitation() {
            this.form_invitation.post(route('admin.settings.payment_store'), {
                onFinish: () => {
                    
                },
            }); 
        },
        submit_rank_bonus() {
            this.form_rank_bonus.post(route('admin.settings.payment_store'), {
                onFinish: () => {
                    
                },
                preserveScroll: true
            }); 
        },
        submit_delivery() {
            this.form_delivery.post(route('admin.settings.payment_store'), {
                onFinish: () => {
                    
                },
                preserveScroll: true
            });
        }
    },
    setup(props) {
        let data = {
            maintainance: props.maintainance,
        };
        const form = useForm( data );

        let data_payment = {
            testOrLive: props.testOrLive,
            is3DSecure: props.is3DSecure,
            has3DChallenge: props.has3DChallenge,
        };
        const form_payment = useForm( data_payment );
        
        const form_invitation = useForm({
            invite_bonus: props.invite_bonus,
            invited_bonus: props.invited_bonus,
        });

        const form_delivery = useForm({
            delivery_limit: props.delivery_limit,
        })

        return { form, form_payment, form_invitation, form_delivery }
    },
    mounted() {
    },
}
</script>