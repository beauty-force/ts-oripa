<template>

    <Head :title="editing ? 'クーポン編集' : 'クーポン追加'" />

    <AppLayout>

        <div class="pt-6 md:px-6 px-4">
            <h1 v-if="editing" class="mb-2 text-lg font-bold">クーポン編集</h1>
            <h1 v-else class="mb-2 text-lg font-bold">クーポン追加</h1>
            <hr class="mb-8" />
            <form @submit.prevent="submit()">
                <input v-model="form.id" class="hidden" />
                <div class="mb-6">
                    <label for="title" class="block font-medium text-sm text-neutral-700 mb-2 ml-1">名前 (テキスト) <span
                            class="text-red-500">*</span></label>
                    <input v-model="form.title" id="title" type="text"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.title" class="text-red-500 text-sm mt-1">
                        {{ errors.title }}
                    </div>
                </div>


                <div class="mb-6">
                    <label for="code" class="block font-medium text-sm text-neutral-700 mb-2 ml-1">コード(テキスト) <span
                            class="text-red-500">*</span></label>
                    <input v-model="form.code" id="code" type="text"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.code" class="text-red-500 text-sm mt-1">
                        {{ errors.code }}
                    </div>
                </div>

                <!-- <div class="mb-6">
                    <label for="type" class="block font-medium text-sm text-neutral-700 mb-2 ml-1">種類 <span
                            class="text-red-500">*</span></label>
                    <select v-model="form.type" id="type"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300">
                        <template v-for="(value, key) in types">
                            <option :value="key">{{ value }}</option>
                        </template>
                    </select>
                </div> -->

                <div v-if="form.type == 'NORMAL'" class="mb-6">
                    <label for="point" class="block font-medium text-sm text-neutral-700 mb-2 ml-1">ポイント (半角数字) <span
                            class="text-red-500">*</span></label>
                    <input v-model="form.point" id="point" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.point" class="text-red-500 text-sm mt-1">
                        {{ errors.point }}
                    </div>
                </div>


                <div v-if="errors.discount_rate" class="text-red-500 text-sm mt-1">
                    {{ errors.discount_rate }}
                </div>
                <template v-if="form.type == 'DISCOUNT'" v-for="(point, key) in points">
                    <div class="mb-6">
                        <label :for="'discount_rate' + key"
                            class="block font-medium text-sm text-neutral-700 mb-2 ml-1">{{ point['point'] }} pt - 割引率
                            (半角数字) <span class="text-red-500">*</span></label>
                        <input v-model="form.discount_rate[key]" :id="'discount_rate' + key" type="number"
                            class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                            placeholder="入力してください" />
                        <div v-if="errors[`discount_rate.${key}`]" class="text-red-500 text-sm mt-1">
                            {{ errors[`discount_rate.${key}`] }}
                        </div>
                    </div>
                </template>

                <div class="mb-6">
                    <label for="expiration" class="block font-medium text-sm text-neutral-700 mb-2 ml-1">有効期限 <span
                            class="text-red-500">*</span></label>
                    <VueDatePicker v-model="form.expiration" format="Y-MM-dd HH:mm"></VueDatePicker>
                    <!-- <input v-model="form.expiration" id="expiration" type="text" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" placeholder="入力してください"/> -->
                    <div v-if="errors.expiration" class="text-red-500 text-sm mt-1">
                        {{ errors.expiration }}
                    </div>
                </div>

                <div class="mb-6">
                    <label for="count" class="block font-medium text-sm text-neutral-700 mb-2 ml-1">点数 (半角数字)
                        <span class="text-red-500">*</span></label>
                    <input v-model="form.count" id="count" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.count" class="text-red-500 text-sm mt-1">
                        {{ errors.count }}
                    </div>
                </div>

                <div class="mb-6">
                    <label for="user_limit" class="block font-medium text-sm text-neutral-700 mb-2 ml-1">一人当上限 (半角数字)
                        <span class="text-red-500">*</span></label>
                    <input v-model="form.user_limit" id="user_limit" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.user_limit" class="text-red-500 text-sm mt-1">
                        {{ errors.user_limit }}
                    </div>
                </div>

                <div class="flex gap-4 mb-4">
                    <div class="w-1/2">
                        <button type="submit"
                            class="inline-block items-center w-full py-2.5 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 active:bg-green-700 transition ease-in-out duration-150">
                            保存
                        </button>
                    </div>

                    <div class="w-1/2">
                        <Link :href="route('admin.coupon')"
                            class="text-center inline-block items-center w-full py-2.5 bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 transition ease-in-out duration-150"
                            as="button">
                        戻る
                        </Link>
                    </div>

                </div>
            </form>



        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, usePage, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    components: { Head, AppLayout, Link, VueDatePicker },
    props: {
        errors: Object,
        auth: Object,
        coupon: Object,
        editing: Object,
        types: Object,
        points: Array
    },
    methods: {
        submit() {
            this.form.post(route('admin.coupon.store'));
        }
    },
    computed: {
        flash() {
            return usePage().props.value.flash;
        }
    },
    watch: {
        flash: function (newVal, oldVal) {
            if (newVal.data) {
                this.form.title = newVal.data.title;
                this.form.type = newVal.data.type;
                this.form.code = newVal.data.code;
                this.form.point = newVal.data.point;
                this.form.discount_rate = newVal.data.discount_rate;
                this.form.expiration = newVal.data.expiration;
                this.form.count = newVal.data.count;
                this.form.user_limit = newVal.data.user_limit;
            }
        }
    },
    setup(props) {
        const form = useForm({
            id: props.coupon.id,
            title: props.coupon.title,
            type: props.coupon.type,
            code: props.coupon.code,
            point: props.coupon.point,
            discount_rate: props.coupon.discount_rate.map(item => { return item.rate }),
            expiration: props.coupon.expiration,
            count: props.coupon.count,
            user_limit: props.coupon.user_limit,
        })
        return { form }
    }
}
</script>