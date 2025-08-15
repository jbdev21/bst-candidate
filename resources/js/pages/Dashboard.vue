<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl p-6">
            <h1 class="mb-6 text-3xl font-bold">Ecom Pricing Dashboard</h1>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-lg bg-white p-6 shadow">
                    <h2 class="mb-4 text-xl font-semibold">Products</h2>
                    <p class="text-gray-600">Manage product catalog and pricing</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow">
                    <h2 class="mb-4 text-xl font-semibold">Quotes</h2>
                    <p class="text-gray-600">View and manage price quotes</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow">
                    <h2 class="mb-4 text-xl font-semibold">Orders</h2>
                    <p class="text-gray-600">Track order status and fulfillment</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
