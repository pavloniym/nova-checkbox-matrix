<template>
  <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText" :full-width-content="true">
    <template #field>
      <div class="ncm">
        <input
          v-if="options.searchable && rows.length > 0"
          v-model="search"
          type="search"
          class="w-full form-control form-input form-control-bordered ncm-search"
          :placeholder="__('Search')"
          :dusk="`${field.attribute}-search`"
        />

        <div v-if="rows.length === 0" class="ncm-empty">{{ __('No rows') }}</div>
        <div v-else-if="visibleRows.length === 0" class="ncm-empty">{{ __('No results found.') }}</div>

        <div v-else class="ncm-scroll" :class="[errorClasses, { 'ncm-scroll--fill': mode === 'action-fullscreen' }]">
          <table class="ncm-table">
            <thead>
              <tr>
                <th class="ncm-th ncm-sticky-col"></th>
                <th v-for="column in columns" :key="column.key" class="ncm-th ncm-th--column">
                  <span class="ncm-column-label">{{ column.label }}</span>
                  <span v-if="options.counters" class="ncm-counter" :class="{ 'ncm-counter--active': counts[column.key] > 0 }">
                    {{ counts[column.key] }}
                  </span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in visibleRows" :key="row.key" class="ncm-row">
                <th scope="row" class="ncm-row-head ncm-sticky-col">
                  <div class="ncm-person">
                    <MatrixAvatar v-if="options.avatars" :row="row" />
                    <div class="ncm-person-text">
                      <div class="ncm-label">{{ row.label }}</div>
                      <div v-if="options.subtitles && row.subtitle" class="ncm-subtitle">{{ row.subtitle }}</div>
                      <div v-if="options.captions && row.caption" class="ncm-caption">{{ row.caption }}</div>
                    </div>
                  </div>
                </th>
                <td
                  v-for="column in columns"
                  :key="column.key"
                  class="ncm-cell"
                  :class="{ 'ncm-cell--checked': isChecked(row.key, column.key) }"
                  @click.self="toggle(row.key, column.key)"
                >
                  <input
                    type="checkbox"
                    class="checkbox"
                    :checked="isChecked(row.key, column.key)"
                    :aria-label="`${row.label}: ${column.label}`"
                    @change="toggle(row.key, column.key)"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import MatrixAvatar from './MatrixAvatar'
import { countChecked, fromSelection, isChecked, normalizeOptions, toSelection, toggle } from '../matrix'

export default {
  components: { MatrixAvatar },

  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field', 'mode'],

  data: () => ({ search: '' }),

  computed: {
    rows() {
      return this.field.rows || []
    },

    columns() {
      return this.field.columns || []
    },

    options() {
      return normalizeOptions(this.field.options)
    },

    visibleRows() {
      const query = this.search.trim().toLocaleLowerCase()

      if (query === '') {
        return this.rows
      }

      return this.rows.filter(row =>
        [row.label, row.subtitle, row.caption].filter(Boolean).join(' ').toLocaleLowerCase().includes(query)
      )
    },

    counts() {
      return countChecked(this.value, this.rows.map(row => row.key), this.columns)
    },
  },

  methods: {
    setInitialValue() {
      this.value = toSelection(this.field.value)
    },

    isChecked(rowKey, columnKey) {
      return isChecked(this.value, rowKey, columnKey)
    },

    toggle(rowKey, columnKey) {
      this.value = toggle(this.value, rowKey, columnKey)
    },

    fill(formData) {
      formData.append(this.fieldAttribute, JSON.stringify(fromSelection(this.value, this.columns)))
    },
  },
}
</script>
