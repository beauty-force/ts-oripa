<template>
    <Head title="EP交換所" />

    <AppLayout :is_home="true">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-x-2 gap-y-3 pt-4 pb-8 px-4 sm:gap-x-3" >
            <template v-for="(dp, key) in products.data">
                
                <div class="w-full flex" :class="{'grayscale' : dp.marks == 0}">
                    <Link :href="dp.marks > 0 ? route('user.dp.detail', {id: dp.id}) : ''" class="text-center relative rounded-lg shadow-md border-2 border-gray-200 overflow-hidden flex flex-col">

                        <div v-if="dp.marks==0" class="w-full absolute z-10 bg-neutral-900/50 h-full flex items-center">
                            <span class="w-full text-white text-3xl font-black font-[mplus2]">SOLD OUT</span>
                        </div>
                        <div class="flex bg-white justify-center items-end">
                            <span class="text-sm leading-relaxed">残り&nbsp;</span>
                            <span class="font-semibold text-2xl" :class="{'text-rose-500': dp.marks <=5}">{{ dp.marks }}</span> 
                            <span class="text-sm leading-relaxed">&nbsp;枚</span>
                        </div>
                        <div class="text-center w-full h-full bg-white flex p-2 justify-center">
                            <img class="h-full object-contain lazy" :data-src="dp.image"/> 
                        </div>
                        <div class="flex-1 bg-gradient-to-l to-sky-600 from-sky-400 py-1.5 flex flex-col gap-1">
                            <div class="text-sm px-3 flex items-center text-white justify-center font-bold font-[mplus2]">
                                {{dp.name}}
                            </div>
                            <div class="text-white">
                                <span class="rounded-full px-4 py-0.5 bg-white text-neutral-800 w-[100px] text-sm font-bold font-[mplus2]" >
                                    {{ format_number(dp.dp) }}EP
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </template>
            
        </div>

        
    </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/UserLayout.vue';

export default {
    components: {Head, AppLayout, Link},
    props: {
        errors: Object,
        auth: Object,
        category_share:Object,
        products:Object,
    },
    methods : {
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
    },
    mounted() {
        this.initializeLazyLoading();
    },
}

//
</script>