<template>
  <div>
    <h1>Список услуг</h1>

    <div v-if="$page.props.flash.success" class="success">
      {{ $page.props.flash.success }}
    </div>

    <ul>
      <li v-for="s in services" :key="s.id">
        <a :href="`/services/${s.id}`">{{ s.name }} — {{ s.duration_minutes }} мин</a>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const services = computed(() => page.props.services || [])
</script>

<style scoped>
.success {
  color: #10b981;
  margin-bottom: 1rem;
}
</style>
