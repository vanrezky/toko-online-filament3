<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { Mail, ArrowRight, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <TemplateWrapper title="Forgot Password">
        <div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
            <div class="max-w-md w-full space-y-12">
                <div class="text-center space-y-4">
                    <h2 class="text-3xl md:text-4xl font-bold text-black tracking-tight">Reset Password</h2>
                    <p class="text-gray-500 text-sm">
                        Enter your email address and we'll send you a link to reset your password.
                    </p>
                </div>

                <div v-if="status" class="bg-green-50 border border-green-100 text-green-600 px-4 py-3 text-sm text-center">
                    {{ status }}
                </div>

                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <div class="space-y-4">
                        <!-- Email -->
                        <div class="space-y-1">
                            <label for="email" class="text-xs font-bold uppercase tracking-widest text-black">Email Address</label>
                            <div class="relative">
                                <input 
                                    id="email" 
                                    v-model="form.email"
                                    type="email" 
                                    required 
                                    class="w-full bg-white border border-gray-200 py-3 px-10 text-sm focus:outline-none focus:border-black rounded-none transition-all"
                                    placeholder="name@example.com"
                                />
                                <Mail class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
                            </div>
                            <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                        </div>
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold uppercase tracking-widest text-white bg-black hover:bg-gray-800 focus:outline-none transition-all disabled:bg-gray-300"
                        >
                            Send Reset Link
                            <ArrowRight class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" />
                        </button>
                    </div>
                </form>

                <div class="text-center pt-8 border-t border-gray-200">
                    <Link :href="route('frontend.login')" class="inline-flex items-center text-sm font-bold text-black hover:underline uppercase tracking-widest">
                        <ArrowLeft class="mr-2 w-4 h-4" />
                        Back to Sign In
                    </Link>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>
