<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { Mail, Lock, ArrowRight, UserPlus } from 'lucide-vue-next';

const form = useForm({
  email: '',
  password: '',
  remember: false
});

const submit = () => {
  form.post(route('frontend.login.post'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <TemplateWrapper title="Sign In">
    <div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
      <div class="max-w-md w-full space-y-12">
        <div class="text-center space-y-4">
          <h2 class="text-3xl md:text-4xl font-bold text-black tracking-tight">Welcome Back</h2>
          <p class="text-gray-500 text-sm">Please enter your details to sign in to your account.</p>
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

            <!-- Password -->
            <div class="space-y-1">
              <div class="flex justify-between items-center">
                <label for="password" class="text-xs font-bold uppercase tracking-widest text-black">Password</label>
                <Link :href="route('frontend.forgot-password')" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-black transition-colors">
                  Forgot?
                </Link>
              </div>
              <div class="relative">
                <input 
                  id="password" 
                  v-model="form.password"
                  type="password" 
                  required 
                  class="w-full bg-white border border-gray-200 py-3 px-10 text-sm focus:outline-none focus:border-black rounded-none transition-all"
                  placeholder="••••••••"
                />
                <Lock class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
              </div>
              <p v-if="form.errors.password" class="text-xs text-red-500 mt-1">{{ form.errors.password }}</p>
            </div>
          </div>

          <div class="flex items-center">
            <input 
              id="remember" 
              v-model="form.remember"
              type="checkbox" 
              class="w-4 h-4 border-gray-300 text-black focus:ring-black rounded-none"
            />
            <label for="remember" class="ml-3 text-sm text-gray-500">
              Stay signed in
            </label>
          </div>

          <div>
            <button 
              type="submit" 
              :disabled="form.processing"
              class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold uppercase tracking-widest text-white bg-black hover:bg-gray-800 focus:outline-none transition-all disabled:bg-gray-300"
            >
              Sign In
              <ArrowRight class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </button>
          </div>
        </form>

        <div class="text-center pt-8 border-t border-gray-200">
          <p class="text-sm text-gray-500">
            Don't have an account? 
            <Link :href="route('frontend.signup')" class="font-bold text-black hover:underline ml-1">
              Create an account
            </Link>
          </p>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>
