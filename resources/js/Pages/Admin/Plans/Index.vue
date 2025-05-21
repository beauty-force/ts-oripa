<template>
  <AdminLayout>
    <Head title="プラン管理" />

    <div>
      <div class="mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-3 md:p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium">プラン一覧</h3>
              <Link
                :href="route('admin.plans.create')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                新規プラン作成
              </Link>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 whitespace-nowrap">
                  <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase tracking-wider">
                      プラン名
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase tracking-wider">
                      説明
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase tracking-wider">
                      金額
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase tracking-wider">
                      ボーナス率
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase tracking-wider">
                      操作
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="plan in plans" :key="plan.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ plan.name }}
                    </td>
                    <td class="px-6 py-4">
                      {{ plan.description }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      ¥ {{ plan.amount.toLocaleString() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ plan.point.toLocaleString() }} pt
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <Link
                        :href="route('admin.plans.edit', plan.id)"
                        class="text-indigo-600 hover:text-indigo-900 mr-4"
                      >
                        編集
                      </Link>
                      <button
                        @click="deletePlan(plan)"
                        class="text-red-600 hover:text-red-900"
                      >
                        削除
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/inertia-vue3'
import AdminLayout from '@/Layouts/Admin.vue'
import { useConfirm } from '@/composables/useConfirm'
import { Inertia } from '@inertiajs/inertia';

const { confirm, alert } = useConfirm()
const props = defineProps({
  plans: Array
})

const deletePlan = (plan) => {
  confirm('このプランを削除してもよろしいですか？').then((result) => {
    if (result) {
      Inertia.delete(route('admin.plans.destroy', plan.id))
    }
  })
}
</script> 