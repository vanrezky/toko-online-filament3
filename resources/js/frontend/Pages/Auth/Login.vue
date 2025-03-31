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
import { toTypedSchema } from "@vee-validate/zod";
import { useForm } from "vee-validate";
import * as z from "zod";

const formSchema = toTypedSchema(
    z.object({
        username: z.string().min(2).max(50),
        password: z.string().min(2).max(50),
    }),
);

const { handleSubmit } = useForm({
    validationSchema: formSchema,
});
const onSubmit = handleSubmit((values) => {
    toast({
        title: "You submitted the following values:",
        description: h(
            "pre",
            { class: "mt-2 w-[340px] rounded-md bg-slate-950 p-4" },
            h("code", { class: "text-white" }, JSON.stringify(values, null, 2)),
        ),
    });
});
</script>
<template>
    <Auth title="Sign In" description="Sign In to your account">
        <template #content>
            <form @submit="onSubmit" class="space-y-5">
                <FormField v-slot="{ componentField }" name="username">
                    <FormItem>
                        <FormLabel>Username</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="username" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="password">
                    <FormItem>
                        <FormLabel>Password</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="password" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <div class="text-link text-right text-sm">
                    <Link href="/forgot-password" class="text-link">Forgot Password?</Link>
                </div>
                <div class="flex justify-center">
                    <Button type="submit" class="w-full"> Sign In </Button>
                </div>
                <!-- start social login -->
                <div>
                    <p class="p-2 text-center text-sm text-primary">Or Log in / Register With</p>
                    <div class="flex flex-col gap-2 text-center">
                        <a
                            href="#"
                            class="flex justify-between overflow-hidden rounded-xl bg-primary-foreground p-2 text-primary transition duration-200 hover:bg-primary hover:text-secondary"
                        >
                            <p class="text-sm">Google</p>
                            <p class="ml-auto">[]</p>
                        </a>
                        <a
                            href="#"
                            class="flex justify-center overflow-hidden rounded-xl bg-primary-foreground p-2 text-primary transition duration-200 hover:bg-primary hover:text-secondary"
                        >
                            <p class="text-sm">Facebook</p>
                            <p class="ml-auto">[]</p>
                        </a>
                    </div>
                </div>
                <!-- end social login -->
            </form>
        </template>
        <template #footer>
            <p class="text-sm">
                Dont Have an account?
                <Link href="/sign-up" class="text-link"> Register Now</Link>
            </p>
        </template>
    </Auth>
</template>
