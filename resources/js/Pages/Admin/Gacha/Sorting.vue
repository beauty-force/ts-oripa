<template>

    <Head title="ガチャ並び替え" />

    <AppLayout>
        <div class="mt-2 px-2 md:px-3">
            <div class="p-1 justify-end flex gap-2 items-center">
                <!-- <button @click="shuffle()" class="text-white bg-rose-500 text-sm font-normal py-2 px-3 inline-block rounded">
                    ランダム配置
                </button> -->
                <button v-if="!shuffle_mode" type="button" @click="toggle_shuffle(1)"
                    :class="{ 'opacity-25': form_shuffle_mode.processing }" :disabled="form_shuffle_mode.processing"
                    class="inline px-10 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 mr-2">
                    ランダム設定
                </button>
                <button v-else type="button" @click="toggle_shuffle(0)"
                    :class="{ 'opacity-25': form_shuffle_mode.processing }" :disabled="form_shuffle_mode.processing"
                    class="inline px-10 py-2.5 bg-neutral-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150 mr-2">
                    ランダム設定解除
                </button>
                <button @click.prevent="submit()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    class="w-32 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-normal py-2 px-3 inline-block rounded">
                    <div class="inline-block align-middle">
                        <PaperAirplaneIcon class="w-5 h-5 mr-1" />
                    </div>
                    <div class="inline-block align-middle">保存する</div>
                </button>
            </div>
            <div class="flex mb-1">
                <draggable class="dragArea list-group w-full" :list="list" @change="log">
                    <div class="list-group-item bg-neutral-800 m-1 p-1 rounded-md text-left cursor-pointer text-white relative"
                        v-for="(element, index) in list" :key="index">
                        <img :data-src="element.thumbnail" class="w-24 h-24 inline-block align-middle mr-2 _lazy" />
                        <div class="inline-block text-left align-middle">
                            <div>
                                <span v-if="element.status == 1"
                                    class="bg-green-600 rounded text-xs px-2 mx-1">公開</span>
                                <span v-if="element.status == 0"
                                    class="bg-neutral-600 rounded text-xs px-2 mx-1">非公開</span>
                            </div>
                            <div>
                                <span class="text-white text-sm">残 </span><span class=" text-base">{{ element.count_rest
                                    }}</span><span class="text-white text-xs">/{{ element.count_card }}</span>
                                <span v-if="element.count_rest == 0"
                                    class="bg-red-600 rounded text-xs px-2 mx-1">Soldout</span>
                            </div>
                            <div><span class="text-sm ">1回:</span> <span>{{ element.point }} pt</span></div>
                        </div>
                        <div class="align-middle h-full absolute top-0 right-2 flex items-center gap-2">
                            <button @click="toggle_fix(element.id)" class="rounded px-2 py-1 text-sm"
                                :class="{ 'bg-neutral-700 text-white': !element.fixed, 'bg-neutral-100 text-rose-500': element.fixed }">{{
                                    element.fixed ? '順序固定' : '固定解除' }}</button>
                            <button @click.prevent="levelUp(element.id)"
                                class="bg-neutral-700 rounded p-1 hover:bg-neutral-600">
                                <ArrowLongUpIcon class="w-6 h-5 text-white block" />
                            </button>
                            <button @click.prevent="levelDown(element.id)"
                                class="bg-neutral-700 rounded p-1 hover:bg-neutral-600">
                                <ArrowLongDownIcon class="w-6 h-5 text-white block" />
                            </button>
                        </div>
                    </div>

                </draggable>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import { VueDraggableNext } from 'vue-draggable-next';
import { ArrowLongUpIcon, ArrowLongDownIcon, PaperAirplaneIcon } from '@heroicons/vue/24/solid'

export default {
    components: { Head, AppLayout, Link, draggable: VueDraggableNext, ArrowLongUpIcon, ArrowLongDownIcon, PaperAirplaneIcon },
    props: {
        errors: Object,
        auth: Object,
        gachas: Object,
        category_share: Object,
        shuffle_mode: Boolean
    },
    data() {
        return {
            enabled: true,
            list: this.gachas.data.map(item => {
                return {
                    ...item,
                    fixed: item.order_level > 1000000
                }
            }),
            dragging: false,
        }
    },
    methods: {
        log(event) {
            this.refresh_list();
        },
        refresh_list() {
            this.list = [
                ...this.list.filter(item => { return item.fixed; }),
                ...this.list.filter(item => { return !item.fixed; }),
            ]
            this.$nextTick(() => {
                this.initializeLazyLoading();
            });
        },
        levelUp(id) {
            let i;
            for (i = 1; i < this.list.length; i++) {
                if (this.list[i].id == id) {
                    break;
                }
            }
            if (i < this.list.length) {
                this.arrayMove(i, i - 1);
            }
            this.refresh_list();
        },
        arrayMove(from, to) {
            this.list.splice(to, 0, this.list.splice(from, 1)[0]);
            this.refresh_list();
        },
        levelDown(id) {
            let i;
            for (i = 0; i < this.list.length - 1; i++) {
                if (this.list[i].id == id) {
                    break;
                }
            }
            if (i < (this.list.length - 1)) {
                this.arrayMove(i, i + 1);
            }
            this.refresh_list();
        },
        submit() {
            this.form.gachas = this.list;
            this.form.post(route('admin.gacha.sorting.store'), {
                onFinish: () => {
                }
            });
        },
        shuffle() {
            this.list = this.list.map(value => ({ value, sort: Math.random() }))
                .sort((a, b) => a.sort - b.sort)
                .map(({ value }) => value);
            this.refresh_list();
        },
        toggle_shuffle(mode) {
            this.form_shuffle_mode.shuffle_mode = mode;
            this.form_shuffle_mode.post(route('admin.gacha.shuffle_mode'), {
                onFinish: () => {

                }
            });
        },
        toggle_fix(id) {
            let i;
            for (i = 0; i < this.list.length; i++) {
                if (this.list[i].id == id) {
                    this.list[i].fixed ^= true;
                    break;
                }
            }
            this.refresh_list();
        },
        initializeLazyLoading() {
            const images = document.querySelectorAll('img._lazy');

            const options = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const loadImage = (image) => {
                image.src = image.dataset.src;
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        loadImage(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, options);

            images.forEach(image => {
                observer.observe(image);
            });
        },
    },
    setup(props) {
        const form = useForm({
            gachas: [],
        });
        const form_shuffle_mode = useForm({
            shuffle_mode: props.shuffle_mode
        });
        return { form, form_shuffle_mode }
    },
    mounted() {
        this.initializeLazyLoading();
    },
}
</script>