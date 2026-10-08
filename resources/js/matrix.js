/**
 * Turns a field value (object, array or JSON string) into { rowKey: Set<columnKey> }.
 */
export function toSelection(value) {
  let parsed = value

  if (typeof parsed === 'string') {
    try {
      parsed = JSON.parse(parsed)
    } catch (e) {
      parsed = {}
    }
  }

  const selection = {}

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
 * Serializes a selection into a plain { rowKey: [columnKey] } object, dropping empty rows
 * and keeping the declared column order.
 */
export function fromSelection(selection, columns) {
  const result = {}

  Object.entries(selection).forEach(([rowKey, set]) => {
    const ordered = columns.map(column => column.key).filter(key => set.has(key))

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
