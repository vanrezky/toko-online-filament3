<script setup>
import Layout from "@frontend/Layouts/Layout.vue";
import CardContent from "./ui/card/CardContent.vue";
import Card from "./ui/card/Card.vue";
import CardHeader from "./ui/card/CardHeader.vue";
import CardTitle from "./ui/card/CardTitle.vue";
import CardDescription from "./ui/card/CardDescription.vue";
import CardFooter from "./ui/card/CardFooter.vue";

const props = defineProps({
    title: {
        required: true,
        type: String,
        default: "",
    },
    description: {
        type: String,
        default: "",
    },
});
</script>
<template>
    <Layout :title="title">
        <div class="flex justify-center p-5">
            <Card class="w-full max-w-lg">
                <CardHeader>
                    <CardTitle>{{ title }}</CardTitle>
                    <CardDescription>{{ description }}</CardDescription>
                </CardHeader>
                <CardContent v-if="$slots.content">
                    <!-- content auth -->
                    <slot name="content"></slot>
                    <!-- content auth -->
                </CardContent>
                <CardFooter v-if="$slots.footer">
                    <slot name="footer"></slot>
                </CardFooter>
            </Card>
        </div>
    </Layout>
</template>
