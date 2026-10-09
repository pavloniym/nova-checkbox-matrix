import test from 'node:test'
import assert from 'node:assert/strict'
import { countChecked, fromSelection, isChecked, toSelection, toggle } from '../../resources/js/matrix.js'

const columns = [{ key: 'x' }, { key: 'y' }]

// What the form actually sends to the server
const sent = selection => JSON.parse(JSON.stringify(fromSelection(selection, columns)))
const RISKY_KEYS = ['constructor', 'toString', 'hasOwnProperty', '__proto__', 'valueOf']

test('toSelection accepts objects, arrays and JSON strings', () => {
  assert.deepEqual(sent(toSelection({ a: ['x'] })), { a: ['x'] })
  assert.deepEqual(sent(toSelection('{"a":["y","x"]}')), { a: ['x', 'y'] })
  assert.deepEqual(sent(toSelection([['x']])), { 0: ['x'] })
  assert.deepEqual(sent(toSelection('not json')), {})
  assert.deepEqual(sent(toSelection(null)), {})
})

test('nothing is checked for rows named like Object.prototype members', () => {
  const selection = toSelection({})

  for (const key of RISKY_KEYS) {
    assert.equal(isChecked(selection, key, 'x'), false, key)
  }
})

test('rows named like Object.prototype members can be toggled and serialized', () => {
  let selection = toSelection({})

  for (const key of RISKY_KEYS) {
    selection = toggle(selection, key, 'x')
    assert.equal(isChecked(selection, key, 'x'), true, key)
  }

  const value = fromSelection(selection, columns)

  for (const key of RISKY_KEYS) {
    assert.deepEqual(Object.getOwnPropertyDescriptor(value, key)?.value, ['x'], key)
  }
  assert.equal(JSON.stringify(value).includes('"__proto__":["x"]'), true)
})

test('a "__proto__" key from the server does not replace the prototype', () => {
  const selection = toSelection('{"__proto__":["x"],"a":["y"]}')

  assert.equal(isChecked(selection, '__proto__', 'x'), true)
  assert.equal(isChecked(selection, 'a', 'y'), true)
  assert.equal({}.polluted, undefined)
})

test('toggle unchecks and keeps other rows', () => {
  let selection = toSelection({ a: ['x', 'y'], b: ['x'] })

  selection = toggle(selection, 'a', 'x')

  assert.deepEqual(sent(selection), { a: ['y'], b: ['x'] })
})

test('countChecked counts declared rows and columns only', () => {
  const selection = toSelection({ a: ['x', 'y'], b: ['x'], ghost: ['x'], constructor: ['y'] })

  assert.deepEqual(countChecked(selection, ['a', 'b', 'constructor'], columns), { x: 2, y: 2 })
})
