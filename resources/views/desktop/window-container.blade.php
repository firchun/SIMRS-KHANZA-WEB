    <template x-for="win in $store.windows.items" :key="win.id">
    <div x-data="{
        id: win.id,
        isMaximized: false,
        prevPos: null,
        minW: 400,
        minH: 300,
        loading: true,
        init() {
            this.loadContent();
        },
        async loadContent() {
            this.loading = true;
            try {
                const res = await fetch('/desktop/modules/' + win.module, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = res.ok ? await res.text() : '<div class=\'flex items-center justify-center h-full text-red-400\'>Gagal memuat</div>';
                const el = this.$refs.moduleContainer;
                if (el) { el.innerHTML = html; this.$nextTick(() => { try { window.Alpine.initTree(el); } catch(e) { console.log('Alpine init error:', e); } }); }
            } catch(e) { 
                console.log('Load error:', e);
                const el = this.$refs.moduleContainer;
                if (el) el.innerHTML = '<div class=\'flex items-center justify-center h-full text-red-400\'>Error: ' + e.message + '</div>';
            } finally {
                this.loading = false;
            }
        },
        close() { this.$store.windows.close(this.id); },
        minimize() { this.$store.windows.minimize(this.id); },
        doFocus() {
            if (this.$store.windows.activeId !== this.id) {
                this.$store.windows.focus(this.id);
            }
        },
        startDrag(e) {
            if (this.isMaximized) return;
            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) return;
            this.doFocus();
            const oX = e.clientX - win.x;
            const oY = e.clientY - win.y;
            const move = (ev) => {
                const newX = Math.max(0, Math.min(ev.clientX - oX, window.innerWidth - win.width));
                const newY = Math.max(0, Math.min(ev.clientY - oY, window.innerHeight - win.height - 48));
                this.$store.windows.updatePos(win.id, newX, newY);
            };
            const up = () => { document.removeEventListener('mousemove', move); document.removeEventListener('mouseup', up); this.$store.windows.saveState(); };
            document.addEventListener('mousemove', move);
            document.addEventListener('mouseup', up);
        },
        startResize(dir, e) {
            if (this.isMaximized) return;
            e.stopPropagation();
            this.doFocus();
            const startX = e.clientX;
            const startY = e.clientY;
            const startW = win.width;
            const startH = win.height;
            const startLeft = win.x;
            const startTop = win.y;
            const move = (ev) => {
                const dx = ev.clientX - startX;
                const dy = ev.clientY - startY;
                let newW = startW, newH = startH, newX = startLeft, newY = startTop;
                if (dir.includes('e')) {
                    newW = Math.max(this.minW, startW + dx);
                }
                if (dir.includes('s')) {
                    newH = Math.max(this.minH, startH + dy);
                }
                this.$store.windows.updateSize(win.id, newX, newY, newW, newH);
            };
            const up = () => { document.removeEventListener('mousemove', move); document.removeEventListener('mouseup', up); this.$store.windows.saveState(); };
            document.addEventListener('mousemove', move);
            document.addEventListener('mouseup', up);
        },
        toggleMaximize() {
            if (!this.isMaximized) {
                this.prevPos = { x: win.x, y: win.y, width: win.width, height: win.height };
                this.$store.windows.updateSize(win.id, 0, 0, window.innerWidth, window.innerHeight - 48);
            } else if (this.prevPos) {
                this.$store.windows.updateSize(win.id, this.prevPos.x, this.prevPos.y, this.prevPos.width, this.prevPos.height);
            }
            this.isMaximized = !this.isMaximized;
            this.$store.windows.saveState();
        }
    }"
        x-show="!win.minimized"
        x-cloak
        class="window group"
        :data-window-id="win.id"
        :class="$store.windows.activeId === win.id ? 'window-active' : 'window-inactive'"
        :style="'left:' + win.x + 'px; top:' + win.y + 'px; width:' + win.width + 'px; height:' + win.height + 'px; z-index:' + win.zIndex">

        <div class="window-header" @mousedown="startDrag($event)">
            <div class="window-title"><span x-text="win.title"></span></div>
            <div class="flex items-center gap-1.5">
                <button @click="minimize()" class="window-btn window-btn-min" title="Minimize"></button>
                <button @click="toggleMaximize()" class="window-btn window-btn-max" title="Maximize"></button>
                <button @click="close()" class="window-btn window-btn-close" title="Close"></button>
            </div>
        </div>

        <div class="window-body" @mousedown.stop @click.stop>
            <div x-show="loading" class="h-full overflow-auto p-4 space-y-3 animate-pulse">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                    <div class="space-y-2 flex-1">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/5"></div>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-3 mb-4">
                    <div class="h-24 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="h-24 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="h-24 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="h-24 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                </div>
                <div class="flex gap-3 mb-4">
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                </div>
                <div class="space-y-2">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-4/6"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
                    <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded-lg mt-4"></div>
                </div>
            </div>
            <div x-ref="moduleContainer" class="h-full overflow-auto" x-show="!loading"></div>
        </div>

        <div x-show="!isMaximized"
            class="absolute inset-y-0 right-0 w-1.5 cursor-ew-resize opacity-0 group-hover:opacity-100 hover:opacity-100 transition-opacity"
            style="top:0;background:transparent;" @mousedown="startResize('e', $event)"></div>

        <div x-show="!isMaximized"
            class="absolute inset-x-0 bottom-0 h-1.5 cursor-ns-resize opacity-0 group-hover:opacity-100 hover:opacity-100 transition-opacity"
            style="left:0;background:transparent;" @mousedown="startResize('s', $event)"></div>

        <div x-show="!isMaximized"
            class="resize-corner absolute bottom-0 right-0 w-4 h-4 cursor-nwse-resize opacity-0 group-hover:opacity-100 hover:opacity-100 transition-opacity"
            style="background:transparent;" @mousedown="startResize('se', $event)">
        </div>
    </div>
</template>
