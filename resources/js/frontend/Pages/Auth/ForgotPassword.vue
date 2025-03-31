<script setup>
import Auth from "@frontend/Components/Auth.vue";
import Button from "@frontend/Components/ui/button/Button.vue";
import Card from "@frontend/Components/ui/card/Card.vue";
import CardContent from "@frontend/Components/ui/card/CardContent.vue";
import CardDescription from "@frontend/Components/ui/card/CardDescription.vue";
import CardHeader from "@frontend/Components/ui/card/CardHeader.vue";
import CardTitle from "@frontend/Components/ui/card/CardTitle.vue";
import { FormField } from "@frontend/Components/ui/form";
import FormControl from "@frontend/Components/ui/form/FormControl.vue";
import FormItem from "@frontend/Components/ui/form/FormItem.vue";
import FormLabel from "@frontend/Components/ui/form/FormLabel.vue";
import FormMessage from "@frontend/Components/ui/form/FormMessage.vue";
import Input from "@frontend/Components/ui/input/Input.vue";
import { Link } from "@inertiajs/vue3";
</script>
<template>
    <Auth
        title="Forgot Password"
        description="Forgot your password? No problem. Please enter your email address below. We will send a link to reset your password. Use the link to create a new password."
    >
        <template #content>
            <form @submit="onSubmit" class="space-y-5">
                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="please enter your email" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="flex justify-center">
                    <Button type="submit" class="w-full"> Get Link </Button>
                </div>
            </form>
        </template>
        <template #footer>
            <p class="text-sm">
                Back to
                <Link href="/sign-in" class="text-link"> Sign In</Link>
            </p>
        </template>
    </Auth>
</template>
