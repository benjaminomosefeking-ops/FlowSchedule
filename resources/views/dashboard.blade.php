<x-public-layout>
    <x-slot name="title">Dashboard | FlowSchedule</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush

    @include('layouts.navigation')

    <!-- DASHBOARD CONTENT -->
    <main class="dashboard-wrap">

        <!-- Hero con información del usuario -->
        <section class="user-hero">
            <div class="user-hero-content">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="user-hero-text">
                    <span class="eyebrow blue">
                        Dashboard · {{ \Carbon\Carbon::now()->locale('es')->isoFormat('MMMM YYYY') }}
                    </span>
                    <h1>
                        Hola, <span class="accent">{{ Auth::user()->name }}</span>
                    </h1>
                    <p class="lead">Bienvenido a tu panel de control de FlowSchedule</p>
                </div>
            </div>
        </section>

        <!-- Grid principal -->
        <div class="dashboard-grid">

            <!-- Información del usuario -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Información Personal</h2>
                    <span class="card-badge">Activo</span>
                </div>
                <div class="sticky" aria-hidden="true">hola <strong style="font-weight: 700;">{{ Auth::user()->name }}</strong>, mucho gusto</div>

                <div class="user-info-section">
                    <div class="user-info-row">
                        <span class="user-info-label">Nombre completo</span>
                        <span class="user-info-value">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="user-info-divider"></div>
                    <div class="user-info-row">
                        <span class="user-info-label">Correo electrónico</span>
                        <span class="user-info-value">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="user-info-divider"></div>
                    <div class="user-info-row">
                        <span class="user-info-label">Miembro desde</span>
                        <span class="user-info-value">{{ Auth::user()->created_at->locale('es')->isoFormat('DD MMM YYYY') }}</span>
                    </div>
                    <div class="user-info-divider"></div>
                    <div class="user-info-row">
                        <span class="user-info-label">Última conexión</span>
                        <span class="user-info-value">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('DD MMM YYYY, HH:mm') }}</span>
                    </div>
                </div>
            </div>

            <!-- Próximos turnos -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Calendario</h2>
                    <a href="{{ route('calendar') }}" class="btn-ghost card-action-btn">
                        + Crear
                    </a>
                </div>
                <p class="card-subtitle">Tus próximos eventos</p>

                @if(($upcomingShifts ?? collect())->count() > 0 || ($calendarEvents ?? collect())->count() > 0)
                <ul class="shifts-list dashboard-scroll-list">
                    @foreach($upcomingShifts as $shift)
                    <li class="shift-item">
                        <div class="shift-time-badge">
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}—{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                        </div>
                        <div class="shift-details">
                            <h4>{{ $shift->title }}</h4>
                            <p>{{ \Carbon\Carbon::parse($shift->start_time)->locale('es')->isoFormat('dddd, D MMM') }}</p>
                        </div>
                        <span class="shift-status {{ $shift->status }}">
                            {{ ucfirst($shift->status) }}
                        </span>
                    </li>
                    @endforeach
                    @foreach(($calendarEvents ?? collect()) as $event)
                    <li class="shift-item calendar-event-item">
                        <a href="{{ route('calendar') }}" class="calendar-event-link">
                            <div class="shift-time-badge">
                                {{ $event->date->format('d/m') }}
                            </div>
                            <div class="shift-details">
                                <h4>{{ $event->title ?: 'Día marcado' }}</h4>
                                <p>{{ $event->date->locale('es')->isoFormat('dddd, D MMM') }}</p>
                            </div>
                            <span class="shift-status pending">Calendario</span>
                        </a>
                        <form method="POST" action="{{ route('calendar.events.destroy', $event->date->format('Y-m-d')) }}" onsubmit="return confirm('¿Eliminar este calendario?');" class="calendar-event-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-calendar">Eliminar</button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                <div class="card-footer-note">
                    {{ $calendarEventCount ?? 0 }} {{ ($calendarEventCount ?? 0) === 1 ? 'calendario guardado' : 'calendarios guardados' }}
                </div>
                @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <h3>No hay turnos asignados</h3>
                    <p>Crea tu primer turno para comenzar</p>
                    <a href="{{ route('calendar') }}" class="btn-ghost card-action-btn">
                        + Crear Calendario
                    </a>
                </div>
                @endif
            </div>

            <!-- Lista de tareas / To-Do -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Tareas Pendientes</h2>
                    <button id="newTaskBtn" class="btn-ghost card-action-btn">
                        + Nueva
                    </button>
                </div>

                @if(isset($todos) && $todos->count() > 0)
                <ul class="todo-list">
                    @foreach($todos as $todo)
                    <li class="todo-item" data-id="{{ $todo->id }}" data-title="{{ $todo->title }}" data-description="{{ $todo->description }}" data-due="{{ $todo->due_date ? $todo->due_date->format('Y-m-d') : '' }}" data-priority="{{ $todo->priority }}">
                        <div class="todo-checkbox" data-todo-id="{{ $todo->id }}" role="checkbox" aria-label="Marcar tarea como completada" aria-checked="false" tabindex="0"></div>
                        <div class="todo-body">
                            <span class="todo-text priority-{{ ['1' => 'low', '2' => 'medium', '3' => 'high'][$todo->priority] ?? 'low' }}">{{ $todo->title }}</span>
                            <span class="todo-date">{{ $todo->due_date ? $todo->due_date->format('d M') : 'Sin fecha' }}</span>
                        </div>
                        <div class="todo-actions">
                            <button type="button" class="btn-edit-todo" title="Modificar">Modificar</button>
                            <button type="button" class="btn-delete-todo" title="Borrar">Borrar</button>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    <h3>Sin tareas pendientes</h3>
                    <p>¡Todo está al día! Buen trabajo.</p>
                </div>
                @endif
                <div class="card-footer-note">
                    {{ $pendingTodoCount ?? 0 }} {{ ($pendingTodoCount ?? 0) === 1 ? 'tarea pendiente' : 'tareas pendientes' }}
                </div>
            </div>

            <!-- Pizarra Táctil -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Pizarra Táctil</h2>
                    <span class="card-badge">Dibuja y guarda tus ideas</span>
                </div>
                <div class="whiteboard-container">
                    <div class="whiteboard-controls">
                        <button id="pencilBtn" class="whiteboard-tool active" title="Lápiz">
                            <svg width="20" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                            </svg>
                        </button>
                        <button id="eraserBtn" class="whiteboard-tool" title="Goma">
                            <svg width="20" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m7 21-3.3-3.3a1 1 0 0 1 0-1.4L14.6 5.4a2 2 0 0 1 2.8 0l1.2 1.2a2 2 0 0 1 0 2.8L8.4 19.6a2 2 0 0 1-1.4.6Z"></path>
                                <path d="m22 21-7.6-7.6"></path>
                            </svg>
                        </button>
                        <button id="clearBtn" class="whiteboard-tool" title="Borrar todo">
                            <svg width="20" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14H5V6"></path>
                                <path d="M10 11v5"></path>
                                <path d="M14 11v5"></path>
                                <path d="M9 6V3h6v3"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="whiteboard-canvas-container">
                        <canvas id="whiteboardCanvas" width="600" height="320"></canvas>
                    </div>
                    <div class="whiteboard-status" id="whiteboardStatus"></div>
                </div>
            </div>

        </div>

        <!-- Acciones rápidas -->
        <div class="dashboard-card dashboard-card-full">
            <div class="card-header">
                <h2 class="card-title">Acciones Rápidas</h2>
            </div>

            <div class="quick-actions">
                <a href="{{ route('boards.index') }}" class="quick-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                    Mis Pizarras
                </a>
                <a href="{{ route('profile.edit') }}" class="quick-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Editar Perfil
                </a>
                <a href="{{ route('help') }}" class="quick-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Ayuda / Soporte
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#"
                    onclick="event.preventDefault(); if(confirm('¿Estás seguro de que deseas cerrar sesión?')) { document.getElementById('logout-form').submit(); }"
                    class="quick-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        width="16" height="16" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Cerrar sesión
                </a>
            </div>
        </div>
    </main>

    @include('layouts.footer')

    <!-- Modal para crear nueva tarea -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <button id="modalCloseBtn" class="modal-close-btn">×</button>

            <h2 id="modalTitle" class="modal-title">Nueva Tarea</h2>

            <form id="taskForm" class="modal-form">
                <div class="form-group">
                    <label for="taskTitle" class="form-label">Nombre de la tarea *</label>
                    <input type="text" id="taskTitle" name="title" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="taskDate" class="form-label">Fecha de vencimiento</label>
                    <input type="date" id="taskDate" name="due_date" class="form-input">
                </div>

                <div class="form-group">
                    <label for="taskDescription" class="form-label">Descripción (máx. 200 caracteres)</label>
                    <textarea id="taskDescription" name="description" rows="4" maxlength="200" class="form-input form-textarea"></textarea>
                    <div id="charCount" class="char-count">0/200</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dificultad *</label>
                    <div class="priority-options">
                        <label class="priority-option">
                            <input type="radio" name="priority" value="1" checked>
                            <span class="priority-text priority-text-low">Baja</span>
                        </label>
                        <label class="priority-option">
                            <input type="radio" name="priority" value="2">
                            <span class="priority-text priority-text-medium">Media</span>
                        </label>
                        <label class="priority-option">
                            <input type="radio" name="priority" value="3">
                            <span class="priority-text priority-text-high">Alta</span>
                        </label>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" id="modalCancelBtn" class="btn-modal-cancel">Cancelar</button>
                    <button type="submit" id="modalSubmitBtn" class="btn-modal-submit">Crear Tarea</button>
                </div>

                <div id="modalMessage" class="modal-message"></div>
            </form>
        </div>
    </div>

    <!-- Modal para editar tarea existente -->
    <div id="editTaskModal" class="modal">
        <div class="modal-content">
            <button id="editModalCloseBtn" class="modal-close-btn">×</button>

            <h2 id="editModalTitle" class="modal-title">Editar Tarea</h2>

            <form id="editTaskForm" class="modal-form">
                <input type="hidden" id="editTaskId" name="id">

                <div class="form-group">
                    <label for="editTaskTitle" class="form-label">Nombre de la tarea *</label>
                    <input type="text" id="editTaskTitle" name="title" required class="form-input">
                </div>

                <div class="form-group">
                    <label for="editTaskDate" class="form-label">Fecha de vencimiento</label>
                    <input type="date" id="editTaskDate" name="due_date" class="form-input">
                </div>

                <div class="form-group">
                    <label for="editTaskDescription" class="form-label">Descripción (máx. 200 caracteres)</label>
                    <textarea id="editTaskDescription" name="description" rows="4" maxlength="200" class="form-input form-textarea"></textarea>
                    <div id="editCharCount" class="char-count">0/200</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dificultad *</label>
                    <div class="priority-options">
                        <label class="priority-option">
                            <input type="radio" name="editPriority" value="1" checked>
                            <span class="priority-text priority-text-low">Baja</span>
                        </label>
                        <label class="priority-option">
                            <input type="radio" name="editPriority" value="2">
                            <span class="priority-text priority-text-medium">Media</span>
                        </label>
                        <label class="priority-option">
                            <input type="radio" name="editPriority" value="3">
                            <span class="priority-text priority-text-high">Alta</span>
                        </label>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" id="editModalCancelBtn" class="btn-modal-cancel">Cancelar</button>
                    <button type="submit" id="editModalSubmitBtn" class="btn-modal-submit">Guardar Cambios</button>
                </div>

                <div id="editModalMessage" class="modal-message"></div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            // Elementos del modal de crear tarea
            const newTaskBtn = document.getElementById('newTaskBtn');
            const createModal = document.getElementById('taskModal');
            const createModalCloseBtn = document.getElementById('modalCloseBtn');
            const createModalCancelBtn = document.getElementById('modalCancelBtn');
            const createModalSubmitBtn = document.getElementById('modalSubmitBtn');
            const createTaskForm = document.getElementById('taskForm');
            const createModalMessage = document.getElementById('modalMessage');
            const createModalTitle = document.getElementById('modalTitle');
            const createCharCount = document.getElementById('charCount');
            const createTaskDescription = document.getElementById('taskDescription');

            // Elementos del modal de editar tarea
            const editModal = document.getElementById('editTaskModal');
            const editModalCloseBtn = document.getElementById('editModalCloseBtn');
            const editModalCancelBtn = document.getElementById('editModalCancelBtn');
            const editModalSubmitBtn = document.getElementById('editModalSubmitBtn');
            const editTaskForm = document.getElementById('editTaskForm');
            const editModalMessage = document.getElementById('editModalMessage');
            const editModalTitle = document.getElementById('editModalTitle');
            const editCharCount = document.getElementById('editCharCount');
            const editTaskTitle = document.getElementById('editTaskTitle');
            const editTaskDescription = document.getElementById('editTaskDescription');
            const editTaskIdInput = document.getElementById('editTaskId');

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            // ABRIR MODAL DE CREAR TAREA
            newTaskBtn.addEventListener('click', function(e) {
                e.preventDefault();
                createModalTitle.textContent = 'Nueva Tarea';
                createModalSubmitBtn.textContent = 'Crear Tarea';
                createModal.classList.add('is-open');
                createTaskForm.reset();
                createTaskDescription.focus();
                updateCreateCharCount();
                createModalMessage.textContent = '';
            });

            // CERRAR MODAL DE CREAR TAREA
            function closeCreateModal() {
                createModal.classList.remove('is-open');
                createModalMessage.textContent = '';
            }

            createModalCloseBtn.addEventListener('click', closeCreateModal);
            createModalCancelBtn.addEventListener('click', closeCreateModal);

            createModal.addEventListener('click', function(e) {
                if (e.target === createModal) {
                    closeCreateModal();
                }
            });

            // CERRAR MODAL DE EDITAR TAREA
            function closeEditModal() {
                editModal.classList.remove('is-open');
                editModalMessage.textContent = '';
            }

            editModalCloseBtn.addEventListener('click', closeEditModal);
            editModalCancelBtn.addEventListener('click', closeEditModal);

            editModal.addEventListener('click', function(e) {
                if (e.target === editModal) {
                    closeEditModal();
                }
            });

            // ACTUALIZAR CONTADOR DE CARACTERES - CREAR TAREA
            function updateCreateCharCount() {
                const remaining = 200 - createTaskDescription.value.length;
                createCharCount.textContent = `${createTaskDescription.value.length}/200`;
                if (remaining < 0) {
                    createCharCount.style.color = '#e53e3e';
                } else if (remaining < 20) {
                    createCharCount.style.color = '#dd6b20';
                } else {
                    createCharCount.style.color = 'var(--pencil)';
                }
            }

            createTaskDescription.addEventListener('input', updateCreateCharCount);

            // ACTUALIZAR CONTADOR DE CARACTERES - EDITAR TAREA
            function updateEditCharCount() {
                const remaining = 200 - editTaskDescription.value.length;
                editCharCount.textContent = `${editTaskDescription.value.length}/200`;
                if (remaining < 0) {
                    editCharCount.style.color = '#e53e3e';
                } else if (remaining < 20) {
                    editCharCount.style.color = '#dd6b20';
                } else {
                    editCharCount.style.color = 'var(--pencil)';
                }
            }

            editTaskDescription.addEventListener('input', updateEditCharCount);

            // MANEJAR ENVÍO DEL FORMULARIO DE CREAR TAREA
            createTaskForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const title = document.getElementById('taskTitle').value.trim();
                if (!title) {
                    createModalMessage.textContent = 'El nombre de la tarea es obligatorio';
                    createModalMessage.style.color = '#e53e3e';
                    return;
                }

                const dueDate = document.getElementById('taskDate').value || null;
                const description = document.getElementById('taskDescription').value.trim() || null;
                const priority = parseInt(document.querySelector('input[name="priority"]:checked').value) || 1;

                createModalSubmitBtn.disabled = true;
                createModalSubmitBtn.textContent = 'Creando...';
                createModalMessage.textContent = '';

                try {
                    const response = await fetch('/todos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: title,
                            due_date: dueDate,
                            description: description,
                            priority: priority
                        })
                    });

                    if (response.ok) {
                        window.location.reload();
                    } else {
                        const errorData = await response.json();
                        console.error('Error al crear tarea:', errorData);
                        createModalMessage.textContent = errorData.message || 'Error al crear la tarea. Por favor, intenta nuevamente.';
                        createModalMessage.style.color = '#e53e3e';
                    }
                } catch (error) {
                    console.error('Error de red:', error);
                    createModalMessage.textContent = 'Error de conexión. Por favor, verifica tu internet e intenta nuevamente.';
                    createModalMessage.style.color = '#e53e3e';
                } finally {
                    createModalSubmitBtn.disabled = false;
                    createModalSubmitBtn.textContent = 'Crear Tarea';
                }
            });

            // MANEJAR EDICIÓN, ELIMINACIÓN Y COMPLETAR TAREAS
            document.addEventListener('click', function(e) {
                const todoItem = e.target.closest('.todo-item');
                if (!todoItem) return;

                const todoId = todoItem.dataset.id;
                if (!todoId) return;

                const checkbox = e.target.closest('.todo-checkbox');
                if (checkbox && todoItem.contains(checkbox)) {
                    e.preventDefault();
                    toggleTodoCompletion(todoId, todoItem);
                    return;
                }

                const editButton = e.target.closest('.btn-edit-todo');
                if (editButton && todoItem.contains(editButton)) {
                    e.preventDefault();
                    openEditModal(todoId, todoItem);
                    return;
                }

                const deleteButton = e.target.closest('.btn-delete-todo');
                if (deleteButton && todoItem.contains(deleteButton)) {
                    e.preventDefault();
                    deleteTodo(todoId, todoItem);
                    return;
                }
            });

            // ABRIR MODAL DE EDITAR TAREA CON DATOS DE LA TAREA
            async function openEditModal(todoId, todoItem) {
                try {
                    const title = todoItem.dataset.title || '';
                    const description = todoItem.dataset.description || '';
                    const dueDate = todoItem.dataset.due || '';
                    const priority = parseInt(todoItem.dataset.priority) || 1;

                    editTaskIdInput.value = todoId;
                    document.getElementById('editTaskTitle').value = title;
                    document.getElementById('editTaskDescription').value = description;
                    document.getElementById('editTaskDate').value = dueDate;

                    const priorityRadio = document.querySelector(`input[name="editPriority"][value="${priority}"]`);
                    if (priorityRadio) {
                        priorityRadio.checked = true;
                    }

                    editModalTitle.textContent = 'Editar Tarea';
                    editModalSubmitBtn.textContent = 'Guardar Cambios';
                    editModal.classList.add('is-open');
                    editTaskTitle.focus();
                    updateEditCharCount();
                    editModalMessage.textContent = '';
                } catch (error) {
                    console.error('Error al abrir modal de edición:', error);
                    alert('Error al cargar la tarea para edición. Por favor, intenta nuevamente.');
                }
            }

            // MANEJAR ENVÍO DEL FORMULARIO DE EDITAR TAREA
            editTaskForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const title = document.getElementById('editTaskTitle').value.trim();
                if (!title) {
                    editModalMessage.textContent = 'El nombre de la tarea es obligatorio';
                    editModalMessage.style.color = '#e53e3e';
                    return;
                }

                const todoId = editTaskIdInput.value;
                const dueDate = document.getElementById('editTaskDate').value || null;
                const description = document.getElementById('editTaskDescription').value.trim() || null;
                const priority = parseInt(document.querySelector('input[name="editPriority"]:checked').value) || 1;

                editModalSubmitBtn.disabled = true;
                editModalSubmitBtn.textContent = 'Guardando...';
                editModalMessage.textContent = '';

                try {
                    const response = await fetch(`/todos/${todoId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: title,
                            due_date: dueDate,
                            description: description,
                            priority: priority
                        })
                    });

                    if (response.ok) {
                        window.location.reload();
                    } else {
                        const errorData = await response.json();
                        console.error('Error al actualizar tarea:', errorData);
                        editModalMessage.textContent = errorData.message || 'Error al actualizar la tarea. Por favor, intenta nuevamente.';
                        editModalMessage.style.color = '#e53e3e';
                    }
                } catch (error) {
                    console.error('Error de red:', error);
                    editModalMessage.textContent = 'Error de conexión. Por favor, verifica tu internet e intenta nuevamente.';
                    editModalMessage.style.color = '#e53e3e';
                } finally {
                    editModalSubmitBtn.disabled = false;
                    editModalSubmitBtn.textContent = 'Guardar Cambios';
                }
            });

            // ALTERNAR ESTADO DE COMPLETADO DE TAREA
            async function toggleTodoCompletion(todoId, todoItem) {
                try {
                    const response = await fetch(`/todos/${todoId}/toggle`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const todoText = todoItem.querySelector('.todo-text');
                        const checkbox = todoItem.querySelector('.todo-checkbox');
                        const isCompleted = todoText.classList.toggle('completed');
                        checkbox.classList.toggle('checked', isCompleted);
                        checkbox.setAttribute('aria-checked', String(isCompleted));
                    } else {
                        const errorData = await response.json();
                        console.error('Error al alternar estado de tarea:', errorData);
                        alert('Error al actualizar la tarea. Por favor, intenta nuevamente.');
                    }
                } catch (error) {
                    console.error('Error de red:', error);
                    alert('Error de conexión. Por favor, verifica tu internet e intenta nuevamente.');
                }
            }

            // ELIMINAR TAREA
            async function deleteTodo(todoId, todoItem) {
                if (!confirm('¿Estás seguro de que quieres eliminar esta tarea?')) {
                    return;
                }

                try {
                    const response = await fetch(`/todos/${todoId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        todoItem.remove();
                    } else {
                        const errorData = await response.json();
                        console.error('Error al eliminar tarea:', errorData);
                        alert('Error al eliminar la tarea. Por favor, intenta nuevamente.');
                    }
                } catch (error) {
                    console.error('Error de red:', error);
                    alert('Error de conexión. Por favor, verifica tu internet e intenta nuevamente.');
                }
            }
        })();
    </script>

    <script>
        (function() {
            'use strict';

            const canvas = document.getElementById('whiteboardCanvas');
            const ctx = canvas.getContext('2d');
            const pencilBtn = document.getElementById('pencilBtn');
            const eraserBtn = document.getElementById('eraserBtn');
            const clearBtn = document.getElementById('clearBtn');
            const statusEl = document.getElementById('whiteboardStatus');
            const whiteboardStorageKey = @json('flowschedule_whiteboard_user_' . Auth::id());

            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;
            let currentTool = 'pencil';

            function initCanvas() {
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.strokeStyle = '#1A1A1A';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
            }

            function setTool(tool) {
                currentTool = tool;
                pencilBtn.classList.toggle('active', tool === 'pencil');
                eraserBtn.classList.toggle('active', tool === 'eraser');

                if (tool === 'pencil') {
                    ctx.strokeStyle = '#1A1A1A';
                    ctx.lineWidth = 2;
                } else {
                    ctx.strokeStyle = 'white';
                    ctx.lineWidth = 10;
                }

                statusEl.textContent = tool === 'pencil' ? 'Lápiz seleccionado' : 'Goma seleccionada';
            }

            function getPosition(e) {
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;

                return {
                    x: (e.clientX - rect.left) * scaleX,
                    y: (e.clientY - rect.top) * scaleY
                };
            }

            function startDrawing(e) {
                isDrawing = true;
                const pos = getPosition(e);
                lastX = pos.x;
                lastY = pos.y;

                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
            }

            function drawLine(e) {
                if (!isDrawing) return;

                const pos = getPosition(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();

                lastX = pos.x;
                lastY = pos.y;
            }

            function stopDrawing() {
                if (!isDrawing) return;
                isDrawing = false;
                saveDrawing();
            }

            function saveDrawing() {
                try {
                    const dataUrl = canvas.toDataURL('image/png');
                    localStorage.setItem(whiteboardStorageKey, dataUrl);
                    statusEl.textContent = 'Dibujo guardado';
                    setTimeout(() => {
                        statusEl.textContent = 'Listo para dibujar';
                    }, 1500);
                } catch (error) {
                    console.error('Error saving drawing:', error);
                    statusEl.textContent = 'Error al guardar';
                }
            }

            function loadDrawing() {
                try {
                    const savedData = localStorage.getItem(whiteboardStorageKey);
                    if (savedData) {
                        const img = new Image();
                        img.onload = function() {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                            statusEl.textContent = 'Dibujo cargado';
                            setTimeout(() => {
                                statusEl.textContent = 'Listo para dibujar';
                            }, 1500);
                        };
                        img.src = savedData;
                    }
                } catch (error) {
                    console.error('Error loading drawing:', error);
                }
            }

            function clearCanvas() {
                if (confirm('¿Estás seguro de que quieres borrar todo el dibujo?')) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = 'white';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    localStorage.removeItem(whiteboardStorageKey);
                    statusEl.textContent = 'Canvas limpiado';
                    setTimeout(() => {
                        statusEl.textContent = 'Listo para dibujar';
                    }, 1500);
                }
            }

            pencilBtn.addEventListener('click', () => setTool('pencil'));
            eraserBtn.addEventListener('click', () => setTool('eraser'));
            clearBtn.addEventListener('click', clearCanvas);

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', drawLine);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseleave', stopDrawing);

            canvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                const touch = e.touches[0];
                const mouseEvent = new MouseEvent('mousedown', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                canvas.dispatchEvent(mouseEvent);
            }, { passive: false });

            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                const touch = e.touches[0];
                const mouseEvent = new MouseEvent('mousemove', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                canvas.dispatchEvent(mouseEvent);
            }, { passive: false });

            canvas.addEventListener('touchend', stopDrawing);

            initCanvas();
            setTool('pencil');
            loadDrawing();
        })();
    </script>

</x-public-layout>