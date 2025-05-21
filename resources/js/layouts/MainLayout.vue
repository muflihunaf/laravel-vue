<template>
  <div class="min-h-screen bg-gray-100">
    <div class="flex">
      <!-- <Sidebar /> -->
      <!-- Main content -->
      <div class="flex-1">
        <main class="flex-1">
          <slot></slot>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
// import Sidebar from '@/components/layout/Sidebar.vue';
</script>

<style scoped>
/* Custom scrollbar for sidebar */
::-webkit-scrollbar {
  width: 8px;
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
</style> 