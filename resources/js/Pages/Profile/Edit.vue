<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import InviteFriend from './Partials/InviteFriend.vue';
import LineLink from './Partials/LineLink.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/inertia-vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: Boolean,
    invitations: Object,
    invite_url: String,
    invite_bonus: String,
    invited_bonus: String,
    // line_link_url: String,
    line_invite_text: String,
    twitter_invite_text: String,
});
</script>

<template>
    <Head title="プロフィール" />

    <UserLayout>
        <div class="w-full flex justify-center pt-6">
            <div class="md:w-[768px] w-full px-4 md:px-6 lg:px-8">
                <h2 class="font-bold text-2xl bg-gradient-to-r from-sky-500 to-sky-400 bg-clip-text text-transparent leading-tight px-2 mb-4">
                    プロフィール設定
                </h2>

                <div class="w-full py-4">
                    <div class="mx-auto space-y-8">
                        <div class="p-6 sm:p-8 bg-white/80 border border-sky-100 shadow-lg rounded-xl shadow-sky-200/50 hover:shadow-sky-300/50 transition-shadow duration-300">
                            <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
                        </div>
                        
                        <div class="p-6 sm:p-8 bg-white/80 border border-sky-100 shadow-lg rounded-xl shadow-sky-200/50 hover:shadow-sky-300/50 transition-shadow duration-300">
                            <InviteFriend 
                                :invitations="invitations" 
                                :invite_url="invite_url" 
                                :invite_bonus="invite_bonus" 
                                :invited_bonus="invited_bonus" 
                                :line_invite_text="line_invite_text" 
                                :twitter_invite_text="twitter_invite_text"
                            />
                        </div>
                        
                        <div class="p-6 sm:p-8 bg-white/80 border border-sky-100 shadow-lg rounded-xl shadow-sky-200/50 hover:shadow-sky-300/50 transition-shadow duration-300">
                            <UpdatePasswordForm />
                        </div>

                        <!-- <div class="p-6 sm:p-8 bg-white/80 border border-red-100 shadow-lg rounded-xl shadow-red-200/50 hover:shadow-red-300/50 transition-shadow duration-300">
                            <DeleteUserForm />
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>