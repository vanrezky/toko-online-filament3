<script setup>
import Auth from "@frontend/Components/Auth.vue";
import Button from "@frontend/Components/ui/button/Button.vue";
import { FormField } from "@frontend/Components/ui/form";
import FormControl from "@frontend/Components/ui/form/FormControl.vue";
import FormItem from "@frontend/Components/ui/form/FormItem.vue";
import FormLabel from "@frontend/Components/ui/form/FormLabel.vue";
import FormMessage from "@frontend/Components/ui/form/FormMessage.vue";
import Input from "@frontend/Components/ui/input/Input.vue";
import { Link } from "@inertiajs/vue3";
</script>
<template>
    <Auth title="Sign Up" description="Create your account to get more feature..">
        <template #content>
            <form @submit="onSubmit" class="space-y-5">
                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="email" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="Full Name">
                    <FormItem>
                        <FormLabel>Full Name</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="Full Name" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="username">
                    <FormItem>
                        <FormLabel>Username</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="shadcn" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="password">
                    <FormItem>
                        <FormLabel>Password</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="shadcn" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="confirm_password">
                    <FormItem>
                        <FormLabel>Confirm Password</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="confirm password" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <div class="flex justify-center">
                    <Button type="submit" class="w-full"> Sign Up </Button>
                </div>
            </form>
        </template>
        <template #footer>
            <p class="text-sm">
                Already have an account?
                <Link href="/sign-in" class="text-link">Sign In Now</Link>
            </p>
        </template>
    </Auth>
</template>
