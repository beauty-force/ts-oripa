<template>
    <div class="w-full bottom-0 bg-neutral-50 border-t border-neutral-300">

        <div class="md:w-[760px] w-full mx-auto pt-4 md:px-6 px-4 items-start text-neutral-700">
            <div class="md:w-[760px] w-full text-center mx-auto pb-2 md:px-6 px-4 flex flex-wrap md:gap-20 md:justify-center leading-7"> 
                <Link :href="route('main.terms_conditions')" class="text-inherit-400 text-center md:w-auto w-[100%]">利用規約</Link>
                <Link :href="route('main.privacy_police')" class="text-inherit-400 text-center md:w-auto w-[100%]">プライバシーポリシー</Link>
                <Link :href="route('main.notation_commercial')" class="text-inherit-400 text-center md:w-auto w-[100%]">特定商取引法に基づく表記</Link>        
            </div>
            <hr class="border-neutral-500 mx-4 my-1"/>
            <div class="md:w-[760px] w-full text-center mx-auto pt-2 pb-4 md:px-6 px-4">
                <span class="text-inherit-100 text-sm font-semibold mr-3">TS-ORIPA </span> 
                <span class="text-inherit-400 text-xs">&copy; {{year}} TS-ORIPA all rights reserved</span>
            </div>
        </div>
    </div>
</template> 

<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';

export default {
    components: {Link},
    data () {
        return {
            year : "",
        };
    },
    mounted() {
        const d = new Date();
        this.year = d.getFullYear();
    },
}
</script>

