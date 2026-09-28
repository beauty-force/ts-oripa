<template>
  <AdminLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        プラン編集
      </h2>
    </template>

    <div class="py-2">
      <div class="mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-3 md:p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                  プラン名
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{ 'border-red-500': form.errors.name }"
                  required
                >
                <p v-if="form.errors.name" class="text-red-500 text-xs italic">
                  {{ form.errors.name }}
                </p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                  説明
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{ 'border-red-500': form.errors.description }"
                  rows="3"
                ></textarea>
                <p v-if="form.errors.description" class="text-red-500 text-xs italic">
                  {{ form.errors.description }}
                </p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="amount">
                  金額（円）
                </label>
                <input
                  id="amount"
                  v-model="form.amount"
                  type="number"
                  min="0"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{ 'border-red-500': form.errors.amount }"
                  required
                >
                <p v-if="form.errors.amount" class="text-red-500 text-xs italic">
                  {{ form.errors.amount }}
                </p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="point">
                  付与ポイント
                </label>
                <input
                  id="point"
                  v-model="form.point"
                  type="number"
                  min="0"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{ 'border-red-500': form.errors.point }"
                  required
                >
                <p v-if="form.errors.point" class="text-red-500 text-xs italic">
                  {{ form.errors.point }}
                </p>
              </div>

              <div class="flex items-center justify-between">
                <button
                  type="submit"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                  :disabled="form.processing"
                >
                  更新
                </button>
                <Link
                  :href="route('admin.plans.index')"
                  class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                >
                  キャンセル
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/inertia-vue3'
import AdminLayout from '@/Layouts/Admin.vue'

const props = defineProps({
  plan: Object
})

const form = useForm({
  name: props.plan.name,
  description: props.plan.description,
  amount: props.plan.amount,
  point: props.plan.point
})

const submit = () => {
  form.put(route('admin.plans.update', props.plan.id))
}
</script> 