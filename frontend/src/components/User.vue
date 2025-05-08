<script setup lang="ts">
  import {useDashboardStore} from "@/stores/dashboard";

  const dashboardStore = useDashboardStore()

</script>

<template>
<div>
  <div v-if="dashboardStore.currentUser">
    Loading...
  </div>
  <div v-else>
    <h1>{{dashboardStore.currentUser}}</h1>
  </div>
</div>
</template>

<style scoped>

</style>