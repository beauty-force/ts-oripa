<template>
    <div>
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-lg md:text-xl font-bold bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent">
                獲得した商品一覧
            </h1>
        </div>

        <!-- Tab Bar -->
        <div class="bg-white rounded-lg shadow-sm mb-6 overflow-hidden border border-gray-200">
            <div class="flex divide-x divide-gray-200">
                <Link v-for="(item, key) in main_tab" 
                    :key="key"
                    :href="item.route_url" 
                    class="flex-1 relative group"
                    :class="{'bg-sky-50': item.is_active}"
                >
                    <div class="px-4 py-3 text-center transition-all duration-200"
                        :class="{
                            'text-sky-600 font-semibold': item.is_active,
                            'text-gray-600 hover:text-sky-600': !item.is_active
                        }"
                    >
                        <div class="flex items-center justify-center gap-2">
                            <span class="text-sm">{{ item.title }}</span>
                            <span v-if="item.is_active" class="absolute bottom-0 left-0 w-full h-0.5 bg-sky-500"></span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';

export default {
    components: {
        Link
    },
    props: {
        main_tab: {
            type: Array,
            required: true
        }
    }
}
</script> 