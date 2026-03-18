<script setup>
import { useForm, Link } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Mail, ArrowRight, ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <TemplateWrapper title="Lupa Kata Sandi">
        <div class="flex min-h-[70vh] items-center justify-center bg-secondary/30 px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-8">
                <div class="rounded-2xl bg-white p-8 shadow-sm">
                    <div class="mb-8 space-y-2 text-center">
                        <h2 class="text-2xl font-bold text-foreground md:text-3xl">Lupa Kata Sandi?</h2>
                        <p class="text-sm text-muted-foreground">Masukkan email Anda dan kami akan mengirim link untuk mereset kata sandi.</p>
                    </div>

                    <div v-if="status" class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                        {{ status }}
                    </div>

                    <form class="space-y-5" @submit.prevent="submit">
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

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex w-full items-center justify-center gap-2 rounded-full bg-primary py-3.5 text-sm font-bold text-primary-foreground shadow-md transition-all hover:bg-primary/90 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Kirim Link Reset
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
