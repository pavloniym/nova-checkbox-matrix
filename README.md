# Nova Checkbox Matrix

A [Laravel Nova 5](https://nova.laravel.com) field that renders a **rows × columns checkbox matrix** — for example *people × roles*.

- Sticky header and first column, horizontal scroll on narrow screens
- Per-row avatar (with initials fallback), label and subtitle
- Client-side search over rows (case-insensitive, Unicode-aware)
- Selected-count badge in each column header
- Light and dark themes
- Works on resource forms, detail and index pages, **and in actions**
- Server-side normalization: unknown rows/columns sent by the client are discarded
- Typed value objects in and out — no domain assumptions

## Installation

```bash
composer require pavloniym/nova-checkbox-matrix
```

The service provider is auto-discovered.

## Usage

Everything is configured through small value objects: `MatrixRow`, `MatrixColumn` and `MatrixOptions`.
The submitted value comes back as a `MatrixValue` of `MatrixSelection`s.

```php
use Pavloniym\NovaCheckboxMatrix\CheckboxMatrix;
use Pavloniym\NovaCheckboxMatrix\MatrixColumn;
use Pavloniym\NovaCheckboxMatrix\MatrixOptions;
use Pavloniym\NovaCheckboxMatrix\MatrixRow;

CheckboxMatrix::make('Team', 'members')
    ->columns([
        MatrixColumn::make('translator', 'Translator'),
        MatrixColumn::make('voice_actor', 'Voice actor'),
        MatrixColumn::make('sound_engineer', 'Sound engineer'),
    ])
    ->rows($users->map(fn (User $user) => MatrixRow::make($user->id, $user->name)
        ->subtitle('@' . $user->username)   // second line
        ->caption($user->position)          // third line
        ->avatar($user->avatar_url)))       // absolute http(s) URL, initials otherwise
    ->options(MatrixOptions::make()
        ->searchable()                      // default: false
        ->avatars(true)                     // default: true
        ->subtitles(true)                   // default: true
        ->captions(true)                    // default: true
        ->counters(true))                   // default: true
    ->default(MatrixValue::fromArray([$userId => ['translator']]));
```

### Reading the value

```php
use Pavloniym\NovaCheckboxMatrix\MatrixValue;

public function handle(ActionFields $fields, Collection $models)
{
    /** @var MatrixValue|null $value  null when the field was not submitted at all */
    $value = $fields->get('members');

    foreach ($value?->selections() ?? [] as $selection) {
        $selection->rowKey;      // string
        $selection->columnKeys;  // list<string>, in column order
    }

    $value?->forRow($userId);   // ?MatrixSelection
    $value?->isEmpty();         // true when every checkbox is cleared
}
```

`fields()` is evaluated again when an action runs, so the submitted value is normalized against the same rows and columns.

### On a resource

Store the value in a JSON column cast to `array`. `MatrixValue` serializes to `{"<rowKey>": ["<columnKey>", ...]}`
and the field resolves stored JSON back into a `MatrixValue`.

### Fullscreen actions

In an action opened with `->fullscreen()` the matrix takes the viewport height and keeps its header sticky.

## Value guarantees

- Only declared row keys and column keys are kept, everything else sent by the client is dropped.
- Columns are ordered as declared; duplicates are removed.
- Rows without any checked column are omitted.
- When the request does not contain the attribute at all, the model attribute is left untouched.

## API

| Object | Method | Description |
| --- | --- | --- |
| `CheckboxMatrix` | `rows(iterable<MatrixRow>)` | Rows in display order |
| | `columns(iterable<MatrixColumn>)` | Columns in display order |
| | `options(MatrixOptions)` | Display options |
| `MatrixRow` | `make($key, $label)` | `$key` is cast to string |
| | `subtitle(?string)` / `caption(?string)` | Second and third line |
| | `avatar(?string)` | Absolute `http(s)` URL; anything else is ignored |
| `MatrixColumn` | `make($key, $label)` | `$key` is cast to string |
| `MatrixOptions` | `searchable()`, `avatars()`, `subtitles()`, `captions()`, `counters()` | Toggle UI parts |
| `MatrixValue` | `selections()`, `forRow()`, `isEmpty()`, `toArray()`, `fromArray()`, `empty()` | Submitted value |
| `MatrixSelection` | `rowKey`, `columnKeys` | Checked columns of one row |

## Translations

The component uses Nova's `__()` helper with these keys: `Search`, `No rows`, `No results found.` Add them to your `lang/{locale}.json` to translate.

## Development

```bash
composer install          # needs access to laravel/nova for the JS build
npm install
npm run prod              # builds dist/
vendor/bin/phpunit
```

`dist/` is committed so consumers do not need a JS toolchain.

## License

MIT
