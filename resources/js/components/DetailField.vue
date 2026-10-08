<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <ul v-if="selectedRows.length > 0" class="ncm-detail">
        <li v-for="row in selectedRows" :key="row.key" class="ncm-detail-row">
          <MatrixAvatar :row="row" />
          <div class="ncm-person-text">
            <div class="ncm-label">{{ row.label }}</div>
            <div class="ncm-chips">
              <span v-for="column in row.columns" :key="column.key" class="ncm-chip">{{ column.label }}</span>
            </div>
          </div>
        </li>
      </ul>
      <p v-else>&mdash;</p>
    </template>
  </PanelItem>
</template>

<script>
import MatrixAvatar from './MatrixAvatar'
import { toSelection } from '../matrix'

export default {
  components: { MatrixAvatar },

  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  computed: {
    selectedRows() {
      const selection = toSelection(this.field.value)
      const columns = this.field.columns || []

      return (this.field.rows || [])
        .filter(row => selection[row.key])
        .map(row => ({
          ...row,
          columns: columns.filter(column => selection[row.key].has(column.key)),
        }))
    },
  },
}
</script>
