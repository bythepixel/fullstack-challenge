<script setup lang="ts">
import {useDashboardStore} from "@/stores/dashboard"
import {useModal} from "@/components/modal"
import User from "@/components/User.vue";

//TODO include the font awesome icon, haven't used the free version in a while
const dashboardStore = useDashboardStore()
const modal = useModal()
//could definitely stand to add more calculation here
let userClick = (user:User) => {

}

</script>

<template>
  <div id="dashboard">
    <v-card
        class="mx-auto"
        max-width="300"
    >
      <v-list density="compact">
        <v-list-subheader>Dashboard</v-list-subheader>

        <v-list-item
          v-for="[id, item] in dashboardStore.all"
          :key="item.id"
          :subtitle="item.forecast[0]!==undefined ? item.forecast[0].temperature +' '+ item.forecast[0].shortForecast : 'unavailable'"
          :title="item.name"
          @click="userClick(item)"
        >

          <template v-slot:prepend>
            <span>
              <img v-if="item.forecast[0]!==undefined" class="dashboard-icon" :src="item.forecast[0].icon"
                   :alt="item.forecast[0].shortForecast" />
              <svg v-else class="dashboard-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c-9.4 9.4-9.4 24.6 0 33.9l47 47-47 47c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l47-47 47 47c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-47-47 47-47c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-47 47-47-47c-9.4-9.4-24.6-9.4-33.9 0z"/></svg>
            </span>
          </template>

        </v-list-item>
      </v-list>
    </v-card>
  </div>
</template>

<style scoped>
  .dashboard-icon{
    height: 35px;
    width: 35px;
    border-radius: 50%;
    margin: 10px 10px;
  }
</style>
