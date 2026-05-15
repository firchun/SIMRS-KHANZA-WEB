import Alpine from 'alpinejs';

function pushError(info) {
    try { Alpine.store('errors')?.add(info); } catch {}
}

export const api = {
    baseUrl: '/api',
    _cache: {},
    _cacheTTL: 60000,

    async request(method, path, data = null) {
        const cacheKey = 'GET:' + path;
        const now = Date.now();

        if (method === 'GET' && this._cache[cacheKey] && (now - this._cache[cacheKey].ts) < this._cacheTTL) {
            return this._cache[cacheKey].data;
        }

        const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
        const token = localStorage.getItem('token');
        if (token) headers['Authorization'] = 'Bearer ' + token;

        const opts = { method, headers };
        if (data) opts.body = JSON.stringify(data);

        let res, json;
        try {
            res = await fetch(this.baseUrl + path, opts);
            const ct = res.headers.get('content-type') || '';
            if (ct.includes('application/json')) {
                json = await res.json();
            } else {
                const text = await res.text();
                json = { message: text.slice(0, 500) };
            }
        } catch (e) {
            pushError({ type: 'Network Error', message: e.message, file: path, timestamp: Date.now() });
            throw e;
        }

        if (!res.ok) {
            if (res.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
            const msg = json.message || 'Request failed (' + res.status + ')';
            pushError({ type: 'API ' + res.status, message: msg, file: path, timestamp: Date.now() });
            throw new Error(msg);
        }

        if (method === 'GET') {
            this._cache[cacheKey] = { data: json, ts: now };
        }

        return json;
    },

    get(path) { return this.request('GET', path); },
    post(path, data) { return this.request('POST', path, data); },
    put(path, data) { return this.request('PUT', path, data); },
    delete(path, data) { return this.request('DELETE', path, data); },

    cacheBust(path) {
        if (path) {
            delete this._cache['GET:' + path];
        } else {
            this._cache = {};
        }
    },

    setCacheTTL(ms) {
        this._cacheTTL = ms;
    }
};
