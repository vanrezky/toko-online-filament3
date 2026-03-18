<script setup>
import { useForm, Link } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Mail, Lock, ArrowRight } from "lucide-vue-next";

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("frontend.login.post"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <TemplateWrapper title="Masuk">
        <div class="flex min-h-[70vh] items-center justify-center bg-secondary/30 px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-8">
                <div class="rounded-2xl bg-white p-8 shadow-sm">
                    <div class="mb-8 space-y-2 text-center">
                        <h2 class="text-2xl font-bold text-foreground md:text-3xl">Selamat Datang</h2>
                        <p class="text-sm text-muted-foreground">Masuk ke akun Anda untuk melanjutkan.</p>
                    </div>

                    <form class="space-y-5" @submit.prevent="submit">
                        <div class="space-y-5">
                            <div class="space-y-2">
                                <label for="email" class="text-sm font-semibold text-foreground">Email</label>
                                <div class="relative">
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full rounded-xl border border-border bg-secondary px-4 py-3.5 pl-11 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                                        placeholder="nama@email.com"
                                    />
                                    <Mail class="absolute left-4 top-3.5 h-5 w-5 text-muted-foreground" />
                                </div>
                                <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label for="password" class="text-sm font-semibold text-foreground">Kata Sandi</label>
                                    <Link :href="route('frontend.forgot-password')" class="text-xs font-medium text-primary hover:underline">
                                        Lupa kata sandi?
                                    </Link>
                                </div>
                                <div class="relative">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        required
                                        class="w-full rounded-xl border border-border bg-secondary px-4 py-3.5 pl-11 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                                        placeholder="••••••••"
                                    />
                                    <Lock class="absolute left-4 top-3.5 h-5 w-5 text-muted-foreground" />
                                </div>
                                <p v-if="form.errors.password" class="text-xs text-red-500">{{ form.errors.password }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input
                                id="remember"
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-border bg-secondary text-primary focus:ring-primary"
                            />
                            <label for="remember" class="ml-3 text-sm text-muted-foreground"> Ingat saya </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex w-full items-center justify-center gap-2 rounded-full bg-primary py-3.5 text-sm font-bold text-primary-foreground shadow-md transition-all hover:bg-primary/90 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Masuk
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </form>

                    <div class="mt-8 border-t border-border pt-6 text-center">
                        <p class="text-sm text-muted-foreground">
                            Belum punya akun?
                            <Link :href="route('frontend.signup')" class="font-bold text-primary hover:underline"> Daftar sekarang </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>
