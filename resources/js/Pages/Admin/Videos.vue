<template>

    <Head title="ガチャ演出動画設定" />

    <AppLayout :hide_cat_bar="1" :closeModal="() => { open = false }">


        <div class="pt-6 md:px-6 px-4">
            <button @click="() => { open_form(null) }"
                class="rounded-lg bg-rose-500 hover:bg-rose-400 text-sm text-white px-4 py-1 mb-3">登録</button>

            <div v-for="(video, index) in videos" class="mb-4 flex items-center flex-wrap gap-2" :key="index">
                <div class="max-w-48 px-2">
                    「{{ video.level }}」 動画 :
                </div>
                <div class="max-w-32">
                    <video v-if="video.file" class="inline-block max-h-96" :src="video.file" type="video/mp4"
                        controls></video>
                </div>
                <div class="flex-1 justify-end flex gap-2">
                    <button @click="() => { open_form(video) }"
                        class="rounded-lg bg-sky-500 hover:bg-sky-400 text-sm text-white px-4 py-1">編集</button>
                    <button @click="destroy(video.id)"
                        class="rounded-lg px-3 py-1 bg-red-500 text-sm text-neutral-50">
                        削除
                    </button>
                </div>
            </div>
        </div>
        <template>
            <TransitionRoot as="template" :show="open">
                <Dialog as="div" class="relative z-20" @close="open = false" :open="open">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                        enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                        leave-to="opacity-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                    </TransitionChild>

                    <div class="fixed inset-0 z-20 overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                            <TransitionChild as="template" enter="ease-out duration-300"
                                enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                                leave-from="opacity-100 translate-y-0 sm:scale-100"
                                leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                <DialogPanel
                                    class="p-3 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                                    <form @submit.prevent="submit()">

                                        <div class="mb-4">
                                            <label for="level" class="block font-medium text-sm text-neutral-700 mb-1">名前<span class="text-red-500">*</span></label>
                                            <input v-model="form.level" id="level" type="text"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                                placeholder="入力してください" required />

                                            <label for="video" class="block font-medium text-neutral-700 mb-2">動画ファイル選択
                                            </label>

                                            <input id="video" ref="video" type="file" class="w-full" />

                                        </div>

                                        <div class="text-center">
                                            <button type="submit"
                                                class="inline-block items-center px-8 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 mr-2">
                                                登録
                                            </button>

                                            <button type="button" @click="open = false"
                                                class="inline-block items-center px-8 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                                                キャンセル
                                            </button>
                                        </div>
                                    </form>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>
        </template>
    </AppLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { Inertia } from '@inertiajs/inertia';
import { useConfirm } from "@/composables/useConfirm";

const { confirm } = useConfirm();

export default {
    components: { Head, AppLayout, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot },
    props: {
        errors: Object,
        auth: Object,
        category_share: Object,
        videos: Object,

    },
    data() {
        return {
            open: false,

        }
    },
    methods: {
        submit() {
            if (this.$refs.video.files.length > 0) {
                this.form.video = this.$refs.video.files[0];
            }
            else this.form.video = "";
            this.form.post(route('admin.video.store'));
        },
        open_form(video) {
            this.form.id = video ? video.id : 0;
            this.form.gacha_id = video ? video.gacha_id : 0;
            this.form.level = video ? video.level : (this.videos.length > 0 ? this.videos[this.videos.length - 1].level + 1 : 1);
            this.open = true;
        },
        destroy(id) {
            confirm("削除してもいいですか？", '', 'error').then((result) => {
                if (result) {
                    Inertia.delete(route('admin.video.destroy', { 'id': id }), { preserveScroll: true });
                }
            });
        },
    },
    setup(props) {
        const form = useForm({
            id: 0,
            gacha_id: 0,
            level: props.videos.length,
            video: "",
        })

        return { form }
    }
}
</script>