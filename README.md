# Nova Checkbox Matrix

A [Laravel Nova 5](https://nova.laravel.com) field that renders a **rows × columns checkbox matrix** — for example *people × roles*.

- Sticky header and first column, horizontal scroll on narrow screens
- Per-row avatar (with initials fallback), label and subtitle
- Client-side search over rows (case-insensitive, Unicode-aware)
- Selected-count badge in each column header
- Light and dark themes
- Works on resource forms, detail and index pages, **and in actions**
- Server-side normalization: unknown rows/columns sent by the client are discarded

## Installation

```bash
composer require pavloniym/nova-checkbox-matrix
```

The service provider is auto-discovered.

## Usage

```php
use Pavloniym\NovaCheckboxMatrix\CheckboxMatrix;
use Pavloniym\NovaCheckboxMatrix\MatrixRow;

CheckboxMatrix::make('Team', 'members')
    ->rows($users->map(fn (User $user) => MatrixRow::make($user->id, $user->name)
        ->subtitle('@' . $user->username)
        ->avatar($user->avatar_url)))
    ->columns([
        'translator' => 'Translator',
        'voice_actor' => 'Voice actor',
        'sound_engineer' => 'Sound engineer',
    ])
    ->default([$userId => ['translator']])
    ->searchable();
```

### In an action

```php
public function fields(NovaRequest $request): array
{
    return [
        CheckboxMatrix::make('Members', 'members')->rows($rows)->columns($columns)->default($current),
    ];
}

public function handle(ActionFields $fields, Collection $models)
{
    $members = $fields->get('members'); // ['<rowKey>' => ['translator', 'voice_actor'], ...]
}
```

`fields()` is evaluated again when the action runs, so the submitted value is normalized against the same rows and columns.

### On a resource

Store the value in a JSON column cast to `array`. The field resolves and fills it through the same normalizer.

## Value format

```json
{ "<rowKey>": ["<columnKey>", "..."] }
```

- Only declared row keys and column keys are kept, everything else is dropped.
- Columns are ordered as declared; duplicates are removed.
- Rows without any checked column are omitted, so an empty matrix is `{}` / `[]`.
- Numeric row keys follow PHP array semantics and come back as integer array keys.
- When the request does not contain the attribute at all, the model attribute is left untouched.

## API

| Method | Description |
| --- | --- |
| `rows(iterable<MatrixRow>)` | Rows in display order |
| `columns(array<string, string>)` | Column key → label, in display order |
| `searchable(bool = true)` | Show the search input above the matrix |
| `MatrixRow::make($key, $label)` | Row definition; `$key` is cast to string |
| `->subtitle(?string)` | Secondary line under the label |
| `->avatar(?string)` | Absolute `http(s)` image URL; anything else is ignored and initials are shown |

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
