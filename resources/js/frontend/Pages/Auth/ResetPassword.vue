<script setup>
import { ref } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Lock, ArrowRight, ArrowLeft, Eye, EyeOff, Check, X } from "lucide-vue-next";

const props = defineProps({
    token: String,
    email: String,
    guard: {
        type: String,
        default: "customer",
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    guard: props.guard,
    password: "",
    password_confirmation: "",
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const passwordRules = {
    minLength: (p) => p.length >= 8,
    uppercase: (p) => /[A-Z]/.test(p),
    number: (p) => /\d/.test(p),
    symbol: (p) => /[!@#$%^&*(),.?":{}|<>]/.test(p),
};

const isPasswordValid = (rule) => {
    return rule(form.password);
};

const submit = () => {
    form.post(route("frontend.reset-password.update"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <TemplateWrapper title="Reset Password">
        <div class="flex min-h-[70vh] items-center justify-center bg-secondary/30 px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-8">
                <div class="rounded-2xl bg-white p-8 shadow-sm">
                    <div class="mb-8 space-y-2 text-center">
                        <h2 class="text-2xl font-bold text-foreground md:text-3xl">Reset Password</h2>
                        <p class="text-sm text-muted-foreground">Masukkan password baru untuk akun Anda.</p>
                    </div>

                    <form class="space-y-5" @submit.prevent="submit">
                        <input type="hidden" v-model="form.token" />
                        <input type="hidden" v-model="form.email" />
                        <input type="hidden" v-model="form.guard" />

                        <div class="space-y-2">
                            <label for="password" class="text-sm font-semibold text-foreground">Password Baru</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    class="w-full rounded-xl border border-border bg-secondary px-4 py-3.5 pl-11 pr-11 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                                    placeholder="Min. 8 karakter"
                                />
                                <Lock class="absolute left-4 top-3.5 h-5 w-5 text-muted-foreground" />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-4 top-3.5 text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-xs text-red-500">{{ form.errors.password }}</p>

                            <div v-if="form.password" class="mt-3 space-y-1.5 rounded-xl bg-secondary/50 p-3">
                                <p class="text-xs font-medium text-muted-foreground">Password harus mengandung:</p>
                                <div
                                    class="flex items-center gap-2 text-xs"
                                    :class="isPasswordValid(passwordRules.minLength) ? 'text-green-600' : 'text-red-500'"
                                >
                                    <component :is="isPasswordValid(passwordRules.minLength) ? Check : X" class="h-3.5 w-3.5" />
                                    Minimal 8 karakter
                                </div>
                                <div
                                    class="flex items-center gap-2 text-xs"
                                    :class="isPasswordValid(passwordRules.uppercase) ? 'text-green-600' : 'text-red-500'"
                                >
                                    <component :is="isPasswordValid(passwordRules.uppercase) ? Check : X" class="h-3.5 w-3.5" />
                                    1 huruf besar
                                </div>
                                <div
                                    class="flex items-center gap-2 text-xs"
                                    :class="isPasswordValid(passwordRules.number) ? 'text-green-600' : 'text-red-500'"
                                >
                                    <component :is="isPasswordValid(passwordRules.number) ? Check : X" class="h-3.5 w-3.5" />
                                    1 angka
                                </div>
                                <div
                                    class="flex items-center gap-2 text-xs"
                                    :class="isPasswordValid(passwordRules.symbol) ? 'text-green-600' : 'text-red-500'"
                                >
                                    <component :is="isPasswordValid(passwordRules.symbol) ? Check : X" class="h-3.5 w-3.5" />
                                    1 simbol
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation" class="text-sm font-semibold text-foreground">Konfirmasi Password</label>
                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    required
                                    class="w-full rounded-xl border border-border bg-secondary px-4 py-3.5 pl-11 pr-11 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                                    placeholder="Ulangi password baru"
                                />
                                <Lock class="absolute left-4 top-3.5 h-5 w-5 text-muted-foreground" />
                                <button
                                    type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute right-4 top-3.5 text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    <component :is="showConfirmPassword ? EyeOff : Eye" class="h-5 w-5" />
                                </button>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex w-full items-center justify-center gap-2 rounded-full bg-primary py-3.5 text-sm font-bold text-primary-foreground shadow-md transition-all hover:bg-primary/90 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Reset Password
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </form>

                    <div class="mt-8 border-t border-border pt-6 text-center">
                        <Link
                            :href="route('frontend.login')"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-foreground transition-colors hover:text-primary"
                        >
                            <ArrowLeft class="h-4 w-4" />
                            Kembali ke Masuk
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>
