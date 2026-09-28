<template>

    <Head title="決済" />
    <AppLayout>
        <div v-if="bank_info" class="w-max max-w-full mx-auto flex flex-col items-center px-4">

            <h3 class="text-center my-5 font-bold text-lg underline underline-offset-4">銀行振り込みの流れ</h3>
            <div class="border p-5 rounded-lg w-full flex flex-col items-center gap-4">
                <div class="flex w-full">
                    <span><strong>1</strong></span>
                    <p class="ml-3 indent-1">振り込み元口座情報の入力</p>
                </div>
                <form class="flex flex-col gap-2">
                    <div class="form-group flex items-center gap-2">
                        <label for="account" class="w-20 block text-right">口座名義 <span
                                class="text-red-500">*</span></label>
                        <input type="text" class="rounded-md" id="account" placeholder="口座名義" v-model="form.account">
                    </div>
                    <div class="form-group flex items-center gap-2">
                        <label for="bank_name" class="w-20 block text-right">銀行名 <span
                                class="text-red-500">*</span></label>
                        <input type="text" class="rounded-md" id="bank_name" placeholder="銀行名" v-model="form.bank_name">
                    </div>
                </form>
            </div>
            <div class="text-center">
                <img :src="'/images/arrow_down.png'" class="h-10" />
            </div>
            <div class="border p-5 rounded-lg w-full flex flex-col items-center gap-4">
                <div class="flex w-full">
                    <span><strong>2</strong></span>
                    <p class="ml-3 indent-1">銀行振り込み</p>
                </div>

                <div class="text-center w-full flex flex-col gap-1">
                    <div class="flex justify-start">
                        <span class="font-bold text-right pr-8 w-2/5">銀行名</span>
                        <span>{{ bank_info.bank_name }}</span>
                    </div>
                    <div class="flex justify-start">
                        <span class="font-bold text-right pr-8 w-2/5">支店コード</span>
                        <span>{{ bank_info.branch_code }}</span>
                    </div>
                    <div class="flex justify-start">
                        <span class="font-bold text-right pr-8 w-2/5">支店名</span>
                        <span>{{ bank_info.branch_name }}</span>
                    </div>
                    <div class="flex justify-start">
                        <span class="font-bold text-right pr-8 w-2/5">口座種類</span>
                        <span>{{ bank_info.account_type }}</span>
                    </div>
                    <div class="flex justify-start">
                        <span class="font-bold text-right pr-8 w-2/5">口座名義</span>
                        <span class="">{{ bank_info.holder_name }}</span>
                    </div>
                    <div class="flex justify-start">
                        <span class="font-bold text-right pr-8 w-2/5">口座番号</span>
                        <span>{{ bank_info.account_number }}</span>
                    </div>
                    <div class="flex justify-start">
                        <span class="font-bold text-right pr-8 w-2/5">金額</span>
                        <span>¥ {{ format_number(amount) }}</span>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <img :src="'/images/arrow_down.png'" class="h-10" />
            </div>
            <div class="border p-5 rounded-lg w-full flex">
                <span><strong>3</strong></span>
                <p class="ml-3 indent-1">運営が振り込み確認後、 ポイントの購入完了<br>ポイント反映までお時間がかかる場合がございます。</p>
            </div>

            <br>

            <div class="mb-6 flex flex-wrap justify-center gap-2">
                <button class="px-8 py-2 rounded-md hover:bg-sky-100 border border-sky-500 text-sky-600"
                    @click.prevent="makeBankPayment()">
                    <span>完了</span>
                </button><br>
                <Link :href="route(purchase_uri)"
                    class="px-8 py-2 rounded-md hover:bg-rose-100 border border-rose-500 text-rose-600">
                戻る
                </Link>
            </div>

        </div>
        <div v-else class="text-center rounded-md flex justify-center">
            <div v-if="!merchant_id" class="mt-8 mb-8 text-center">
                処理中です。
            </div>
            <div id="AmazonPayButton" class="mt-8"></div>
        </div>

    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: { Head, AppLayout, Link },
    props: {
        errors: Object,
        auth: Object,
        checkout: Object,
        point: Object,
        bank_info: Object,
        link_url: String,
        amount: Number,
        coupon_id: Number,
        merchant_id: String,
        public_key_id: String,
        payload: Object,
        signature: String,
        checkoutSessionId: String,
    },
    data() {
        return {
            purchase_bank_uri: 'user.point.purchase_bank',
            purchase_uri: 'user.point',
            is_processing: true,
        }
    },
    mounted() {
        if (this.checkout) window.location.replace(this.checkout.url);
        else if (this.link_url) window.location.replace(this.link_url);
        else {
            const lwaScript = document.createElement('script');
            lwaScript.src = 'https://static-fe.payments-amazon.com/checkout.js';
            lwaScript.onload = this.renderAmazonPayButton;
            document.head.appendChild(lwaScript);
        }
        if (this.auth.user) {
            if (this.auth.user.type == 1) {
                this.is_admin = true;
                this.purchase_bank_uri = 'test.user.point.purchase_bank';
                this.purchase_uri = 'test.user.point';
            }
        }
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
        makeBankPayment() {
            confirm("銀行口座情報を正確に入力しましたか？").then((result) => {
                if (result) {
                    this.form.post(route(this.purchase_bank_uri, { id: this.point.id, coupon_id: this.coupon_id }), {
                        preserveScroll: true,
                        onFinish: () => {
                        },
                    });
                }
            });
        },
        back() {
            window.history.back();
        },
        renderAmazonPayButton() {
            this.is_processing = false;
            amazon.Pay.renderButton('#AmazonPayButton', {
                // set checkout environment
                merchantId: this.merchant_id,
                publicKeyId: this.public_key_id,
                ledgerCurrency: 'JPY',
                // customize the buyer experience
                checkoutLanguage: 'ja_JP',
                productType: 'PayOnly',
                placement: 'Checkout',
                buttonColor: 'Gold',
                buttonWidth: '300px',
                buttonHeight: '50px',
                // configure Create Checkout Session request
                createCheckoutSessionConfig: {
                    payloadJSON: JSON.stringify(this.payload), // string generated in step 2
                    signature: this.signature, // signature generated in step 3
                    algorithm: 'AMZN-PAY-RSASSA-PSS-V2'
                }
            });
        },
    },

    setup(props) {

        const form = useForm({
            point_id: props.point.id,
            account: '',
            bank_name: ''
        })

        return { form }
    },
}

</script>