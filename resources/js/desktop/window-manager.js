const STORAGE_KEY = 'khanza_windows';
const TASKBAR_HEIGHT = 48;

function maxWinHeight() {
    return Math.max(300, window.innerHeight - TASKBAR_HEIGHT);
}

function clampHeight(h) {
    return Math.min(h, maxWinHeight());
}

export const windowManager = {
    items: [],
    zIndex: 100,
    counter: 0,
    activeId: null,

    _save() {
        try {
            const data = {
                items: this.items.map(w => ({
                    id: w.id,
                    module: w.module,
                    title: w.title,
                    icon: w.icon,
                    data: w.data,
                    zIndex: w.zIndex,
                    minimized: w.minimized,
                    x: w.x,
                    y: w.y,
                    width: w.width,
                    height: w.height,
                })),
                zIndex: this.zIndex,
                counter: this.counter,
                activeId: this.activeId,
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        } catch {}
    },

    load() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;
            const data = JSON.parse(raw);
            if (data.items) {
                this.items = data.items.map(w => ({
                    ...w,
                    height: clampHeight(w.height),
                    y: Math.min(w.y, maxWinHeight() - 200),
                }));
            }
            if (data.zIndex) this.zIndex = data.zIndex;
            if (data.counter) this.counter = data.counter;
            if (data.activeId) this.activeId = data.activeId;
        } catch {
            localStorage.removeItem(STORAGE_KEY);
        }
    },

    nextZ() {
        return ++this.zIndex;
    },

    open(module, data = {}) {
        const id = 'win-' + (++this.counter);
        const win = {
            id,
            module: module.key,
            title: module.label,
            icon: module.icon,
            data,
            zIndex: this.nextZ(),
            minimized: false,
            x: 40 + (this.items.length * 20) % 600,
            y: Math.min(40 + (this.items.length * 20) % 400, maxWinHeight() - 200),
            width: module.width || 640,
            height: clampHeight(module.height || 480),
        };
        this.items = [...this.items, win];
        this.activeId = id;
        this._save();
        return win;
    },

    close(id) {
        const idx = this.items.findIndex(w => w.id === id);
        if (idx > -1) {
            if (this.items[idx].onclose) this.items[idx].onclose();
            this.items = this.items.filter(w => w.id !== id);
            if (this.activeId === id) {
                const remaining = this.items;
                this.activeId = remaining.length ? remaining[remaining.length - 1].id : null;
            }
        }
        this._save();
    },

    minimize(id) {
        this.items = this.items.map(w => w.id === id ? { ...w, minimized: !w.minimized } : w);
        if (this.activeId === id) {
            const remaining = this.items.filter(w => !w.minimized);
            this.activeId = remaining.length ? remaining[remaining.length - 1].id : null;
        }
        this._save();
    },

    focus(id) {
        const z = this.nextZ();
        const win = this.items.find(w => w.id === id);
        if (win?.minimized) return;
        this.activeId = id;
        const updated = this.items.map(w => w.id === id ? { ...w, zIndex: z } : w);
        this.items = [...updated.filter(w => w.id !== id), ...updated.filter(w => w.id === id)];
        this._save();
    },

    restore(id) {
        const z = this.nextZ();
        this.activeId = id;
        const updated = this.items.map(w => w.id === id ? { ...w, minimized: false, zIndex: z } : w);
        this.items = [...updated.filter(w => w.id !== id), ...updated.filter(w => w.id === id)];
        this._save();
    },

    saveState() {
        this._save();
    },

    updatePos(id, x, y) {
        this.items = this.items.map(w => w.id === id ? { ...w, x, y } : w);
    },

    updateSize(id, x, y, width, height) {
        this.items = this.items.map(w => w.id === id ? { ...w, x, y, width, height: clampHeight(height) } : w);
    },

    clearAll() {
        this.items = [];
        this.activeId = null;
        this._save();
    }
};
