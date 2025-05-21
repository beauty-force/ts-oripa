<template>
    <UserLayout>

        <Head :title="title" />
        <div class="pt-6 md:px-4 px-2" v-html="content">
            
        </div>

    </UserLayout>

</template>
<script>
import { Link, Head } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
export default {
    components: { Link, UserLayout, Head },
    props: {
        title: String,
        content: String
    }
}
</script>