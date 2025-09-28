export const JODIT_JS_CDN = 'https://cdn.jsdelivr.net/npm/jodit@3.24.7/build/jodit.min.js';
export const JODIT_CSS_CDN = 'https://cdn.jsdelivr.net/npm/jodit@3.24.7/build/jodit.min.css';

export const DEFAULT_BUTTONS = 'undo,redo,|,paragraph,|,bold,italic,underline,|,ul,ol,indent,outdent,|,link,unlink,|,table,blockquote,|,source';

export function createJoditConfig(overrides = {}) {
    return {
        toolbarSticky: false,
        minHeight: 260,
        buttons: DEFAULT_BUTTONS,
        ...overrides,
    };
}

