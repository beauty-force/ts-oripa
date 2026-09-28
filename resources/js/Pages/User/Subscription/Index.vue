<template>
  <AppLayout>

    <Head title="サブスクリプション" />

    <Loading :show="isLoading" :message="'処理中...'" />

    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden">
          <div class="p-6 bg-white border-gray-200">
            <h3 v-if="currentSubscription" class="text-lg font-semibold mb-6">現在のサブスクリプション</h3>
            <div v-if="currentSubscription" class="mb-8 p-6 bg-blue-50 rounded-lg">
              <div class="flex flex-wrap justify-between md:grid md:grid-cols-3 gap-4">
                <div>
                  <p class="text-sm text-blue-600">プラン名</p>
                  <p class="font-medium">{{ currentSubscription.plan.name }}</p>
                </div>
                <div>
                  <p class="text-sm text-blue-600">開始日</p>
                  <p class="font-medium">{{ formatDate(currentSubscription.start_date) }}</p>
                </div>
                <div>
                  <p class="text-sm text-blue-600">付与ポイント</p>
                  <p class="font-medium">{{ formatNumber(currentSubscription.plan.point) }} pt</p>
                </div>
              </div>
              <div class="flex flex-wrap items-end justify-between mt-3">
                <template v-for="card in cards">
                  <div v-if="currentSubscription.card_id === card.id"
                    class="flex items-center py-2">
                    <div class="flex items-center gap-1">
                      <img :src="getCardBrandImage(card.brand)" :alt="card.brand" class="w-20 h-12 mr-2">
                      <div>
                        <p class="font-medium">{{ formatCardNumber(card.card_no) }}</p>
                        <p class="text-sm text-gray-500">有効期限: {{ formatExpireDate(card.expire) }}</p>
                        <p class="text-xs text-gray-500">名義人: {{ card.holder_name }}</p>
                      </div>
                    </div>
                  </div>
                </template>
                <form @submit.prevent="cancelSubscription()" class="mt-4">
                  <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 rounded text-sm"
                    :disabled="form.processing">
                    解約
                  </button>
                </form>
              </div>
            </div>

            <h3 class="text-lg font-semibold mb-6">利用可能なプラン</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div v-for="plan in plans" :key="plan.id" class="border rounded-lg p-6"
                :class="{ 'bg-sky-50 border-sky-500 border-2': currentSubscription?.plan_id === plan.id }">
                <h4 class="text-xl font-bold mb-2">{{ plan.name }}</h4>
                <p class="text-gray-600 mb-4">{{ plan.description }}</p>
                <div class="mb-4">
                  <p class="text-xl font-bold leading-8">¥{{ formatNumber(plan.amount) }}<span
                      class="text-sm font-normal">/月</span></p>
                  <p class="text-green-600 text-sm">{{ formatNumber(plan.point) }} ポイント付与</p>
                  <p class="text-rose-500 text-sm leading-7 font-bold">{{ formatNumber(plan.point - plan.amount) }} ptお得!!</p>
                </div>
                <div>
                  <form @submit.prevent="openCardSelection(plan)">
                    <button type="submit"
                      class="w-full bg-theme bg-theme-hover text-white font-bold py-2 px-4 rounded text-sm"
                      :disabled="form.processing">
                      {{ !currentSubscription ? 'サブスクライブ' : (currentSubscription.plan_id != plan.id ? 'プラン変更' : 'カード変更') }}
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card Selection Modal -->
    <TransitionRoot :show="showCardModal" class="">
      <TransitionChild enter="transition ease-in-out duration-200 transform" enter-from="translate-y-full"
        enter-to="translate-y-0" leave="transition ease-in-out duration-200 transform" leave-from="translate-y-0"
        leave-to="translate-y-full" as="template">
        <div class="fixed inset-0 z-40" @click="closeCardModal">
          <div class="flex flex-col text-neutral-700 rounded relative z-20 top-20 w-fit min-w-64 px-4 bg-neutral-50 border-l border-neutral-200 m-auto max-w-[90%] py-2" @click.stop>
            <div class="py-3 px-1 text-center">
              <div class="font-bold text-lg">{{ selectedPlan?.name }}</div>
              <div class="text-lg font-bold text-theme mt-1">¥ {{ formatNumber(selectedPlan?.amount) }}<span
                  class="text-sm font-normal">/月</span></div>
            </div>

            <div class="p-2">
              <div v-if="cards.length > 0" class="text-sm text-gray-500 mb-2">お支払いカードを選択してください</div>
              <div v-if="cards.length > 0" class="space-y-2">
                <template v-for="card in cards" :key="card.id">
                  <div v-if="!currentSubscription || selectedPlan && selectedPlan.id != currentSubscription.plan_id || card.id != currentSubscription.card_id"
                    :class="{ 'border-sky-500 bg-sky-50 border-2': selectedCard === card.id, 'hover:bg-gray-50 hover:border-gray-300': selectedCard !== card.id }"
                    class="flex items-center p-2 border rounded cursor-pointer" @click="selectedCard = card.id">
                    <input type="radio" :id="'card-' + card.id" :value="card.id" v-model="selectedCard"
                      class="mr-2 focus:ring-0 focus:ring-offset-0">
                    <label :for="'card-' + card.id" class="flex-1 cursor-pointer">
                      <div class="flex items-center">
                        <img :src="getCardBrandImage(card.brand)" :alt="card.brand" class="w-20 h-12 mr-2">
                        <div>
                          <p class="font-medium">{{ formatCardNumber(card.card_no) }}</p>
                          <p class="text-sm text-gray-500">有効期限: {{ formatExpireDate(card.expire) }}</p>
                          <p class="text-xs text-gray-500">名義人: {{ card.holder_name }}</p>
                        </div>
                      </div>
                    </label>
                  </div>
                </template>
              </div>
              <div v-else class="text-gray-500 mb-2">
                登録済みのカードがありません
              </div>
            </div>

            <hr class="my-1" />
            <button @click="registerCard" class="text-blue-500 hover:text-blue-700 text-sm p-2">
              + 新しいカードを登録
            </button>
            <div class="flex justify-between p-2 gap-2">
              <button @click="closeCardModal" class="bg-gray-300 text-gray-700 flex-1 py-2 rounded text-sm">
                キャンセル
              </button>
              <button @click="confirmSubscription" class="bg-theme text-white flex-1 py-2 rounded text-sm"
                :class="{ 'opacity-50': !selectedCard }"
                :disabled="!selectedCard">
                確認
              </button>
            </div>
          </div>
        </div>
      </TransitionChild>
      <TransitionChild enter="transition-opacity ease-linear duration-200" enter-from="opacity-0" enter-to="opacity-100"
        leave="transition-opacity ease-linear duration-200" leave-from="opacity-100" leave-to="opacity-0"
        class="fixed inset-0 z-30">
        <div class="bg-neutral-600 h-full bg-opacity-50" @click="closeCardModal"></div>
      </TransitionChild>
    </TransitionRoot>
  </AppLayout>
