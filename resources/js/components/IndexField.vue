<template>
  <span>{{ count > 0 ? count : '—' }}</span>
</template>

<script>
import { toSelection } from '../matrix'

export default {
  props: ['resourceName', 'field'],

  computed: {
    count() {
      return Object.keys(toSelection(this.field.value)).length
    },
  },
}
</script>
