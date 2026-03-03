<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { Mail, Lock, User, ArrowRight } from 'lucide-vue-next';

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('frontend.signup.post'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <TemplateWrapper title="Create Account">
    <div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
      <div class="max-w-md w-full space-y-12">
        <div class="text-center space-y-4">
          <h2 class="text-3xl md:text-4xl font-bold text-black tracking-tight">Create Account</h2>
          <p class="text-gray-500 text-sm">Join us to experience the best curated fashion and lifestyle.</p>
        </div>

        <form class="mt-8 space-y-6" @submit.prevent="submit">
          <div class="space-y-4">
            <!-- Name Row -->
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1">
                <label for="first_name" class="text-xs font-bold uppercase tracking-widest text-black">First Name</label>
                <input id="first_name" v-model="form.first_name" type="text" required class="w-full bg-white border border-gray-200 py-3 px-4 text-sm focus:outline-none focus:border-black rounded-none" />
              </div>
              <div class="space-y-1">
                <label for="last_name" class="text-xs font-bold uppercase tracking-widest text-black">Last Name</label>
                <input id="last_name" v-model="form.last_name" type="text" required class="w-full bg-white border border-gray-200 py-3 px-4 text-sm focus:outline-none focus:border-black rounded-none" />
              </div>
            </div>

            <!-- Email -->
            <div class="space-y-1">
              <label for="email" class="text-xs font-bold uppercase tracking-widest text-black">Email Address</label>
              <div class="relative">
                <input id="email" v-model="form.email" type="email" required class="w-full bg-white border border-gray-200 py-3 px-10 text-sm focus:outline-none focus:border-black rounded-none" placeholder="name@example.com" />
                <Mail class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
              </div>
              <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
            </div>

            <!-- Password -->
            <div class="space-y-1">
              <label for="password" class="text-xs font-bold uppercase tracking-widest text-black">Password</label>
              <div class="relative">
                <input id="password" v-model="form.password" type="password" required class="w-full bg-white border border-gray-200 py-3 px-10 text-sm focus:outline-none focus:border-black rounded-none" placeholder="••••••••" />
                <Lock class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1">
              <label for="password_confirmation" class="text-xs font-bold uppercase tracking-widest text-black">Confirm Password</label>
              <div class="relative">
                <input id="password_confirmation" v-model="form.password_confirmation" type="password" required class="w-full bg-white border border-gray-200 py-3 px-10 text-sm focus:outline-none focus:border-black rounded-none" placeholder="••••••••" />
                <Lock class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
              </div>
              <p v-if="form.errors.password" class="text-xs text-red-500 mt-1">{{ form.errors.password }}</p>
            </div>
          </div>

          <div>
            <button 
              type="submit" 
              :disabled="form.processing"
              class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold uppercase tracking-widest text-white bg-black hover:bg-gray-800 focus:outline-none transition-all disabled:bg-gray-300"
            >
              Create Account
              <ArrowRight class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </button>
          </div>
        </form>

        <div class="text-center pt-8 border-t border-gray-200">
          <p class="text-sm text-gray-500">
            Already have an account? 
            <Link :href="route('frontend.login')" class="font-bold text-black hover:underline ml-1">
              Sign In
            </Link>
          </p>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>
