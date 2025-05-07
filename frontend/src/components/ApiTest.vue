<script setup>
import {useDashboardStore} from "../stores/dashboard";
import Dashboard from "@/components/Dashboard.vue";
const dashboardStore = useDashboardStore();


</script>

<template>
  <div v-if="!dashboardStore.all">
    Pinging the api...
  </div>

  <div v-if="dashboardStore.all">
    The api responded with: <br />
    <Dashboard />
  </div>
</template>