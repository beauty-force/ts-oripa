<template>

    <div v-if="banners && banners.length > 0" class="md:w-[760px] w-full bg-neutral-200 md:p-3 mx-auto">
        <carousel :items-to-show="1.2" :autoplay="6000" :wrapAround="banners.length > 1" class="w-full">
            <template v-for="banner in banners">
                <slide class="w-full px-2">
                    <div class="w-full border md:rounded-md overflow-hidden">
                        <img class="w-full cursor-pointer h-full object-contain" :src="banner.image"
                            @click="openInNewTab(banner.link_url)" />
                    </div>
                </slide>
            </template>
            <template #addons>
                <!-- <navigation /> -->
                <pagination />
            </template>
        </carousel>

    </div>
    
</template>

<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';
import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'

export default {
    components: {
        Link,
        Carousel,
        Slide,
        Pagination,
        Navigation
    },
    props: {
    },
    data() {
        return {
            category_share: usePage().props.value.category_share,
            url_gacha: "",
            url_dp: "",
            banners: [],
            // user: null,
        }
    },
    mounted() {
        this.url_dp = route('main.dp') + this.category_share.cat_route_appendix;
        this.url_gacha = route('main') + this.category_share.cat_route_appendix;

        this.banners = usePage().props.value.banners;
    },
    methods: {
        openInNewTab: (url) => {
            window.open(url, '_blank').focus();
        }
    }
}
</script>