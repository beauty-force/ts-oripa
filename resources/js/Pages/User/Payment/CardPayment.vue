<template>
    <Head title="カード決済" />

    <AdminLayout>
        <div class="flex justify-center items-center flex-1 bg-white py-2">
            <div class="p-6 w-full max-w-md">
                <h2 class="text-center text-xl font-semibold text-gray-800 mb-7">
                    カード決済
                </h2>

                <div v-if="loading" class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-75 z-[20]">
                    <div class="w-16 h-16 bg-white p-4 rounded-full shadow-lg">
                        <svg class="animate-spin h-8 w-8 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Registered Card List -->
                <div v-if="!loading && cards.length" class="mb-6">
                    <h3 class="text-lg font-bold mb-3">登録済みカード</h3>
                    <div v-for="(card, index) in cards" :key="card.id" class="mb-4">
                        <div
                            @click="selectRegisteredCard(card)"
                            :class="{ 'border-sky-500 bg-sky-100 font-bold': selectedCard === card.id, 'hover:bg-gray-100 hover:border-gray-300': selectedCard !== card.id }"
                            class="w-full p-4 bg-white border-2 border-gray-200 rounded-lg flex justify-between items-center transition duration-200 cursor-pointer">
                            <div class="flex items-center gap-4">
                                <!-- Card Brand Icon -->
                                <img
                                    :src="getCardBrandIcon(card.brand)"
                                    :alt="card.brand"
                                    class="w-16"
                                />

                                <!-- Card Details -->
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ formatCardNumber(card.card_no) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        有効期限: {{ formatExpireDate(card.expire) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        名義人: {{ card.holder_name }}
                                    </p>
                                </div>
                            </div>

                            <!-- Delete Button -->
                            <button
                                @click.stop="deleteCard(card.id)"
                                class="text-red-500 hover:text-red-700 text-sm font-semibold"
                            >
                                削除
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="!loading" class="mb-6">
                    <h3 class="text-lg font-bold mb-3">他のカードでお支払い</h3>
                    <button
                        @click="newCardPayment"
                        :class="{ 'border-sky-500 bg-sky-100 font-bold': isNewCard, 'hover:bg-gray-100 hover:border-gray-300': !isNewCard }"
                        class="w-full p-4 bg-white border-2 border-gray-200 rounded-lg flex justify-center items-center transition duration-200">
                        カード情報を入力
                    </button>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="!selectedCard && !isNewCard || loading"
                    @click="proceedToPayment"
                    class="w-full p-4 bg-sky-500 text-white rounded-lg text-lg font-semibold transition duration-200 hover:bg-sky-600 my-4 disabled:opacity-50 disabled:cursor-not-allowed">
                    支払いを続ける
                </button>
                
                <!-- New Card Registration -->
                <div v-if="!loading" class="mt-6">
                    <h3 class="text-lg font-bold mb-3">新しいカードを登録する</h3>
                    <button
                        @click="registerNewCard"
                    class="w-full p-4 bg-orange-500 text-white rounded-lg text-lg font-semibold transition duration-200 hover:bg-orange-600 my-4 disabled:opacity-50 disabled:cursor-not-allowed">
                        新しいカードを登録
                    </button>
                </div>
        
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import { Head, usePage } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/UserLayout.vue';
import { useConfirm } from "@/composables/useConfirm";
import axios from 'axios';

const { confirm } = useConfirm();

export default {
    components: { Head, AdminLayout },
    props: {
        registeredCards: Array, // Pass the list of registered cards from the backend
        point: Object,
        coupon_id: Number,
    },
    data() {
        return {
            selectedCard: null, // ID of the selected registered card
            isNewCard: false, // Whether the user wants to register a new card
            loading: false, // Loading state
            cardPaymentUrl: 'user.point.card_payment_post',
            registerCardUrl: 'user.point.card_register',
            paymentRegisterUrl: 'user.point.paymentRegister',
            deleteCardUrl: 'user.point.deleteCard',
            purchaseSuccessUrl: 'purchase_success',
            cards: this.registeredCards,
        };
    },
    methods: {
        selectRegisteredCard(card) {
            this.selectedCard = card.id;
            this.isNewCard = false;
        },
        newCardPayment() {
            this.selectedCard = null;
            this.isNewCard = true;
        },
        registerNewCard() {
            this.loading = true;
            this.selectedCard = null;
            this.isNewCard = false;
            axios.get(route(this.registerCardUrl, { type: 'purchase' })).then(result => {
                if (result.data.status == 1) {
                    window.location.href = result.data.link_url;
                }
                else {

                }
            });
        },
        proceedToPayment() {
            this.loading = true; // Show loading spinner
            if (this.selectedCard) {
                // Handle payment with the selected registered card
                axios.post(route(this.cardPaymentUrl), {
                    card_id: this.selectedCard,
                    id: this.point.id,
                    coupon_id: this.coupon_id,
                }).then((result) => {
                    // console.log(result);
                    if (result.data.status == 'CAPTURED') {
                        window.location.href = this.purchaseSuccessUrl;
                    }
                    else if (result.data.status == 'AUTHENTICATED') {
                        window.location.href = result.data.acs_url;
                    }
                }).finally(() => {
                    this.loading = false; // Hide loading spinner
                });
            } else if (this.isNewCard) {
                axios.post(route(this.paymentRegisterUrl),
                {
                    id: this.point.id,
                    pay_type: 'Card',
                    coupon_id: this.coupon_id
                }).then(result => {
                    if (result.data.status == 0) {
                        alert(result.data.message);
                    }
                    else {
                        window.location.href = result.data.link_url;
                    }
                });
            }
        },
        deleteCard(cardId) {
            confirm('このカードを削除してもよろしいですか？', '', 'error').then((result) => {
                if (result) {
                    this.loading = true; // Show loading spinner
                    axios.post(route(this.deleteCardUrl), { card_id: cardId }).then((result) => {
                        if (result.data.status == 1) {
                            this.cards = this.cards.filter(card => card.id != cardId);
                        }
                    }).finally(() => {
                        this.loading = false; // Hide loading spinner
                    });
                }
            })
        },
        getCardBrandIcon(brand) {
            return `/images/credit_cards/${brand.toLowerCase()}.svg`;
        },
        formatCardNumber(cardNo) {
            return cardNo.replace(/(.{4})/g, '$1 ').trim();
        },
        formatExpireDate(expire) {
            return `${expire.slice(0, 2)}/${expire.slice(2)}`;
        },
    },
    mounted() {
        if (usePage().props.value.auth.user?.type == 1) {
            this.cardPaymentUrl = 'test.user.point.card_payment_post';
            this.registerCardUrl = 'test.user.point.card_register';
            this.paymentRegisterUrl = 'test.user.point.paymentRegister';
            this.deleteCardUrl = 'test.user.point.deleteCard';
            this.purchaseSuccessUrl = 'test.purchase_success';
        }
    },
};
</script>