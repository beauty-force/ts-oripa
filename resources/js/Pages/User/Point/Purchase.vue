<template>
    <Head title="決済登録" />

    <AdminLayout>
        <div class="min-h-screen bg-gradient-to-b from-sky-50 to-white py-8 px-4">
            <div class="max-w-md mx-auto">
                <!-- Back Button -->
                <div class="-mt-4 mb-4">
                    <Link :href="route('user.point')" 
                        class="inline-flex items-center gap-2 text-neutral-600 hover:text-sky-600 transition-colors group">
                        <div class="w-8 h-8 flex items-center justify-center rounded-full bg-white shadow-sm group-hover:bg-sky-50 transition-colors">
                            <ArrowLeftIcon class="w-5 h-5" />
                        </div>
                        <span class="text-sm font-medium">ポイント一覧に戻る</span>
                    </Link>
                </div>

                <!-- Main Content -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div v-if="payment_stage == 'register'" class="p-6">
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <div class="inline-block bg-sky-50 rounded-full px-4 py-1.5 mb-4">
                                <span class="text-sky-600 text-sm font-medium">ポイント購入</span>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                                {{ format_number(point.point) }} ポイント
                            </h2>
                            <div v-if="rank && rank.pt_rate > 0" 
                                class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-600 px-3 py-1 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium">+{{ format_number(point.point * rank.pt_rate / 100) }} ボーナス</span>
                            </div>
                        </div>

                        <!-- Price Display -->
                        <div class="bg-gradient-to-r from-sky-500 to-sky-600 rounded-xl p-6 mb-8 text-center">
                            <div class="text-white/80 text-sm mb-1">お支払い金額</div>
                            <div class="text-white text-3xl font-bold">
                                &yen;{{ point.amount.toLocaleString() }}
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <form @submit.prevent="handleSubmit">
                            <div class="space-y-3 mb-6">
                                <div v-for="pay_type in supported_pay_type" :key="pay_type">
                                    <button type="button" @click="selectPaymentMethod(pay_type)"
                                        :class="{ 
                                            'ring-2 ring-sky-500 bg-sky-50': selectedMethod === pay_type,
                                            'hover:bg-gray-50': selectedMethod !== pay_type 
                                        }"
                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl flex justify-between items-center transition-all duration-200 hover:shadow-sm">
                                        <span class="font-medium text-gray-900">{{ pay_type_titles[pay_type] }}</span>
                                        <div v-if="pay_type === 'Card'" class="grid grid-cols-3 grid-rows-2 gap-1 w-24 h-10">
                                            <img src="/images/credit_cards/visa.svg" alt="Visa">
                                            <img src="/images/credit_cards/master.svg" alt="Mastercard">
                                            <img src="/images/credit_cards/jcb.svg" alt="JCB">
                                            <img src="/images/credit_cards/amex.svg" alt="American Express">
                                            <img src="/images/credit_cards/diners.svg" alt="Diners Club">
                                            <img src="/images/credit_cards/discover.svg" alt="Discover">
                                        </div>
                                        <img v-if="pay_type === 'amazonpay'" src="/images/payments/amazon-pay.png" alt="Amazon Pay"
                                            class="w-24 h-10 object-contain">
                                        <img v-if="pay_type === 'Applepay'" src="/images/payments/apple-pay.png" alt="Apple Pay"
                                            class="w-24 h-10 object-contain">
                                        <img v-if="pay_type === 'Paypay'" src="/images/payments/paypay.png" alt="PayPay"
                                            class="w-24 h-10 object-contain">
                                        <img v-if="pay_type === 'Konbini'" src="/images/payments/konbini.png" alt="Konbini"
                                            class="w-36 h-10 object-contain">
                                        <img v-if="pay_type === 'Virtualaccount'" src="/images/payments/bank.png"
                                            alt="Bank Transfer" class="w-24 h-10 object-contain">
                                        <img v-if="pay_type === 'Googlepay'" src="/images/payments/google-pay.svg" alt="Google Pay"
                                            class="w-24 h-10 object-contain">
                                    </button>
                                </div>
                            </div>

                            <!-- Terms Agreement -->
                            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" v-model="agreeTerms" 
                                        class="mt-1 focus:ring-sky-500 rounded border-gray-300 text-sky-600" />
                                    <span class="text-sm text-gray-600">
                                        個人情報の取り扱いに同意する
                                        <a :href="route('main.privacy_police')"
                                            class="text-sky-600 hover:text-sky-700 font-medium">取り扱いの詳細を確認する</a>
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" :disabled="selectedMethod == null"
                                class="w-full py-4 bg-gradient-to-r from-sky-500 to-sky-600 text-white rounded-xl text-lg font-semibold transition-all duration-200 hover:from-sky-600 hover:to-sky-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md">
                                支払いを進める
                            </button>
                        </form>
                    </div>

                    <!-- Loading Component -->
                    <Loading :show="loading" />

                    <div v-if="payment_stage == 'Applepay'" class="text-center p-6" id="applepay_container">
                    </div>

                    <div v-if="payment_stage == 'amazonpay'" class="text-center p-6" id="amazonpay_container">
                    </div>

                    <div v-if="payment_stage == 'Googlepay'" class="text-center p-6" id="googlepay_container">
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/UserLayout.vue';
import Loading from '@/Components/Loading.vue';
import axios from 'axios';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import { useConfirm } from "@/composables/useConfirm";

