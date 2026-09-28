<template>
    <!-- BEGIN: Pagination -->
    <div v-if="total>1" class="flex justify-center items-center">
        <button v-if="page>1" @click="() => { callback(page = 1); }" class="rounded-l-md border w-9 h-9 flex justify-center items-center hover:bg-neutral-200">1</button>
        <span v-if="page>3" class="p-2"><EllipsisHorizontalIcon class="w-5"/></span>
        <button v-if="page>2" @click="() => { callback(page = page - 1); }" class="border w-9 h-9 flex justify-center items-center hover:bg-neutral-200">{{ page - 1 }}</button>
        <button @click="() => { callback(page) }" class="w-9 h-9 flex justify-center items-center text-white bg-neutral-600" :class="{'rounded-l-md': page == 1, 'rounded-r-md': page == total}">{{ page }}</button>
        <button v-if="page+1<total" @click="() => { callback(page = page + 1); }" class="border w-9 h-9 flex justify-center items-center hover:bg-neutral-200">{{ page + 1 }}</button>
        <span v-if="page+2<total" class="p-2"><EllipsisHorizontalIcon class="w-5"/></span>
        <button v-if="page<total" @click="() => { callback(page = total); }" class="rounded-r-md border w-9 h-9 flex justify-center items-center hover:bg-neutral-200">{{ total }}</button>
    </div>
</template>

<script>
    import { ChevronRightIcon, ChevronLeftIcon, EllipsisHorizontalIcon } from '@heroicons/vue/24/outline'
    export default {
        components: {ChevronRightIcon, ChevronLeftIcon, EllipsisHorizontalIcon},
        props: {
            callback: Function,
            total: Number,
        },
        data() {
            return {
                page: 1
            }
        },
        methods: {
        }
    }
</script>