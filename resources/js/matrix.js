// Row keys are arbitrary strings ("constructor", "__proto__", ...), so selections are prototype-less
// objects and every lookup goes through Object.hasOwn.
const emptyRecord = () => Object.create(null)

/**
 * Turns a field value (object, array or JSON string) into { rowKey: Set<columnKey> }.
 */
export function toSelection(value) {
  let parsed = value

  if (typeof parsed === 'string') {
    try {
      parsed = JSON.parse(parsed)
    } catch (e) {
      parsed = null
    }
  }

  const selection = emptyRecord()

  if (parsed && typeof parsed === 'object') {
    Object.entries(parsed).forEach(([rowKey, columns]) => {
      if (Array.isArray(columns) && columns.length > 0) {
        selection[String(rowKey)] = new Set(columns.map(String))
      }
    })
  }

  return selection
}

/**
 * Whether the given cell is checked.
 */
export function isChecked(selection, rowKey, columnKey) {
  return Object.hasOwn(selection, rowKey) && selection[rowKey].has(columnKey)
}

/**
 * Returns a new selection with the given cell toggled.
 */
export function toggle(selection, rowKey, columnKey) {
  const next = Object.assign(emptyRecord(), selection)
  const columns = new Set(Object.hasOwn(selection, rowKey) ? selection[rowKey] : [])

  columns.has(columnKey) ? columns.delete(columnKey) : columns.add(columnKey)
  next[rowKey] = columns

  return next
}

/**
 * Number of checked rows per column, counting declared rows and columns only.
 */
export function countChecked(selection, rowKeys, columns) {
  const counts = Object.fromEntries(columns.map(column => [column.key, 0]))

  rowKeys.forEach(rowKey => {
    columns.forEach(column => {
      if (isChecked(selection, rowKey, column.key)) counts[column.key]++
    })
  })

  return counts
}

/**
 * Serializes a selection into a plain { rowKey: [columnKey] } object, dropping empty rows
 * and keeping the declared column order.
 */
export function fromSelection(selection, columns) {
  const result = emptyRecord()

  Object.keys(selection).forEach(rowKey => {
    const ordered = columns.map(column => column.key).filter(key => selection[rowKey].has(key))

    if (ordered.length > 0) {
      result[rowKey] = ordered
    }
  })

  return result
}

const DEFAULT_OPTIONS = {
  searchable: false,
  avatars: true,
  subtitles: true,
  captions: true,
  counters: true,
}

/**
 * Fills missing display options with their defaults.
 */
export function normalizeOptions(options) {
  return { ...DEFAULT_OPTIONS, ...(options || {}) }
}
