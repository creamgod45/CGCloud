# FilePond v4 to v5 Migration Guide (Current Project)

This document outlines the steps required to migrate the current project from FilePond v4 to v5.

## 1. Dependency Changes (`package.json`)

Update the dependencies to their v5 equivalents. Note that plugins are now "extensions".

```json
{
  "dependencies": {
    "filepond": "^5.0.0",
    "filepond-extensions": "^1.0.0"
  }
}
```

## 2. Initialization Refactor (`resources/js/components/filepond.js`)

In v5, we move away from `FilePond.create()` to a more declarative or extension-based setup.

### Current (v4)
```javascript
import * as FilePond from 'filepond';
FilePond.registerPlugin(FilePondPluginImagePreview);
let pond = FilePond.create(element, options);
```

### Proposed (v5)
```javascript
import { defineFilePond } from 'filepond';
import { 
    FormPostStore, 
    ImagePreview, 
    FileValidateSize, 
    FileValidateType,
    FilePoster,
    ImageValidateSize,
    ImageTransform
} from 'filepond-extensions';

// Define the custom element and its extensions
defineFilePond({
    extensions: [
        FormPostStore,
        ImagePreview,
        FileValidateSize,
        FileValidateType,
        FilePoster,
        ImageValidateSize,
        ImageTransform
    ]
});
```

## 3. Configuration Mapping

| Feature | v4 Property | v5 Property / Extension |
| :--- | :--- | :--- |
| **Server** | `server: { process, revert, patch }` | `FormPostStore` (url), `ChunkedUploadStore` |
| **Initial Files** | `files` | `entries` |
| **Multiple** | `allowMultiple` | `multiple` |
| **Locale** | `FilePond.setOptions(zh_tw)` | `locale` property |
| **Max File Size** | `maxFileSize` | `max-file-size` (on element or config) |
| **Thumbnails** | `imageTransformVariants` | Integrated in `ImageTransform` extension |

## 4. Blade Template Updates

Update the HTML to use the custom element.

### Before
```html
<input type="file" class="filepond" data-upload="{{ route(...) }}" ... />
```

### After
```html
<file-pond
    multiple
    locale="zh-tw"
    extension-form-post-store-url="{{ route(...) }}"
    extension-form-post-store-revert-url="{{ route(...) }}"
    entries='@json($initialFiles)'
></file-pond>
```

## 5. CSS / Styling

The project currently uses `resources/css/components/_filepond.scss` with `@apply`.
In v5, use CSS Custom Properties for better integration with the Shadow DOM.

```css
file-pond {
    --file-pond-panel-background-color: theme('colors.slate.50');
    --file-pond-panel-border-color: theme('colors.slate.300');
}
```

## 6. Events & Integration

*   **Form Submit**: V5 handles `storeAsFile` by default. The `filepondElement.uploaded` check in `form.onsubmit` should be updated to check the pond's internal state.
*   **Heartbeat**: Use the `entries` change event to trigger the heartbeat logic.

## 7. Next Steps

1.  Create a branch for migration.
2.  Update `package.json` and run `npm install`.
3.  Rewrite `resources/js/components/filepond.js`.
4.  Update Blade templates incrementally.
5.  Refactor SCSS to use CSS variables.
