<script setup lang="ts">
const ingredient = defineProps<{
  index: number;
  ingredient: string;
  quantity: number;
  unit: string;
}>();
</script>

<template>
  <tr>
    <td>{{ ingredient.index }}</td>
    <td>{{ ingredient.ingredient }}</td>
    <td>{{ ingredient.quantity }}</td>
    <td>{{ ingredient.unit }}</td>
  </tr>
</template>