<template>
  <div>
    <h1>Список услуг</h1>
    <ul>
      <li v-for="s in services" :key="s.id">
        <a :href="`/services/${s.id}`">{{ s.name }} — {{ s.duration_minutes }} мин</a>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/inertia-vue3';
const { props } = usePage();
const services = props.value.services || [];
</script>
