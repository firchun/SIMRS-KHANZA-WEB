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

        const res = await fetch(this.baseUrl + path, opts);
        const json = await res.json();

        if (!res.ok) {
            if (res.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
            throw new Error(json.message || 'Request failed');
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
