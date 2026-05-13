<div x-data="{
    activeTab: 'ai',
    chatEnd: null,

    // Chatbot AI
    messages: [],
    input: '',
    loading: false,
    systemPrompt: 'Kamu adalah asisten AI untuk SIMRS Khanza. Bantu pengguna dengan pertanyaan seputar sistem informasi rumah sakit.',

    // Chat User
    users: [
        { id: 1, name: 'Dr. Andi Pratama', role: 'Dokter', avatar: 'AP', online: true, lastSeen: 'online' },
        { id: 2, name: 'Siti Rahmawati', role: 'Perawat', avatar: 'SR', online: true, lastSeen: 'online' },
        { id: 3, name: 'Bambang Susilo', role: 'Apoteker', avatar: 'BS', online: false, lastSeen: '2 jam lalu' },
        { id: 4, name: 'Dewi Sartika', role: 'Admin', avatar: 'DS', online: true, lastSeen: 'online' },
        { id: 5, name: 'Rudi Hartono', role: 'Kasir', avatar: 'RH', online: false, lastSeen: '1 jam lalu' },
        { id: 6, name: 'Fitriani', role: 'Laborat', avatar: 'FT', online: false, lastSeen: '3 jam lalu' },
    ],
    conversations: {
        1: [
            { from: 1, text: 'Selamat pagi, ada pasien baru yang perlu diperiksa?', time: '09:15' },
            { from: 0, text: 'Pagi dok, ada pasien atas nama Budi Santoso di ruang 3.', time: '09:17' },
            { from: 1, text: 'Baik, saya akan segera ke sana.', time: '09:18' },
        ],
        2: [
            { from: 2, text: 'Permisi, untuk pasien Ani di ranap butuh infus diganti.', time: '10:30' },
            { from: 0, text: 'Baik bu Siti, akan saya siapkan.', time: '10:32' },
            { from: 2, text: 'Terima kasih.', time: '10:33' },
        ],
    },
    activeChat: null,
    chatInput: '',
    currentUser: { id: 0, name: 'Saya', role: 'User', avatar: 'SY' },

    init() {
        this.messages = [
            { role: 'assistant', content: 'Halo! Saya asisten AI SIMRS Khanza. Ada yang bisa saya bantu?' }
        ];
    },

    // Chatbot AI methods
    async send() {
        const text = this.input.trim();
        if (!text || this.loading) return;
        this.input = '';
        this.messages.push({ role: 'user', content: text });
        this.loading = true;
        this.$nextTick(() => this.scrollDown());

        if (!this.$store.ai.isConfigured()) {
            this.messages.push({ role: 'assistant', content: '\u26a0\ufe0f **AI belum dikonfigurasi.** Silakan atur endpoint dan API Key di menu **Settings \u2192 Chatbot AI** terlebih dahulu.' });
            this.loading = false;
            this.$nextTick(() => this.scrollDown());
            return;
        }

        try {
            const payload = {
                model: this.$store.ai.model,
                messages: [
                    { role: 'system', content: this.systemPrompt },
                    ...this.messages.filter(m => m.role !== 'system').map(m => ({ role: m.role, content: m.content }))
                ],
                max_tokens: 1024,
                temperature: 0.7
            };

            const res = await fetch(this.$store.ai.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + this.$store.ai.apiKey
                },
                body: JSON.stringify(payload)
            });

            if (!res.ok) {
                const errText = await res.text().catch(() => '');
                throw new Error(res.status + ' ' + res.statusText + (errText ? ': ' + errText.slice(0, 200) : ''));
            }

            const json = await res.json();
            const reply = json.choices?.[0]?.message?.content || 'Maaf, tidak ada respon dari AI.';
            this.messages.push({ role: 'assistant', content: reply });
        } catch (e) {
            this.messages.push({ role: 'assistant', content: '\u26a0\ufe0f **Error:** ' + e.message });
        }
        finally {
            this.loading = false;
            this.$nextTick(() => this.scrollDown());
        }
    },

    scrollDown() {
        if (this.chatEnd) this.chatEnd.scrollIntoView({ behavior: 'smooth' });
    },

    keydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.send();
        }
    },

    formatMsg(content) {
        return content
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
    },

    // Chat User methods
    openChat(user) {
        this.activeChat = user.id;
    },

    sendChat() {
        const text = this.chatInput.trim();
        if (!text || this.activeChat === null) return;
        if (!this.conversations[this.activeChat]) {
            this.conversations[this.activeChat] = [];
        }
        const now = new Date();
        const time = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
        this.conversations[this.activeChat].push({ from: 0, text, time });
        this.chatInput = '';
        this.$nextTick(() => this.scrollDown());
    },

    chatKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.sendChat();
        }
    },

    getOtherUser(userId) {
        return this.users.find(u => u.id === userId);
    },

    getUnread(userId) {
        const conv = this.conversations[userId];
        if (!conv) return 0;
        return conv.filter(m => m.from === userId && !m.read).length;
    },

    markRead(userId) {
        const conv = this.conversations[userId];
        if (conv) conv.forEach(m => { if (m.from === userId) m.read = true; });
    }
}" class="flex flex-col h-full" style="color:var(--text-primary)">

    {{-- Tabs --}}
    <div class="flex shrink-0" style="border-bottom:1px solid var(--border);background-color:var(--bg-muted)">
        <button @click="activeTab='ai'"
            class="flex items-center gap-1.5 px-4 py-2 text-xs font-medium transition-colors border-b-2"
            :class="activeTab==='ai' ? '' : 'border-transparent hover:bg-black/5 dark:hover:bg-white/5'"
            :style="activeTab==='ai' ? 'border-color:var(--accent-blue);color:var(--accent-blue)' : 'color:var(--text-muted)'">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            Chatbot AI
        </button>
        <button @click="activeTab='user'"
            class="flex items-center gap-1.5 px-4 py-2 text-xs font-medium transition-colors border-b-2 relative"
            :class="activeTab==='user' ? '' : 'border-transparent hover:bg-black/5 dark:hover:bg-white/5'"
            :style="activeTab==='user' ? 'border-color:var(--accent-blue);color:var(--accent-blue)' : 'color:var(--text-muted)'">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Chat User
        </button>
    </div>

    {{-- Tab Content: Chatbot AI --}}
    <template x-if="activeTab === 'ai'">
        <div class="flex flex-col h-full">
            {{-- Header --}}
            <div class="flex items-center gap-2 px-3 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
                <div class="flex-1">
                    <div class="text-[10px]" style="color:var(--text-muted)">
                        <span class="w-1.5 h-1.5 inline-block rounded-full mr-1" :class="$store.ai.isConfigured() ? 'bg-green-500' : 'bg-red-500'"></span>
                        <span x-text="$store.ai.isConfigured() ? 'Terhubung' : 'Belum dikonfigurasi'"></span>
                    </div>
                </div>
                <button @click="$store.ai.isConfigured() ? '' : $store.windows.open({key:'settings',label:'Settings',icon:'settings',width:960,height:680},{activeTab:'chatbot'})" class="text-[10px] px-2 py-1 rounded" style="background-color:var(--bg-hover)">
                    <template x-if="!$store.ai.isConfigured()">Konfigurasi</template>
                </button>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                <template x-for="(msg, i) in messages" :key="i">
                    <div class="flex gap-2" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[85%] rounded-lg px-3 py-2 text-xs leading-relaxed"
                            :class="msg.role === 'user' ? 'rounded-br-sm' : 'rounded-bl-sm'"
                            :style="msg.role === 'user'
                                ? 'background-color:var(--accent-blue);color:#fff'
                                : 'background-color:var(--bg-muted);color:var(--text-primary)'">
                            <div x-html="formatMsg(msg.content)"></div>
                        </div>
                    </div>
                </template>
                <template x-if="loading">
                    <div class="flex gap-2 justify-start">
                        <div class="max-w-[85%] rounded-lg rounded-bl-sm px-3 py-2 text-xs" style="background-color:var(--bg-muted);color:var(--text-muted)">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:0ms"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:150ms"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:300ms"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-ref="chatEnd" x-init="chatEnd = $el"></div>
            </div>

            {{-- Input --}}
            <div class="border-t p-2 shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
                <div class="flex items-end gap-2">
                    <div class="flex-1 relative">
                        <textarea x-model="input" @keydown="keydown($event)" placeholder="Ketik pesan..." rows="1"
                            class="form-input text-xs w-full resize-none pr-8" style="min-height:32px;max-height:96px"
                            @input="$el.style.height='auto';$el.style.height=$el.scrollHeight+'px'"></textarea>
                        <button @click="send" :disabled="!input.trim() || loading"
                            class="absolute right-1 bottom-1 p-0.5 rounded transition-colors"
                            :style="input.trim() && !loading ? 'color:var(--accent-blue)' : 'color:var(--text-muted)'">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <p class="text-[10px] mt-1 text-center" style="color:var(--text-muted)">Tekan Enter untuk kirim, Shift+Enter untuk baris baru</p>
            </div>
        </div>
    </template>

    {{-- Tab Content: Chat User --}}
    <template x-if="activeTab === 'user'">
        <div class="flex flex-1 h-full overflow-hidden">
            {{-- User List --}}
            <div class="w-52 shrink-0 overflow-y-auto" style="border-right:1px solid var(--border);background-color:var(--bg-muted)">
                <div class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wide" style="color:var(--text-muted)">Pengguna</div>
                <template x-for="user in users" :key="user.id">
                    <button @click="openChat(user.id); markRead(user.id)"
                        class="w-full text-left px-3 py-2 transition-colors flex items-center gap-2.5"
                        :class="activeChat === user.id ? 'bg-black/10 dark:bg-white/15' : 'hover:bg-black/5 dark:hover:bg-white/5'"
                        style="color:var(--text-primary)">
                        <div class="relative shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold"
                                style="background-color:var(--accent-blue);color:#fff">
                                <span x-text="user.avatar"></span>
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2"
                                :class="user.online ? 'bg-green-500' : 'bg-gray-400'"
                                style="border-color:var(--bg-muted)"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-medium truncate" x-text="user.name"></div>
                            <div class="flex items-center gap-1 text-[10px]" style="color:var(--text-muted)">
                                <span x-text="user.role"></span>
                                <template x-if="getUnread(user.id)">
                                    <span class="w-4 h-4 rounded-full bg-red-500 text-white flex items-center justify-center text-[8px] font-bold" x-text="getUnread(user.id)"></span>
                                </template>
                            </div>
                        </div>
                    </button>
                </template>
            </div>

            {{-- Conversation --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                <template x-if="activeChat !== null">
                    <div class="flex flex-col h-full">
                        {{-- Chat Header --}}
                        <div class="flex items-center gap-2 px-3 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold"
                                style="background-color:var(--accent-blue);color:#fff">
                                <span x-text="getOtherUser(activeChat)?.avatar"></span>
                            </div>
                            <div>
                                <div class="text-xs font-medium" x-text="getOtherUser(activeChat)?.name"></div>
                                <div class="text-[10px]" style="color:var(--text-muted)" x-text="getOtherUser(activeChat)?.online ? 'Online' : 'Terakhir dilihat ' + getOtherUser(activeChat)?.lastSeen"></div>
                            </div>
                        </div>

                        {{-- Messages --}}
                        <div class="flex-1 overflow-y-auto p-3 space-y-2">
                            <template x-for="(msg, i) in (conversations[activeChat] || [])" :key="i">
                                <div class="flex" :class="msg.from === 0 ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[80%] rounded-lg px-3 py-1.5 text-xs leading-relaxed"
                                        :class="msg.from === 0 ? 'rounded-br-sm' : 'rounded-bl-sm'"
                                        :style="msg.from === 0
                                            ? 'background-color:var(--accent-blue);color:#fff'
                                            : 'background-color:var(--bg-muted);color:var(--text-primary)'">
                                        <div x-text="msg.text"></div>
                                        <div class="text-[9px] mt-0.5 text-right"
                                            :style="msg.from === 0 ? 'color:rgba(255,255,255,0.7)' : 'color:var(--text-muted)'"
                                            x-text="msg.time"></div>
                                    </div>
                                </div>
                            </template>
                            <div x-ref="chatEnd" x-init="chatEnd = $el"></div>
                        </div>

                        {{-- Input --}}
                        <div class="border-t p-2 shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
                            <div class="flex items-end gap-2">
                                <div class="flex-1 relative">
                                    <textarea x-model="chatInput" @keydown="chatKeydown($event)" placeholder="Ketik pesan..." rows="1"
                                        class="form-input text-xs w-full resize-none pr-8" style="min-height:32px;max-height:96px"
                                        @input="$el.style.height='auto';$el.style.height=$el.scrollHeight+'px'"></textarea>
                                    <button @click="sendChat" :disabled="!chatInput.trim()"
                                        class="absolute right-1 bottom-1 p-0.5 rounded transition-colors"
                                        :style="chatInput.trim() ? 'color:var(--accent-blue)' : 'color:var(--text-muted)'">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="activeChat === null">
                    <div class="flex-1 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-12 h-12 mx-auto mb-2" style="color:var(--text-muted)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                            </svg>
                            <p class="text-sm" style="color:var(--text-muted)">Pilih pengguna untuk memulai chat</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
