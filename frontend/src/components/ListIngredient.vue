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
    <td class="item-datas item-number">{{ index }}</td>
    <td class="item-datas">{{ ingredient }}</td>
    <td class="item-datas">{{ quantity }}</td>
    <td class="item-datas">{{ unit }}</td>
  </tr>
</template>
