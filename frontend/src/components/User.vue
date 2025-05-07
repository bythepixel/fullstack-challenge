<script setup lang="ts">
  import {useDashboardStore} from "@/stores/dashboard";

  const dashboardStore = useDashboardStore()

</script>

<template>
<div>
  <div v-if="dashboardStore.currentUser!==null">
    Loading...
  </div>
  <div v-else>
    <h1>{{}}</h1>
  </div>
</div>
</template>

<style scoped>

</style>