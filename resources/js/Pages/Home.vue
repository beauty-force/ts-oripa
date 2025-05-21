<template>

    <Head>
        <title>{{ category_share.title }}カードガチャ</title>
    </Head>

    <AppLayout :is_home="true">
        
        <div class="sm:px-4 px-4 mb-8 grid sm:grid-cols-2 grid-cols-1 gap-4 pt-4 pb-8">
            <template v-for="(gacha, key) in sortedGachas">
                <GachaCard v-if="gacha.count_rest" :gacha="gacha" />
            </template>
            <template v-for="(gacha, key) in sortedGachas">
                <GachaCard v-if="!gacha.count_rest" :gacha="gacha" />
            </template>
        </div>

    </AppLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';
import GachaCard from '@/Parts/GachaCard.vue';

export default {
    components: { Head, AppLayout, Link, GachaCard },
    props: {
        errors: Object,
        auth: Object,
        gachas: Object,
        category_share: Object,
        last24HourConsumePoints: Number
    },
    data() {
        return {
            orderBy: "order"
        }
    },
    computed: {
        sortedGachas() {
            let gachas = this.gachas.data;
            if (this.orderBy == 'point_up') gachas.sort((a, b) => a.status != b.status ? a.status - b.status : a.point - b.point);
            if (this.orderBy == 'point_down') gachas.sort((a, b) => a.status != b.status ? a.status - b.status : b.point - a.point);
            if (this.orderBy == 'order') gachas.sort((a, b) => a.status != b.status ? a.status - b.status : (a.order_level == b.order_level ? b.id - a.id : b.order_level - a.order_level));
            return gachas;
        }
    },
    mounted() {
        this.initializeLazyLoading();
    },
    methods: {
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        }, 
        initializeLazyLoading() {
            const images = document.querySelectorAll('img.lazy');

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
        orderChanged() {
            this.$nextTick(() => {
                this.initializeLazyLoading();
            })
        }
    }
}
</script>
