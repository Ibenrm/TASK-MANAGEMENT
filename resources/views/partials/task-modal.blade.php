{{-- partials/task-modal.blade.php --}}

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* ── Select2 Overrides ── */
    .select2-container--default .select2-selection--single {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        height: 38px !important;
        padding: 4px 6px !important;
        display: flex !important;
        align-items: center !important;
        transition: border-color 0.15s, box-shadow 0.15s !important;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #93c5fd !important;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12) !important;
        outline: none !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #0f172a !important;
        font-size: 14px !important;
        line-height: normal !important;
        padding-left: 0 !important;
    }
    /* Make Select2 placeholder darker */
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #64748b !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 6px !important;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important;
        font-size: 14px !important;
        z-index: 9999 !important;
    }
    .select2-results__option {
        padding: 8px 12px !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #eff6ff !important;
        color: #2563eb !important;
    }
    /* ── Reset & base ────────────────────────────────── */
    #task-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(2px);
    }
    #task-modal {
        position: relative;
        width: 100%;
        max-width: 720px;
        max-height: calc(100vh - 48px);
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 24px 48px -12px rgba(0,0,0,0.18);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* ── Header ──────────────────────────────────────── */
    .tm-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        flex-shrink: 0;
    }
    .tm-header-left { display: flex; align-items: center; gap: 10px; }
    .tm-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        background: #eff6ff;
        display: flex; align-items: center; justify-content: center;
        color: #2563eb;
        flex-shrink: 0;
    }
    .tm-icon svg { width: 17px; height: 17px; }
    .tm-title { font-size: 15px; font-weight: 600; color: #0f172a; margin: 0; }
    .tm-subtitle { font-size: 13px; color: #475569; margin-top: 2px; }
    .tm-close {
        width: 30px; height: 30px; border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: #64748b;
        transition: background 0.15s, color 0.15s;
    }
    .tm-close:hover { background: #f1f5f9; color: #0f172a; }
    .tm-close svg { width: 15px; height: 15px; }

    /* ── Tab bar ─────────────────────────────────────── */
    .tm-tabs {
        display: flex;
        gap: 0;
        padding: 0 20px;
        border-bottom: 1px solid #f1f5f9;
        flex-shrink: 0;
    }
    .tm-tab {
        padding: 12px 16px;
        font-size: 13px; font-weight: 500;
        color: #475569;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.15s;
        user-select: none;
    }
    .tm-tab:hover { color: #475569; }
    .tm-tab.active { color: #2563eb; border-bottom-color: #2563eb; font-weight: 500; }

    /* ── Scrollable body ─────────────────────────────── */
    .tm-body {
        overflow-y: auto;
        padding: 20px;
        flex: 1 1 auto;
    }

    /* ── Form primitives ─────────────────────────────── */
    .tm-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
    .tm-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .tm-label .req { color: #ef4444; margin-left: 2px; }

    .tm-input, .tm-textarea {
        font-size: 14px;
        color: #0f172a;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 11px;
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.15s, box-shadow 0.15s;
        font-family: inherit;
    }
    .tm-input::placeholder, .tm-textarea::placeholder { color: #64748b; }
    .tm-input:focus, .tm-textarea:focus {
        outline: none;
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    }
    .tm-input.title-input {
        font-size: 16px;
        font-weight: 500;
        padding: 10px 13px;
        color: #0f172a;
    }
    .tm-textarea { resize: none; min-height: 180px; line-height: 1.6; }

    /* ── Rich text toolbar ───────────────────────────── */
    .tm-editor-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        transition: box-shadow 0.15s;
    }
    .tm-editor-wrap:focus-within {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    }
    .tm-toolbar {
        display: flex;
        align-items: center;
        gap: 2px;
        padding: 6px 10px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }
    .tm-tool {
        width: 28px; height: 28px; border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        color: #475569; cursor: pointer; transition: all 0.15s;
        background: transparent; border: none;
    }
    .tm-tool:hover { background: #e2e8f0; color: #0f172a; }
    .tm-tool svg { width: 14px; height: 14px; }
    .tm-tdivider { width: 1px; height: 14px; background: #e2e8f0; margin: 0 4px; }
    .tm-editor-wrap .tm-textarea { border: none; border-radius: 0; box-shadow: none !important; }

    /* ── 2-col grid ──────────────────────────────────── */
    .tm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 540px) { .tm-grid { grid-template-columns: 1fr; } }

    /* ── Divider ─────────────────────────────────────── */
    .tm-sep { border: none; border-top: 1px solid #f1f5f9; margin: 4px 0 20px; }
    .tm-section-label {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.05em; color: #475569;
        margin-bottom: 14px;
    }

    /* ── Tagify assignee ─────────────────────────────── */
    .tagify.tm-tagify {
        --tags-border-color: #e2e8f0;
        --tags-hover-border-color: #cbd5e1;
        --tags-focus-border-color: #93c5fd;
        --tag-bg: #eff6ff;
        --tag-text-color: #1d4ed8;
        --placeholder-color: #64748b; 
        border-radius: 8px;
        padding: 4px;
        border: 1px solid #e2e8f0;
        min-height: 38px;
        font-family: inherit;
        font-size: 13px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .tagify.tm-tagify:focus-within {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        outline: none;
    }
    .tagify.tm-tagify .tagify__tag {
        background: #eff6ff;
        border-radius: 99px;
        margin: 2px;
    }
    .tagify.tm-tagify .tagify__tag > div { padding: 2px 8px; }
    .tagify.tm-tagify .tagify__tag > div::before { box-shadow: none !important; background: transparent !important; }
    .tagify.tm-tagify .tagify__tag__removeBtn { color: #60a5fa; }
    .tagify.tm-tagify .tagify__tag__removeBtn:hover { background: #93c5fd; color: #fff; }
    .tagify.tm-tagify .tagify__input { color: #0f172a; font-size: 13px; }
    .tagify.tm-tagify .tagify__input::before { color: #64748b !important; }

    /* ── Footer ──────────────────────────────────────── */
    .tm-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 20px;
        border-top: 1px solid #f1f5f9;
        flex-shrink: 0;
        background: #fafafa;
    }
    .tm-footer-meta {
        display: flex; align-items: center; gap: 5px;
        font-size: 12px; color: #64748b;
    }
    .tm-footer-meta svg { width: 13px; height: 13px; }
    .tm-actions { display: flex; gap: 8px; }
    .btn-cancel {
        font-size: 13px; font-weight: 500;
        padding: 7px 16px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: background 0.12s;
    }
    .btn-cancel:hover { background: #f8fafc; }
    .btn-save {
        font-size: 13px; font-weight: 500;
        padding: 7px 18px;
        border-radius: 8px;
        border: none;
        background: #2563eb;
        color: #ffffff;
        cursor: pointer;
        display: flex; align-items: center; gap: 6px;
        transition: background 0.12s;
        box-shadow: 0 1px 3px rgba(37,99,235,0.35);
    }
    .btn-save:hover { background: #1d4ed8; }
    .btn-save svg { width: 14px; height: 14px; }

    /* ── Dark Mode Overrides ─────────────────────────── */
    html.dark #task-modal { background: #1e293b; border-color: #334155; }
    html.dark .tm-header, html.dark .tm-tabs, html.dark .tm-footer { border-color: #334155; }
    html.dark .tm-icon { background: #1e3a8a; color: #60a5fa; }
    html.dark .tm-title { color: #f8fafc; }
    html.dark .tm-subtitle { color: #94a3b8; }
    html.dark .tm-close { background: #0f172a; border-color: #334155; color: #94a3b8; }
    html.dark .tm-close:hover { background: #334155; color: #f1f5f9; }
    
    html.dark .tm-tab { color: #94a3b8; }
    html.dark .tm-tab:hover { color: #cbd5e1; }
    html.dark .tm-tab.active { color: #60a5fa; border-bottom-color: #60a5fa; }
    
    html.dark .tm-input, html.dark .tm-textarea { background: #0f172a; border-color: #334155; color: #f8fafc; }
    html.dark .tm-input.title-input { color: #f8fafc; }
    html.dark .tm-input::placeholder, html.dark .tm-textarea::placeholder { color: #64748b; }
    html.dark .tm-input:focus, html.dark .tm-textarea:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.2); }
    
    html.dark .tm-editor-wrap { border-color: #334155; }
    html.dark .tm-toolbar { background: #0f172a; border-bottom-color: #334155; }
    html.dark .tm-tool { color: #94a3b8; }
    html.dark .tm-tool:hover { background: #334155; color: #f8fafc; }
    html.dark .tm-tdivider { background: #334155; }
    
    html.dark .tm-sep { border-top-color: #334155; }
    html.dark .tm-section-label { color: #64748b; }
    
    html.dark .tm-footer { background: #0f172a; border-top-color: #334155; }
    html.dark .btn-cancel { background: #1e293b; border-color: #334155; color: #cbd5e1; }
    html.dark .btn-cancel:hover { background: #334155; }
    
    html.dark .tagify.tm-tagify { border-color: #334155; }
    html.dark .tagify.tm-tagify .tagify__input { color: #f8fafc; }
    html.dark .tagify.tm-tagify .tagify__tag { background: #1e3a8a; color: #93c5fd; }
    html.dark .tagify.tm-tagify .tagify__tag > div::before { background: transparent !important; }
    
    html.dark .select2-container--default .select2-selection--single { background: #0f172a !important; border-color: #334155 !important; }
    html.dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #f8fafc !important; }
    html.dark .select2-dropdown { background: #1e293b !important; border-color: #334155 !important; }
    html.dark .select2-results__option { color: #cbd5e1 !important; }
    html.dark .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #334155 !important; color: #f8fafc !important; }
    
    /* ── Tab content hide/show ───────────────────────── */
    [x-cloak] { display: none !important; }
</style>
@endpush


{{-- ════════════════════════════════════════════════════════
     Modal
     Controlled by x-data="{ showModal: false }" on parent
     ════════════════════════════════════════════════════════ --}}
<div x-show="showModal"
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     id="task-modal-overlay"
     @click.self="showModal = false"
     x-data="{
        activeTab: 'detail',
        editMode: false,
        task: { title: '', note: '', start_date: '', deadline_date: '' },
        todos: [],
        comments: [],
        newTodo: '',
        
        get uncompletedTodos() {
            return this.todos.filter(t => !t.is_checked);
        },
        
        get completedTodos() {
            return this.todos.filter(t => t.is_checked);
        },
        
        initModal(e) {
            const data = e.detail.task;
            if (data) {
                this.editMode = true;
                this.task = data;
                this.todos = (data.todos ? JSON.parse(JSON.stringify(data.todos)) : []).map(t => ({
                    ...t,
                    _id: t._id || Date.now() + '-' + Math.random().toString(36).substr(2, 9)
                }));
                this.comments = data.comments || [];
                this.activeTab = 'detail';
                setTimeout(() => {
                    $('#priority_id').val(data.priority_id).trigger('change');
                    $('#status_id').val(data.status_id).trigger('change');
                    // Tagify update
                    if(window.taskAssigneesTagify) {
                        window.taskAssigneesTagify.removeAllTags();
                        if(data.assignees) {
                            window.taskAssigneesTagify.addTags(data.assignees.map(a => ({ value: a.id, name: a.name, email: a.email })));
                        }
                    }
                }, 100);
            } else {
                this.editMode = false;
                this.task = { title: '', note: '', start_date: '', deadline_date: '' };
                this.todos = [];
                this.comments = [];
                this.activeTab = 'detail';
                setTimeout(() => {
                    $('#priority_id').val('').trigger('change');
                    $('#status_id').val('').trigger('change');
                    if(window.taskAssigneesTagify) window.taskAssigneesTagify.removeAllTags();
                }, 100);
            }
        },
        
        addTodo() {
            if (this.newTodo.trim() !== '') {
                this.todos.push({ 
                    _id: Date.now() + '-' + Math.random().toString(36).substr(2, 9), 
                    todo_text: this.newTodo, 
                    is_checked: false 
                });
                this.newTodo = '';
            }
        },
        
        removeTodo(todoId) {
            const index = this.todos.findIndex(t => t._id === todoId);
            if (index > -1) {
                this.todos.splice(index, 1);
            }
        }
     }"
     @modal-opened.window="initModal($event)"
     @keydown.escape.window="showModal = false">

    <div id="task-modal" @click.stop role="dialog" aria-modal="true" aria-labelledby="modal-title">

        {{-- Header --}}
        <div class="tm-header">
            <div class="tm-header-left">
                <div class="tm-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="tm-title" id="modal-title" x-text="editMode ? 'Edit Tugas' : 'Tambah Tugas'"></p>
                    <p class="tm-subtitle" x-text="editMode ? 'Perbarui detail tugas lalu klik Simpan' : 'Isi detail tugas lalu klik Simpan'"></p>
                </div>
            </div>
            <button class="tm-close" @click="showModal = false" type="button" aria-label="Tutup modal">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Tabs --}}
        <div class="tm-tabs" role="tablist">
            <button class="tm-tab" :class="{ active: activeTab === 'detail' }"
                    @click="activeTab = 'detail'" role="tab" type="button">Detail</button>
            <button class="tm-tab" :class="{ active: activeTab === 'komentar' }"
                    @click="activeTab = 'komentar'" role="tab" type="button">Komentar</button>
            <button class="tm-tab" :class="{ active: activeTab === 'aktivitas' }"
                    @click="activeTab = 'aktivitas'" role="tab" type="button">Aktivitas</button>
        </div>

        {{-- Form --}}
        <form :action="editMode ? '/tugas/' + task.id : '{{ route('tasks.store') }}'" method="POST" class="tm-body" id="task-form" @submit="document.getElementById('todos_json_input').value = JSON.stringify(todos)">
            @csrf
            <template x-if="editMode">
                <input type="hidden" name="_method" value="PUT">
            </template>
            <input type="hidden" name="todos_json" id="todos_json_input" :value="JSON.stringify(todos)">

            {{-- Form fields will go below --}}

            {{-- ── TAB: Detail ── --}}
            <div x-show="activeTab === 'detail'" role="tabpanel">

                {{-- Judul --}}
                <div class="tm-group">
                    <label class="tm-label" for="task-title">Judul tugas <span class="req">*</span></label>
                    <input id="task-title" class="tm-input title-input" type="text"
                           name="title" required x-model="task.title"
                           placeholder="Contoh: Perbaikan bug halaman login...">
                </div>

                {{-- Deskripsi --}}
                <div class="tm-group">
                    <label class="tm-label" for="task-note">Deskripsi</label>
                    <div class="tm-editor-wrap">
                        <div class="tm-toolbar" aria-label="Format teks">
                            <button class="tm-tool" type="button" title="Bold">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                            </button>
                            <button class="tm-tool" type="button" title="Italic">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 4h-9M14 20H5M15 4L9 20"/></svg>
                            </button>
                            <button class="tm-tool" type="button" title="Underline">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4v6a6 6 0 0012 0V4M4 20h16"/></svg>
                            </button>
                            <div class="tm-tdivider"></div>
                            <button class="tm-tool" type="button" title="Bullet list">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                            <button class="tm-tool" type="button" title="Ordered list">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h13M7 12h13M7 16h13M3 8h.01M3 12h.01M3 16h.01"/></svg>
                            </button>
                            <div class="tm-tdivider"></div>
                            <button class="tm-tool" type="button" title="Link">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </button>
                            <button class="tm-tool" type="button" title="Lampiran">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            </button>
                        </div>
                        <textarea id="task-note" class="tm-textarea" name="note" x-model="task.note"
                                  placeholder="Tuliskan detail, konteks, atau acceptance criteria di sini..."></textarea>
                    </div>
                </div>

                {{-- Pengaturan section --}}
                <hr class="tm-sep">
                <p class="tm-section-label">Pengaturan</p>

                <div class="tm-grid">

                    {{-- Prioritas --}}
                    <div class="tm-group">
                        <label class="tm-label">Prioritas <span class="req">*</span></label>
                        <select id="priority_id" name="priority_id" class="select2-standard" style="width: 100%;" data-placeholder="Pilih prioritas" required>
                            <option value=""></option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="tm-group">
                        <label class="tm-label">Status <span class="req">*</span></label>
                        <select id="status_id" name="status_id" class="select2-standard" style="width: 100%;" data-placeholder="Pilih status" required>
                            <option value=""></option>
                            @foreach($statusNodes as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="tm-group">
                        <label class="tm-label" for="start_date">Tanggal Mulai</label>
                        <input id="start_date" class="tm-input" type="date" name="start_date" x-model="task.start_date ? task.start_date.split('T')[0] : ''">
                    </div>

                    {{-- Deadline --}}
                    <div class="tm-group">
                        <label class="tm-label" for="deadline_date">Deadline</label>
                        <input id="deadline_date" class="tm-input" type="date" name="deadline_date" x-model="task.deadline_date ? task.deadline_date.split('T')[0] : ''">
                    </div>

                </div>{{-- /tm-grid --}}

                {{-- Assignee (Tagify) --}}
                <div class="tm-group" style="margin-top: 4px;">
                    <label class="tm-label" for="task-assignees">Tugaskan kepada</label>
                    <input id="task-assignees" class="tm-input" type="text"
                           name="assignees_raw"
                           placeholder="Ketik nama anggota...">
                    {{-- Hidden real input that receives comma-separated IDs --}}
                    <input type="hidden" name="assignees" id="task-assignees-ids">
                </div>

                {{-- To-Do List Section --}}
                <hr class="tm-sep">
                <p class="tm-section-label">To-Do List (Sub-Tugas) - Stack Mode</p>
                <div class="space-y-4 mb-4">
                    
                    {{-- Uncompleted Tasks (Belum Selesai) --}}
                    <div class="space-y-2">
                        <template x-if="uncompletedTodos.length > 0">
                            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Belum Selesai</p>
                        </template>
                        <template x-for="(todo, idx) in uncompletedTodos" :key="todo._id">
                            <div class="flex items-center gap-3 group py-1">
                                <!-- Free selection for checking -->
                                <label class="relative flex items-center justify-center shrink-0 cursor-pointer hover:scale-110 transition-transform" 
                                       title="Klik untuk menyelesaikan sub-tugas">
                                    
                                    <input type="checkbox" x-model="todo.is_checked" 
                                           class="absolute opacity-0 w-0 h-0 peer">
                                    
                                    <!-- Custom Checkbox Visuals -->
                                    <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-300 shadow-sm relative
                                                border-slate-300 bg-white text-transparent
                                                peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500 peer-focus-visible:ring-offset-2
                                                dark:border-slate-600 dark:bg-slate-800">
                                    </div>
                                </label>
                                
                                <input type="text" x-model="todo.todo_text" 
                                       class="text-slate-700 dark:text-slate-200 tm-input flex-1 py-1.5 px-3 text-sm transition-all duration-200 bg-transparent border-transparent hover:border-slate-200 focus:border-indigo-300 dark:hover:border-slate-700 dark:focus:border-indigo-500" placeholder="Nama sub-tugas">
                                
                                <button type="button" @click="removeTodo(todo._id)" class="text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity p-1" title="Hapus sub-tugas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="uncompletedTodos.length === 0 && completedTodos.length === 0">
                            <p class="text-xs text-slate-400 italic">Belum ada sub-tugas.</p>
                        </template>
                    </div>

                    {{-- Completed Tasks (Selesai) --}}
                    <div class="space-y-2 mt-2 pt-2 border-t border-slate-100 dark:border-slate-700" x-show="completedTodos.length > 0">
                        <p class="text-[11px] font-semibold text-emerald-500 uppercase tracking-wide">Selesai</p>
                        <template x-for="(todo, idx) in completedTodos" :key="todo._id">
                            <div class="flex items-center gap-3 group py-1">
                                <label class="relative flex items-center justify-center shrink-0" 
                                       :class="idx < completedTodos.length - 1 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:scale-110 transition-transform'"
                                       title="Hanya sub-tugas terakhir yang dapat dibatalkan (Stack)">
                                    
                                    <input type="checkbox" x-model="todo.is_checked" 
                                           :disabled="idx < completedTodos.length - 1"
                                           class="absolute opacity-0 w-0 h-0 peer">
                                    
                                    <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-300 shadow-sm relative
                                                border-emerald-500 bg-emerald-500 text-white
                                                peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500 peer-focus-visible:ring-offset-2
                                                dark:border-emerald-500 dark:bg-emerald-500"
                                         :class="idx < completedTodos.length - 1 ? 'opacity-60' : ''">
                                        
                                        <!-- Checkmark -->
                                        <svg class="w-3.5 h-3.5 absolute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </label>
                                
                                <input type="text" x-model="todo.todo_text" 
                                       class="tm-input flex-1 py-1.5 px-3 text-sm line-through text-slate-400 dark:text-slate-500 bg-transparent border-transparent" readonly>
                                
                                <button type="button" @click="removeTodo(todo._id)" class="text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity p-1" title="Hapus sub-tugas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Input New Todo --}}
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <input type="text" x-model="newTodo" @keydown.enter.prevent="addTodo()" class="tm-input flex-1 py-1.5 px-3 text-sm" placeholder="Tambah sub-tugas baru... (tekan enter)">
                        <button type="button" @click="addTodo()" class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900/60 font-semibold text-xs transition-colors border border-indigo-200 dark:border-indigo-800">Tambah</button>
                    </div>
                </div>

            </div>{{-- /tab detail --}}

            {{-- ── TAB: Komentar ── --}}
            <div x-show="activeTab === 'komentar'" role="tabpanel">
                <template x-if="!editMode">
                    <div class="text-center py-10">
                        <p class="text-sm text-slate-500">Komentar hanya dapat ditambahkan setelah tugas dibuat.</p>
                    </div>
                </template>
                
                <template x-if="editMode">
                    <div class="flex flex-col h-[300px]">
                        <!-- Comments List -->
                        <div class="flex-1 overflow-y-auto space-y-4 pr-2 mb-4">
                            <template x-for="comment in comments" :key="comment.id">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 font-bold flex items-center justify-center shrink-0 text-xs">
                                        <span x-text="comment.user ? comment.user.name.charAt(0).toUpperCase() : 'U'"></span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-3 border border-slate-100 dark:border-slate-700">
                                            <div class="flex justify-between items-start mb-1">
                                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300" x-text="comment.user ? comment.user.name : 'User'"></span>
                                                <span class="text-[10px] text-slate-400" x-text="new Date(comment.created_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'})"></span>
                                            </div>
                                            <p class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-line" x-text="comment.comment_text"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="comments.length === 0">
                                <div class="text-center py-6">
                                    <p class="text-sm text-slate-500 italic">Belum ada komentar.</p>
                                </div>
                            </template>
                        </div>
                        
                        <!-- New Comment Input -->
                        <div class="mt-auto">
                            <div class="tm-group mb-0">
                                <textarea name="new_comment" class="tm-textarea" rows="2" placeholder="Tuliskan komentar baru..."></textarea>
                                <p class="text-[10px] text-slate-400 mt-1">Komentar akan disimpan saat Anda mengeklik "Perbarui Tugas".</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ── TAB: Aktivitas ── --}}
            <div x-show="activeTab === 'aktivitas'" role="tabpanel">
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-4">Riwayat Penyelesaian Sub-Tugas</h3>
                    
                    <div class="space-y-4">
                        <template x-for="todo in todos.filter(t => t.is_checked).sort((a,b) => new Date(b.updated_at || Date.now()) - new Date(a.updated_at || Date.now()))" :key="todo._id || todo.id || todo.todo_text">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-700 dark:text-slate-300">Menyelesaikan sub-tugas <span class="font-semibold" x-text="todo.todo_text"></span></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="todo.updated_at ? new Date(todo.updated_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'}) : 'Baru saja'"></p>
                                </div>
                            </div>
                        </template>
                        
                        <template x-if="todos.filter(t => t.is_checked).length === 0">
                            <div class="text-center py-6">
                                <p class="text-sm text-slate-500 dark:text-slate-400 italic">Belum ada sub-tugas yang diselesaikan.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </form>

        {{-- Footer --}}
        <div class="tm-footer">
            <div class="tm-footer-meta">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Hanya anggota tim yang dapat melihat
            </div>
            <div class="tm-actions">
                <button class="btn-cancel" type="button" @click="showModal = false">Batal</button>
                <button class="btn-save" type="submit" form="task-form">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Tugas
                </button>
            </div>
        </div>

    </div>{{-- /#task-modal --}}
</div>{{-- /#task-modal-overlay --}}


@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tagify for assignee field ──────────────────────
    const assigneeUsers = @json($users->map(fn($u) => [
        'value' => $u->full_name ?? $u->name,
        'id'    => $u->id,
    ]));

    const tagInput = document.getElementById('task-assignees');
    const hiddenIds = document.getElementById('task-assignees-ids');

    if (tagInput) {
        const tagify = new Tagify(tagInput, {
            whitelist: assigneeUsers,
            enforceWhitelist: true,
            maxTags: 10,
            dropdown: {
                maxItems: 20,
                classname: 'tagify-dropdown',
                enabled: 1,
                closeOnSelect: false,
            },
            classNames: { tag: 'tagify__tag' },
        });

        tagInput.classList.add('tm-tagify');

        // Sync selected IDs to hidden input on every change
        tagify.on('change', () => {
            const ids = tagify.value.map(t => t.id).join(',');
            hiddenIds.value = ids;
        });
    }

    // ── Re-init when modal opens (Alpine x-show toggle) ──
    // Tagify doesn't need re-init; it's always mounted.
    
    // Initialize Select2
    window.initModalSelect2 = function() {
        $('.select2-standard').select2({
            dropdownParent: $('#task-modal'),
            placeholder: function() { $(this).data('placeholder'); },
            allowClear: true,
            minimumResultsForSearch: Infinity // hides search box since options are few
        });
    };

    setTimeout(window.initModalSelect2, 100);

    // Make sure it reinitializes if user clicks trigger
    $(document).on('click', '[x-on\\:click*="showModal = true"], [@click*="showModal = true"]', function() {
        setTimeout(window.initModalSelect2, 50);
    });

});
</script>
@endpush