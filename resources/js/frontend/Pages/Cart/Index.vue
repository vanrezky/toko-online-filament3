<script setup>
import Layout from "@frontend/Layouts/Layout.vue";
import AppHead from "../AppHead.vue";
import Table from "@frontend/Components/ui/table/Table.vue";
import TableHeader from "@frontend/Components/ui/table/TableHeader.vue";
import TableRow from "@frontend/Components/ui/table/TableRow.vue";
import TableHead from "@frontend/Components/ui/table/TableHead.vue";
import TableBody from "@frontend/Components/ui/table/TableBody.vue";
import TableCell from "@frontend/Components/ui/table/TableCell.vue";
import Card from "@frontend/Components/ui/card/Card.vue";
import CardHeader from "@frontend/Components/ui/card/CardHeader.vue";
import CardTitle from "@frontend/Components/ui/card/CardTitle.vue";
import CardContent from "@frontend/Components/ui/card/CardContent.vue";
import Select from "@frontend/Components/ui/select/Select.vue";
import SelectContent from "@frontend/Components/ui/select/SelectContent.vue";
import SelectGroup from "@frontend/Components/ui/select/SelectGroup.vue";
import SelectLabel from "@frontend/Components/ui/select/SelectLabel.vue";
import SelectItem from "@frontend/Components/ui/select/SelectItem.vue";
import SelectTrigger from "@frontend/Components/ui/select/SelectTrigger.vue";
import { FormField } from "@frontend/Components/ui/form";
import FormItem from "@frontend/Components/ui/form/FormItem.vue";
import FormLabel from "@frontend/Components/ui/form/FormLabel.vue";
import FormControl from "@frontend/Components/ui/form/FormControl.vue";
import FormMessage from "@frontend/Components/ui/form/FormMessage.vue";
import Button from "@frontend/Components/ui/button/Button.vue";
import Input from "@frontend/Components/ui/input/Input.vue";

const carts = [
    {
        id: 1,
        image_url: "https://placehold.co/150",
        name: "product 1",
        price: 10.25,
        qty: 1,
        color: "putih",
        category: "MEN",
        variants: [
            { name: "Size", value: "42" },
            { name: "Color", value: "White" },
        ],
    },
    { id: 2, image_url: "https://placehold.co/150", name: "product 2", price: 9.25, qty: 2, category: "WOMEN" },
    { id: 3, image_url: "https://placehold.co/150", name: "product 3", price: 15.25, qty: 1, category: "CHILD" },
];

const totalPrice = (a, b) => {
    return a * b;
};
</script>

<template>
    <Layout title="Cart">
        <div class="grid lg:grid-cols-3 lg:gap-5">
            <Card class="col-span-2">
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow class="font-semibold text-primary">
                                <TableHead>Product</TableHead>
                                <TableHead>Price</TableHead>
                                <TableHead>Quantity</TableHead>
                                <TableHead>Total Price</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="cart in carts" :key="cart.id">
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <img :src="cart.image_url" alt="" class="size-32 rounded-lg" />
                                        <div>
                                            <p class="text-xs uppercase text-primary/50">{{ cart.category }}</p>
                                            <h4 class="font-semibold tracking-wide">{{ cart.name }}</h4>
                                            <!-- variant -->
                                            <div class="mt-2" v-show="cart.variants">
                                                <div v-for="variant in cart.variants">
                                                    <span class="mr-2 text-xs text-primary/50">{{ variant.name }}</span>
                                                    <span class="text-xs text-primary"> {{ variant.value }}</span>
                                                </div>
                                            </div>
                                            <!-- variant -->
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>{{ cart.price }}</TableCell>
                                <TableCell>{{ cart.qty }}</TableCell>
                                <TableCell>{{ totalPrice(cart.qty, cart.price) }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Calculating Shipping</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-5">
                        <form @submit="onSubmit" class="space-y-5">
                            <FormField v-slot="{ componentField }" name="country">
                                <FormItem>
                                    <FormLabel>Country</FormLabel>

                                    <Select v-bind="componentField">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select a verified email to display" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="m@example.com"> m@example.com </SelectItem>
                                                <SelectItem value="m@google.com"> m@google.com </SelectItem>
                                                <SelectItem value="m@support.com"> m@support.com </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                            <div class="grid gap-2 lg:grid-cols-2">
                                <FormField v-slot="{ componentField }" name="state">
                                    <FormItem>
                                        <FormLabel>State/City</FormLabel>

                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select a verified email to display" />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem value="m@example.com"> m@example.com </SelectItem>
                                                    <SelectItem value="m@google.com"> m@google.com </SelectItem>
                                                    <SelectItem value="m@support.com"> m@support.com </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField v-slot="{ componentField }" name="zip_code">
                                    <FormItem>
                                        <FormLabel>Zip Code</FormLabel>
                                        <FormControl>
                                            <Input type="text" placeholder="zip code" v-bind="componentField" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                            </div>
                            <Button type="submit" class="w-full"> Update </Button>
                        </form>
                        <div class="border-b"></div>
                        <form @submit="onSubmit" class="space-y-5">
                            <h4 class="text-base font-semibold">Coupon Code</h4>
                            <FormField v-slot="{ componentField }" name="coupon_code">
                                <FormItem>
                                    <FormControl>
                                        <Input type="text" placeholder="Coupon code" v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                            <Button type="submit" class="w-full"> Apply </Button>
                        </form>
                        <div class="space-y-3 rounded-lg bg-secondary p-2 shadow">
                            <h4 class="text-base font-semibold">Cart Total</h4>
                            <div class="flex text-sm">
                                <span>Cart Subtotal</span>
                                <span class="ml-auto font-semibold"> Rp75.000</span>
                            </div>
                            <div class="flex text-sm">
                                <span>Discount</span>
                                <span class="ml-auto font-semibold text-primary">-Rp10.000</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold">Cart Total</span>
                                <span class="ml-auto text-lg font-semibold">Rp65.000</span>
                            </div>
                            <Button type="submit" class="w-full"> Submit </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
            <div class="col-span-1 rounded-lg bg-secondary shadow"></div>
        </div>
    </Layout>
</template>
