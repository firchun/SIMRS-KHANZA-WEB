const APP_SECRET = 'KhanzaWeb-2026-Secure!@#';
let derivedKey = null;

function simpleHash(str) {
    let hash = 5381;
    for (let i = 0; i < str.length; i++) {
        hash = ((hash << 5) + hash) + str.charCodeAt(i);
    }
    return (hash >>> 0).toString(36);
}

function xorCrypt(text, key) {
    let result = '';
    for (let i = 0; i < text.length; i++) {
        result += String.fromCharCode(text.charCodeAt(i) ^ key.charCodeAt(i % key.length));
    }
    return result;
}

function getKey() {
    return derivedKey || simpleHash(APP_SECRET);
}

function encrypt(value) {
    const str = String(value);
    const key = getKey();
    const encrypted = xorCrypt(str, key);
    return btoa(encodeURIComponent(encrypted));
}

function decrypt(encoded) {
    const key = getKey();
    try {
        const encrypted = decodeURIComponent(atob(encoded));
        return xorCrypt(encrypted, key);
    } catch {
        try {
            const fallbackKey = simpleHash(APP_SECRET);
            if (fallbackKey === key) return null;
            const encrypted = decodeURIComponent(atob(encoded));
            return xorCrypt(encrypted, fallbackKey);
        } catch {
            return null;
        }
    }
}

async function deriveKey() {
    try {
        const encoder = new TextEncoder();
        const hashBuffer = await crypto.subtle.digest('SHA-256', encoder.encode(APP_SECRET));
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        derivedKey = hashArray.map(b => String.fromCharCode(b)).join('');
    } catch {
        derivedKey = null;
    }
}

(function() {
    const origSetItem = Storage.prototype.setItem;
    const origGetItem = Storage.prototype.getItem;
    const origRemoveItem = Storage.prototype.removeItem;
    const origClear = Storage.prototype.clear;
    const origKey = Storage.prototype.key;

    Storage.prototype.setItem = function(key, value) {
        try {
            const encrypted = encrypt(value);
            origSetItem.call(this, key, '__kc:' + encrypted);
        } catch {
            origSetItem.call(this, key, value);
        }
    };

    Storage.prototype.getItem = function(key) {
        const val = origGetItem.call(this, key);
        if (val === null || val === undefined) return null;
        if (typeof val === 'string' && val.startsWith('__kc:')) {
            try {
                return decrypt(val.slice(5));
            } catch {
                return val;
            }
        }
        return val;
    };

    Storage.prototype.removeItem = function(key) {
        origRemoveItem.call(this, key);
    };

    Storage.prototype.clear = function() {
        origClear.call(this);
    };

    Storage.prototype.key = function(index) {
        return origKey.call(this, index);
    };
})();

deriveKey();

export { encrypt, decrypt };