</template>

<script setup>
import { useForm, Head } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/UserLayout.vue'
import { ref } from 'vue'
import { TransitionRoot, TransitionChild } from '@headlessui/vue'
import axios from 'axios';
import { useConfirm } from "@/composables/useConfirm";
import Loading from '@/Components/Loading.vue'

const { confirm } = useConfirm();

const props = defineProps({
  plans: Array,
  currentSubscription: Object,
  cards: Array
})

const form = useForm({})

const showCardModal = ref(false)
const selectedCard = ref('')
const selectedPlan = ref(null)
const isLoading = ref(false)


const formatNumber = (number) => {
  return new Intl.NumberFormat('ja-JP').format(number)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ja-JP')
}

const getCardBrandImage = (brand) => {
  return `/images/credit_cards/${brand.toLowerCase()}.svg`;
}

const formatCardNumber = (cardNo) => {
  return cardNo.replace(/(.{4})/g, '$1 ').trim();
}

const formatExpireDate = (expire) => {
  return `${expire.slice(0, 2)}/${expire.slice(2)}`;
}

const cancelSubscription = () => {
  confirm('解約しますか？', '解約後、1ヶ月間登録はできません。').then(result => {
    if (result) {
      isLoading.value = true
      form.post(route('user.subscription.cancel', props.currentSubscription.id), {
        onFinish: () => {
          isLoading.value = false
        }
      })
    }
  })
}

const registerCard = () => {
  isLoading.value = true
  axios.get(route('user.point.card_register', { type: 'subscription' })).then(result => {
    isLoading.value = false
    if (result.data.status == 1) {
      window.location.href = result.data.link_url;
    }
  }).catch(() => {
    isLoading.value = false
  });
}

const openCardSelection = (plan) => {
  selectedPlan.value = plan
  showCardModal.value = true
}

const closeCardModal = () => {
  showCardModal.value = false
  selectedCard.value = ''
  selectedPlan.value = null
}

const confirmSubscription = () => {
  if (selectedPlan.value && selectedCard.value) {
    if (props.currentSubscription) {
      confirm('プランを変更しますか？', '<div class="text-start space-y-2">\
<div>•　アップグレードの場合、現在のプランとの差額がすぐに請求され、請求日も本日に更新されます。</div>\
<div>•　ダウングレードまたは支払いカードを変更する場合、請求日は変わらず、次回の請求時に新しいプランの料金が適用されます。</div>\
</div>').then(result => {
        if (result) {
          isLoading.value = true
          useForm({
            plan_id: selectedPlan.value.id,
            card_id: selectedCard.value,
          }).post(route('user.subscription.update', props.currentSubscription.id), {
            onFinish: () => {
              isLoading.value = false
              closeCardModal()
            },
            preserveScroll: true,
          })
        }
      })
    }
    else {
      confirm('サブスクリプションに登録しますか？').then(result => {
        if (result) {
          isLoading.value = true
          useForm({
            card_id: selectedCard.value,
          }).post(route('user.subscription.subscribe', selectedPlan.value.id), {
            onFinish: () => {
              isLoading.value = false
              closeCardModal()
            },
            preserveScroll: true,
          })
        }
      })
    }
  }
}

</script>