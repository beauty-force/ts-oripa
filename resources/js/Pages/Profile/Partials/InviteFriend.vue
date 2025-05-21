<script setup>
import { usePage } from '@inertiajs/inertia-vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    invitations: Array,
    invite_url: String,
    invite_bonus: String,
    invited_bonus: String,
    line_invite_text: String,
    twitter_invite_text: String,
});
const user = usePage().props.value.auth.user;

const data = ref({
    status: false
});

const onShowAll = () => {
    data.value.status = !data.value.status
}

const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

const line_text = encodeURIComponent(props.line_invite_text);
const twitter_text = encodeURIComponent(props.twitter_invite_text);

const twitterLink = isMobile ? `twitter://post?text=${twitter_text}&url=${props.invite_url}`
    : `https://twitter.com/intent/tweet?text=${twitter_text}&url=${props.invite_url}`;

const lineLink = isMobile? `line://msg/text/${line_text}`
    : `https://social-plugins.line.me/lineit/share?url=${props.invite_url}`;

import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const copyInviteCode = () => {
    navigator.clipboard.writeText(user.invite_code).then(() => {
        toast('招待コードをコピーしました。', {
            autoClose: 2000
        });
    });
}

</script>

<template>
    <section>
        <header>
            <h3 class="text-lg font-semibold text-sky-700 mb-4">友達招待</h3>
            <p class="mt-2 text-sm leading-7 text-neutral-600">
                友達を紹介して、お得なポイントをゲット！<br />
                友達が登録時にあなたの招待コードを入力するだけでOK。<br/>
                あなたに<strong class="text-sky-600">{{ invite_bonus.toLocaleString() }}</strong>ポイントがもらえます！<br/>
                友達も<strong class="text-sky-600">{{ invited_bonus.toLocaleString() }}</strong>ポイントがもらえます！
            </p>
        </header>

        <div class="mt-6 border-4 border-dashed border-sky-200 flex flex-col items-center justify-center rounded-2xl py-6 gap-3 bg-sky-50/50">
            <p class="text-sm font-medium text-sky-700">あなたの招待コード</p>
            <span 
                class="text-4xl font-bold font-[mplus2] px-4 py-2 bg-white rounded-xl shadow-sm cursor-pointer hover:shadow-md transition-shadow duration-300" 
                @click="copyInviteCode"
            >
                {{ user.invite_code }}
            </span>
            <p class="text-xs text-neutral-500">クリックでコピーできます</p>
        </div>

        <div class="flex flex-wrap justify-center gap-4 py-8">
            <a 
                :href="twitterLink" 
                target="_blank" 
                class="flex-1 flex gap-2 rounded-xl border border-neutral-200 px-8 py-3 justify-center items-center font-medium text-neutral-700 hover:bg-neutral-50 hover:border-neutral-300 transition-colors duration-300 shadow-sm hover:shadow-md whitespace-nowrap"
            >
                <img src="/images/twitter-x.svg" class="h-5" />
                <span>Xでシェア</span>
            </a>
            <a 
                :href="lineLink" 
                target="_blank" 
                class="flex-1 flex gap-2 rounded-xl border border-neutral-200 px-8 py-3 justify-center items-center font-medium text-neutral-700 hover:bg-neutral-50 hover:border-neutral-300 transition-colors duration-300 shadow-sm hover:shadow-md whitespace-nowrap"
            >
                <img src="/images/line.png" class="h-6" />
                <span>LINEでシェア</span>
            </a>
        </div>

        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="px-2 text-sm text-neutral-600">{{ invitations.length ? invitations.length + '人の友達が登録済み' : '' }}</span>
                <button 
                    @click="onShowAll" 
                    class="px-4 py-2 rounded-xl border border-neutral-200 text-sm font-medium text-neutral-700 hover:bg-neutral-50 hover:border-neutral-300 transition-colors duration-300"
                >
                    {{ data.status ? 'リストを閉じる' : '友達リストを見る' }}
                </button>
            </div>
            <div 
                v-if="data.status" 
                class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 bg-neutral-50 rounded-xl"
            >
                <template v-for="item in invitations">
                    <span class="px-4 py-2 bg-white rounded-lg border border-neutral-200 text-sm text-neutral-700">
                        {{ item.name }}
                    </span>
                </template>
            </div>
        </div>
    </section>
</template>
