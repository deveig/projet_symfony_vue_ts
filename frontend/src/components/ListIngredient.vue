<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  props: {
    index: Number,
    ingredient: String,
    quantity: Number,
    unit: String
  }
});
</script>
<template>
  <tr>
    <td>{{ index }}</td>
    <td>{{ ingredient }}</td>
    <td>{{ quantity }}</td>
    <td>{{ unit }}</td>
  </tr>
</template>
