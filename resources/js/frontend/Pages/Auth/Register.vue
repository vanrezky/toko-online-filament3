<script setup>
import { useForm, Link } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Mail, Lock, User, ArrowRight } from "lucide-vue-next";

const form = useForm({
    first_name: "",
    last_name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("frontend.signup.post"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <TemplateWrapper title="Daftar">
        <div class="flex min-h-[70vh] items-center justify-center bg-secondary/30 px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-8">
                <div class="rounded-2xl bg-white p-8 shadow-sm">
                    <div class="mb-8 space-y-2 text-center">
                        <h2 class="text-2xl font-bold text-foreground md:text-3xl">Buat Akun</h2>
                        <p class="text-sm text-muted-foreground">Daftar untuk pengalaman belanja terbaik.</p>
                    </div>

                    <form class="space-y-5" @submit.prevent="submit">
                        <div class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="first_name" class="text-sm font-semibold text-foreground">Nama Depan</label>
                                    <div class="relative">
                                        <input
                                            id="first_name"
                                            v-model="form.first_name"
                                            type="text"
                                            required
                                            class="w-full rounded-xl border border-border bg-secondary px-4 py-3.5 pl-11 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                                            placeholder="Nama depan"
                                        />
                                        <User class="absolute left-4 top-3.5 h-5 w-5 text-muted-foreground" />
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label for="last_name" class="text-sm font-semibold text-foreground">Nama Belakang</label>
                                    <input
                                        id="last_name"
                                        v-model="form.last_name"
                                        type="text"
                                        required
                                        class="w-full rounded-xl border border-border bg-secondary px-4 py-3.5 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                                        placeholder="Nama belakang"
                                    />
                                </div>
                            </div>

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
                                <label for="password" class="text-sm font-semibold text-foreground">Kata Sandi</label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        required
                                        class="w-full rounded-xl border border-border bg-secondary px-4 py-3.5 pl-11 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                                        placeholder="Min. 8 karakter"
                                    />
                                    <Lock class="absolute left-4 top-3.5 h-5 w-5 text-muted-foreground" />
                                </div>
                                <p v-if="form.errors.password" class="text-xs text-red-500">{{ form.errors.password }}</p>
                            </div>

                            <div class="space-y-2">
                                <label for="password_confirmation" class="text-sm font-semibold text-foreground">Konfirmasi Kata Sandi</label>
                                <div class="relative">
                                    <input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        required
                                        class="w-full rounded-xl border border-border bg-secondary px-4 py-3.5 pl-11 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                                        placeholder="Ulangi kata sandi"
                                    />
                                    <Lock class="absolute left-4 top-3.5 h-5 w-5 text-muted-foreground" />
                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex w-full items-center justify-center gap-2 rounded-full bg-primary py-3.5 text-sm font-bold text-primary-foreground shadow-md transition-all hover:bg-primary/90 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Daftar
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </form>

                    <div class="mt-8 border-t border-border pt-6 text-center">
                        <p class="text-sm text-muted-foreground">
                            Sudah punya akun?
                            <Link :href="route('frontend.login')" class="font-bold text-primary hover:underline"> Masuk </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>
