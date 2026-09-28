<template>

    <Head title="ページ編集" />

    <AppLayout>

        <div class="pt-4 px-2 md:px-4 flex flex-col h-full gap-3">
            <div class="flex items-center gap-2 text-sm md:text-base">
                <label for="exchangeable" class="block font-medium text-neutral-700">ページ名</label>
                <select
                    class="flex-1 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm text-sm"
                    :onchange="onPageChange" v-model="form.key">
                    <option v-for="(value, key) in titles" :key="key" :value="key">
                        {{ value }}
                    </option>
                </select>
                <button :onclick="save" class="rounded-full bg-theme bg-theme-hover text-white px-4 py-1">保存</button>

            </div>

            <div id="summernote" class="flex-1">

            </div>
        </div>

    </AppLayout>
</template>
<script>

import { Head, useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';

export default {
    components: { Head, AppLayout },
    props: {
        errors: Object,
        auth: Object,
        titles: Object,
        contents: Object
    },
    data() {
        return {
            summernote: null,
        }
    },
    mounted() {
        this.summernote = window.$('#summernote').summernote({
            lineHeights: ['0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '2.0', '3.0'],
            fontSizes: ['8', '9', '10', '12', '14', '16', '18', '20', '24', '28', '32', '36'],
            toolbar: [
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph', 'height']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview']],
            ],
            disableDragAndDrop: true,
            height: Math.max($('#summernote').height() - 100, 300),
        });
        this.summernote.summernote('code', this.contents[this.form.key]);
    },
    methods: {
        onPageChange(e) {
            this.summernote.summernote('code', this.contents[e.target.value]);
        },
        save() {
            this.form.value = this.summernote.summernote('code');
            this.form.post(route('admin.page.update'), {
                preserveScroll: true,
                onFinish: () => {

                },
            });
        }
    },
    setup(props) {
        const form = useForm({
            key: Object.keys(props.titles)[0],
            value: props.contents[Object.keys(props.titles)[0]],
        });
        return { form };
    }
};
</script>
