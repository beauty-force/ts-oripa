<template>
    <div class="w-full sticky top-[79px] z-[14] bg-gradient-to-b from-white to-neutral-50/50">
        <div class="md:w-[760px] font-[mplus2] w-full mx-auto">
            <div class="relative">
                <!-- Scroll Indicators -->
                <div class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
                <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>

                <!-- Category Bar -->
                <div class="whitespace-nowrap text-sm overflow-x-auto overflow-y-hidden font-medium category_bar flex items-center px-4 py-2 scrollbar-hide">
                    <Link v-for="(category, key) in category_share.categories"
                        :href="current_route + '?cat_id=' + category.id" 
                        class="inline-flex items-center px-5 py-2.5 mx-1 rounded-full transition-all duration-200"
                        :class="{ 
                            'bg-gradient-to-r from-sky-500 to-sky-600 text-white shadow-md scale-105 active_tab': category.id == category_share.cat_id,
                            'bg-white text-neutral-600 hover:bg-neutral-50 hover:text-neutral-800 border border-neutral-200': category.id != category_share.cat_id
                        }">
                        <span class="relative">
                            {{ category.title }}
                            <span v-if="category.id == category_share.cat_id" 
                                class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-white rounded-full"></span>
                        </span>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';
import { ArrowRightIcon } from '@heroicons/vue/24/outline'

export default {
    components: { Link, ArrowRightIcon },
    props: {
        is_admin: Boolean
    },
    data() {
        return {
            category_share: usePage().props.value.category_share,
            current_route: "",
            category_bar: null,
        }
    },
    mounted() {
        let url_str = window.location.href;
        this.current_route = url_str.split("?")[0];
        this.category_bar = document.getElementsByClassName('category_bar')[0];
        if (this.category_bar) {
            let active_tab = this.category_bar.getElementsByClassName('active_tab')[0];
            if (active_tab) {
                let offset = active_tab.offsetLeft - this.category_bar.clientWidth / 2 + active_tab.clientWidth / 2;
                this.scrollBy(offset);
            }
        }
    },
    methods: {
        scroll(dir) {
            this.scrollBy(this.category_bar.scrollLeft + this.category_bar.clientWidth * 0.9 * dir);
        },
        scrollBy(offset) {
            if (offset < 0) offset = 0;
            if (offset + this.category_bar.clientWidth > this.category_bar.scrollWidth) offset = this.category_bar.scrollWidth - this.category_bar.clientWidth;
            this.category_bar.scroll(offset, 0);
        }
    }
}
</script>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>