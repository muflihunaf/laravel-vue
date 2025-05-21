<template>
    <div class="min-h-screen bg-gray-100">
        <Navigation />
        <main>
            <router-view></router-view>
        </main>
    </div>
</template>

<script setup>
import Navigation from './components/Navigation.vue';
</script> 