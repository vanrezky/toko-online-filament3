<script setup>
import Container from "@frontend/Components/Container.vue";
import Button from "@frontend/Components/ui/button/Button.vue";
import { Facebook, Instagram, Twitter } from "lucide-vue-next";
import { computed } from "vue";

const languages = computed(() => [
    { code: "ID", name: "INDONESIA" },
    { code: "EN", name: "ENGLISH" },
]);
const currencies = computed(() => [
    { code: "RP", name: "RUPIAH" },
    { code: "USD", name: "USD $" },
]);
</script>

<template>
    <div class="border-b border-b-secondary bg-secondary">
        <Container>
            <div class="flex items-center justify-between p-2">
                <div class="hidden gap-1.5 lg:flex">
                    <Button class="bg-primary-foreground p-2 text-primary/70 hover:bg-primary hover:text-secondary"
                        ><Instagram class="size-4"
                    /></Button>
                    <Button class="bg-primary-foreground p-2 text-primary/70 hover:bg-primary hover:text-secondary"><Twitter size="4" /></Button>
                    <Button class="bg-primary-foreground p-2 text-primary/70 hover:bg-primary hover:text-secondary"><Facebook /></Button>
                </div>
                <div class="text-xs uppercase text-gray-500">Summer Sale For All Swim Suits And Free Express Delivery - OFF 50%!</div>
                <div class="hidden gap-2 lg:flex">
                    <select class="bg-secondary text-center text-xs text-primary/70">
                        <option :value="currency.code" v-for="currency in currencies" :key="currency.code">
                            {{ currency.name }}
                        </option>
                    </select>
                    <select class="bg-secondary text-xs text-primary/70">
                        <option :value="language.code" v-for="language in languages" :key="language.code">
                            {{ language.name }}
                        </option>
                    </select>
                </div>
            </div>
        </Container>
    </div>
</template>
