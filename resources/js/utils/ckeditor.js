const LTS_URL = 'https://cdn.ckeditor.com/4.25.1-lts/standard-all/ckeditor.js';
const FREE_URL = 'https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js';

const env = (typeof import.meta !== 'undefined' && import.meta.env) ? import.meta.env : {};
const licenseKey = (env && env.VITE_CKEDITOR4_LICENSE_KEY) || (typeof window !== 'undefined' ? window.CKEDITOR4_LICENSE_KEY : '');
const ltsFlag = env && (env.VITE_CKEDITOR4_LTS === '1' || env.VITE_CKEDITOR4_LTS === 'true');
const overrideUrl = env && (env.VITE_CKEDITOR4_URL || '').trim();
const useLts = (!!licenseKey && ltsFlag !== false) && (ltsFlag || true);

export const CKEDITOR_URL = overrideUrl || (useLts ? LTS_URL : FREE_URL);

export const DEFAULT_TOOLBAR = [
    ['Undo', 'Redo'],
    ['Format', 'Bold', 'Italic', 'Underline', 'RemoveFormat'],
    ['NumberedList', 'BulletedList', 'Outdent', 'Indent'],
    ['Link', 'Unlink', 'Blockquote', 'Table'],
    ['Source'],
];

function mergeRemovePlugins(defaults, extra) {
    const a = String(defaults || '')
        .split(',')
        .map((s) => s.trim())
        .filter(Boolean);
    const b = String(extra || '')
        .split(',')
        .map((s) => s.trim())
        .filter(Boolean);
    const set = new Set([...a, ...b]);
    return Array.from(set).join(',');
}

export const createEditorConfig = (overrides = {}) => {
    const base = {
        toolbar: DEFAULT_TOOLBAR,
        height: 260,
        // Hide CKEditor UI notifications (including "not secure" banner) in non-LTS builds
        removePlugins: 'elementspath,notification',
        resize_enabled: false,
    };
    if (useLts && licenseKey) {
        base.licenseKey = licenseKey;
    }
    const cfg = { ...base, ...overrides };
    // Merge removePlugins from overrides without duplications
    cfg.removePlugins = mergeRemovePlugins(base.removePlugins, overrides.removePlugins);
    return cfg;
};