const APPLE_PAY_SUPPORTED_VERSION = 3; // Apple PayJSのサポートバージョン
const MARCHANT_IDENTIFIER = "merchant.com.トレしるオリパ"; // マーチャントID
const APPLE_PAY_BUTTON_ID = "apple-pay-button"; // 任意のApple Payボタン要素のID名
const SHOP_VALIDATION_ENDPOINT_URL = route('apple_pay_validate'); // 任意のマーチャントセッション取得エンドポイントのURL

const { confirm, alert } = useConfirm();

export default {
    components: { Head, AdminLayout, Link, ArrowLeftIcon, Loading },
    props: {
        errors: Object,
        auth: Object,
        point: Object,
        is_admin: Number,
        amount: Number,
        amount_str: String,
        supported_pay_type: Object,
        rank: Object,
        coupon_id: Number,
        no_applepay_support: String,
        no_applepay_setting: String,
    },
    data() {
        return {
            selectedMethod: null,
            agreeTerms: false,
            loading: false,
            payment_stage: 'register',
            pay_type_titles: {
                'Card': 'クレジットカード',
                'Paypay': 'PayPay',
                'Konbini': 'コンビニ払い',
                'Virtualaccount': '銀行振込',
                'Applepay': 'Apple Pay',
                'amazonpay': 'Amazon Pay',
                'Googlepay': 'Google Pay',
            },
        };
    },
    methods: {
        selectPaymentMethod(method) {
            this.selectedMethod = method;
        },
        handleSubmit() {
            if (!this.selectedMethod) {
                alert('支払い方法を選択してください');
                return;
            }
            if (!this.agreeTerms) {
                alert('個人情報の取り扱いに同意する必要があります');
                return;
            }
            if (!this.supported_pay_type.includes(this.selectedMethod)) {
                alert('この支払い方法は近々実装される予定です。');
                return;
            }
            if (this.selectedMethod == 'Applepay' && !window.ApplePaySession) {
                alert(this.no_applepay_support, '', 'error');
                return;
            }
            this.paymentRegister(this.selectedMethod);
        },
        submit() {
        },

        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },

        paymentRegister(pay_type) {
            this.loading = true;

            axios.post(this.is_admin == 1 ? route('test.user.point.paymentRegister') : route('user.point.paymentRegister'),
                {
                    id: this.point.id,
                    pay_type,
                    coupon_id: this.coupon_id
                }).then(result => {
                    if (result.data.status == 0) {
                        alert(result.data.message);
                    }
                    else {
                        if (pay_type == 'Card' || pay_type == 'Paypay' || pay_type == 'Konbini' || pay_type == 'Virtualaccount') {
                            if (result.data.status) {
                                window.location.href = result.data.link_url;
                            }
                        }
                        else if (pay_type == 'Applepay') {
                            this.payment_stage = 'Applepay';
                            this.$nextTick(() => {
                                const applePayButtonContainer = document.getElementById("applepay_container");
                                applePayButtonContainer.innerHTML = "";
                                if (window.ApplePaySession) {
                                    const promise =
                                        ApplePaySession.canMakePaymentsWithActiveCard(MARCHANT_IDENTIFIER);
                                    promise.then((canMakePayment) => {
                                        /* ユーザーが少なくとも1枚の利用可能なカードを登録している場合 */
                                        if (canMakePayment) {
                                            // Apple Payボタンを作成
                                            const applePayButton = document.createElement("button");
                                            applePayButton.setAttribute("id", APPLE_PAY_BUTTON_ID);
                                            applePayButton.setAttribute("lang", "ja-JP");
                                            const applePayButtonType = "buy"; // Apple Payボタンの種類, styleプロパティ -apple-pay-button-type の値
                                            const applePayButtonStyle = "#d68d2f"; // Apple Payボタンのスタイル, styleプロパティ -apple-pay-button-style の値
                                            applePayButton.setAttribute(
                                                "style",
                                                `-webkit-appearance: -apple-pay-button;
                                                -apple-pay-button-type: ${applePayButtonType};
                                                -apple-pay-button-style: ${applePayButtonStyle};
                                                width: 220px;
                                                height: 48px;
                                                text-align: center;
                                                font-size: 18px;
                                                border-radius: 5px;
                                                `
                                            );

                                            // Apple PayボタンをDOMに追加
                                            applePayButtonContainer.appendChild(applePayButton);

                                            // Apple Payボタンをクリックしたときのイベントハンドラ
                                            applePayButton.addEventListener("click", (_) => {
                                                // Apple Pay決済リクエスト
                                                // 参照: https://developer.apple.com/documentation/apple_pay_on_the_web/applepaypaymentrequest
                                                const applePayPaymentRequest = {
                                                    countryCode: "JP",
                                                    currencyCode: "JPY",
                                                    supportedNetworks: ["visa", "masterCard", "jcb", "amex"],
                                                    merchantCapabilities: ["supports3DS"],
                                                    total: {
                                                        label: result.data.response.label,
                                                        amount: result.data.response.amount,
                                                    },
                                                };

                                                // Apple Payセッションを作成
                                                // 参照: https://developer.apple.com/documentation/apple_pay_on_the_web/applepaysession/2320659-applepaysession
                                                const applePaySession = new ApplePaySession(
                                                    APPLE_PAY_SUPPORTED_VERSION,
                                                    applePayPaymentRequest
                                                );

                                                // Apple Payの支払いシートが表示され、マーチャントセッションの取得を求められたときのイベントハンドラ
                                                // 参照: https://developer.apple.com/documentation/apple_pay_on_the_web/applepaysession/1778021-onvalidatemerchant
                                                applePaySession.onvalidatemerchant = (event) => {
                                                    const validationURL = event.validationURL;

                                                    // マーチャントセッションを取得
                                                    fetch(SHOP_VALIDATION_ENDPOINT_URL, {
                                                        method: "POST",
                                                        headers: {
                                                            "Content-Type": "application/json",
                                                        },
                                                        body: JSON.stringify({
                                                            validationURL: validationURL,
                                                        }),
                                                    }).then(response => response.json()).then((response) => {
                                                        // マーチャントセッションをApple Payに返却
                                                        applePaySession.completeMerchantValidation(response);
                                                    });
                                                };

                                                // Apple Payのキャンセルボタンが押されたときのイベントハンドラ
                                                // 参照: https://developer.apple.com/documentation/apple_pay_on_the_web/applepaysession/1778029-oncancel
                                                applePaySession.oncancel = (event) => { };


                                                // 購入者により購入が承認されたときのイベントハンドラ
                                                // 参照: https://developer.apple.com/documentation/apple_pay_on_the_web/applepaysession/1778020-onpaymentauthorized
                                                applePaySession.onpaymentauthorized = (event) => {
                                                    const token = event.payment.token;
                                                    applePaySession.completePayment(ApplePaySession.STATUS_SUCCESS);

                                                    // トークンをBase64エンコード
                                                    // このBase64エンコード済みトークンを決済実行JSの引数もしくは決済実行APIのリクエストボディに含めます
                                                    const encodedToken = btoa(JSON.stringify(token.paymentData));

                                                    axios.post(this.is_admin == 1 ? route('test.user.point.paymentExecute') : route('user.point.paymentExecute'),
                                                        {
                                                            pay_type,
                                                            order_id: result.data.response.id,
                                                            access_id: result.data.response.access_id,
                                                            token: encodedToken
                                                        }).then(result => {
                                                            if (result.data.status == 0) {
                                                                alert(result.data.message);
                                                            }
                                                            else {
                                                                window.location.href = result.data.redirect_uri;
                                                            }
                                                        });
                                                }

                                                // Apple Payセッションを開始
                                                applePaySession.begin();
                                            });
                                        }
                                        else {
                                            applePayButtonContainer.innerHTML = this.no_applepay_setting;
                                        }
                                    });
                                }
                                else {
                                    applePayButtonContainer.innerHTML = this.no_applepay_support;
                                }
                            });
                        }
                        else if (pay_type == 'Googlepay') {
                            const lwaScript = document.createElement('script');
                            lwaScript.src = 'https://pay.google.com/gp/p/js/pay.js';
                            lwaScript.onload = this.renderGooglePayButton.bind(this, result.data.response);
                            document.head.appendChild(lwaScript);
                        }
                        else if (pay_type == 'amazonpay') {
                            this.payment_stage = 'amazonpay';
                            const lwaScript = document.createElement('script');
                            lwaScript.src = 'https://static-fe.payments-amazon.com/checkout.js';
                            lwaScript.onload = this.renderAmazonPayButton.bind(this, result.data.response);
                            document.head.appendChild(lwaScript);
                        }
                    }
                    this.loading = false;
                });

            return;
        },
        renderAmazonPayButton(data) {
            this.loading = false;
            amazon.Pay.renderButton('#amazonpay_container', {
                // set checkout environment
                merchantId: data.merchant_id,
                publicKeyId: data.public_key_id,
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
                    payloadJSON: JSON.stringify(data.payload), // string generated in step 2
                    signature: data.signature, // signature generated in step 3
                    algorithm: 'AMZN-PAY-RSASSA-PSS-V2'
                }
            });
        },
        renderGooglePayButton(data) {
            this.loading = false;
            this.payment_stage = 'Googlepay';
            /**
 * PaymentsClientオブジェクトを初期化
 */
            const paymentsClient = new google.payments.api.PaymentsClient({
                environment: data.google_pay_environment // テスト環境の場合は'TEST'、本番環境の場合は'PRODUCTION'
            });

            /**
             * Google Pay APIバージョンを指定
             */
            const baseRequest = {
                apiVersion: 2,
                apiVersionMinor: 0
            };

            /**
             * 決済トークンをリクエストするための情報を設定
             */
            const tokenizationSpecification = {
                type: 'PAYMENT_GATEWAY', // 固定値: PAYMENT_GATEWAY
                parameters: {
                    gateway: 'fincode', // 固定値: fincode

                    // fincodeのショップIDを指定してください。正しく指定されていない場合、fincodeの決済実行APIでエラーになります。
                    gatewayMerchantId: data.shop_id // fincodeのショップID
                }
            };

            /**
             * ショップの情報を設定
             */
            const merchantInfo = {
                merchantId: data.google_pay_merchant_id, // Google販売者ID
                merchantName: data.google_pay_merchant_name // Google Payシート上に表示されるショップ名 
            };

            /**
             * 取引の情報を設定
             */
            const getTransactionInfo = (amount) => {
                return {
                    currencyCode: 'JPY', // 通貨コード。fincodeではJPYのみ対応。
                    totalPrice: amount, // 決済金額。fincodeでは7桁までの整数値で指定。
                    totalPriceStatus: 'FINAL' // 決済金額が確定している場合は`FINAL`を指定。
                }
            };

            /**
             * 利用するカード認証方法とカードブランドを指定
             */
            // allowedCardAuthMethods: 'PAN_ONLY'のみ対応。'CRYPTOGRAM_3DS'には非対応です。
            const allowedCardAuthMethods = ['PAN_ONLY'];
            const allowedCardNetworks = ['VISA', 'MASTERCARD', 'JCB', 'AMEX', 'DISCOVER'];

            /**
             * ユーザーから収集する請求先住所情報の設定
             */
            const billingAddressParameters = {
                // format: 未指定の場合、デフォルトで'MIN'が設定されます。
                format: 'FULL', // 任意: 'MIN'または'FULL'

                // phoneNumberRequired: 未指定の場合、デフォルトでfalseが設定されます。
                phoneNumberRequired: true // 任意: 電話番号を要求する場合はtrue
            };

            /**
             * 決済手段情報を設定
             */
            const baseCardPaymentMethod = {
                type: 'CARD',
                parameters: {
                    allowedAuthMethods: allowedCardAuthMethods,
                    allowedCardNetworks: allowedCardNetworks,
                    billingAddressParameters: billingAddressParameters
                }
            };
            const cardPaymentMethod = Object.assign(
                { tokenizationSpecification: tokenizationSpecification },
                baseCardPaymentMethod
            );

            /**
             * 決済データリクエストを設定
             */
            const paymentDataRequest = Object.assign({}, baseRequest);
            paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
            paymentDataRequest.transactionInfo = getTransactionInfo(data.amount);
            paymentDataRequest.merchantInfo = merchantInfo;

            /**
             * Google PayボタンをDOMに追加する関数
             * @param {HTMLElement} mountTarget Google Payボタンを追加するDOM要素
             */
            const addGooglePayButton = (
                mountTarget, // Google Payボタンを追加するDOM要素
                onClick // Google Payボタンがクリックされたときのコールバック関数
            ) => {
                const button = paymentsClient.createButton({
                    onClick: onClick, // Google Payボタンがクリックされたときのコールバック関数
                    buttonColor: 'default', // ボタンの色を指定
                    buttonType: 'long', // ボタンの形状を指定
                });

                mountTarget.appendChild(button);
            };

            /**
             * Google Payボタンのクリックイベントハンドラ
             * 
             * この関数内でfincodeの決済実行JSまたは決済実行APIに渡す決済トークンを取得します。
             */
            const onGooglePayButtonClick = async () => {
                try {
                    const paymentData = await paymentsClient.loadPaymentData(paymentDataRequest);

                    const token = paymentData.paymentMethodData.tokenizationData.token;

                    // トークンをBase64エンコード
                    // [TODO] Base64エンコード済みのencodedTokenをfincodeの決済実行JSまたは決済実行APIに渡して決済処理を実行します。
                    const encodedToken = btoa(token);

                    this.loading = true;
                    axios.post(this.is_admin == 1 ? route('test.user.point.paymentExecute') : route('user.point.paymentExecute'),
                        {
                            pay_type: 'Googlepay',
                            order_id: data.id,
                            access_id: data.access_id,
                            method: "1",
                            token: encodedToken
                        }).then(result => {
                            if (result.data.status == 0) {
                                alert(result.data.message);
                            }
                            else {
                                // console.log(result.data);
                                window.location.href = result.data.redirect_uri;
                            }
                            this.loading = false;
                        });

                } catch (err) {
                    // 何らかの理由によりGoogle Payの決済トークン取得に失敗
                    console.error(err);
                }
            };

            /**
             * 決済可能かどうかを確認する
             *
             */
            const isReadyToPayRequest = Object.assign({}, baseRequest);
            isReadyToPayRequest.allowedPaymentMethods = [baseCardPaymentMethod];

            paymentsClient.isReadyToPay(isReadyToPayRequest)
                .then((response) => {
                    if (response.result) {
                        // このとき、Google Payが利用可能
                        // Google Payボタンを表示する
                        const mountTarget = document.getElementById('googlepay_container'); // Google Payボタンを追加するDOM要素
                        mountTarget.innerHTML = "";
                        addGooglePayButton(mountTarget, onGooglePayButtonClick);
                    }
                })
                .catch((err) => {
                    // Google Payが何らかの理由で利用不可
                    // 参照： https://developers.google.com/pay/api/web/reference/error-objects#PaymentsError
                    console.error(err);
                });
        },
    },
    mounted() {
        
    },
}
</script>
