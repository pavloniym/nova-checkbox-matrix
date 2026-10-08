<template>
  <img
    v-if="row.avatar && !failed"
    :src="row.avatar"
    :alt="row.label"
    class="ncm-avatar"
    loading="lazy"
    referrerpolicy="no-referrer"
    @error="failed = true"
  />
  <span v-else class="ncm-avatar ncm-avatar--initials" :style="{ backgroundColor: color }" aria-hidden="true">
    {{ initials }}
  </span>
</template>

<script>
const PALETTE = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#8b5cf6', '#14b8a6']

export default {
  props: {
    row: { type: Object, required: true },
  },

  data: () => ({ failed: false }),

  computed: {
    initials() {
      return (this.row.label || '?')
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map(word => Array.from(word)[0] || '')
        .join('')
        .toLocaleUpperCase()
    },

    color() {
      let hash = 0
      for (const char of String(this.row.key)) {
        hash = (hash * 31 + char.codePointAt(0)) >>> 0
      }
      return PALETTE[hash % PALETTE.length]
    },
  },
}
</script>
