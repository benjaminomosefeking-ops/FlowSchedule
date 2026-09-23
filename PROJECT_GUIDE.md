# 📘 Guía Técnica del Proyecto: FlowScheduler

Esta guía ha sido diseñada como un documento de referencia técnica y una herramienta de estudio. Su objetivo es permitir que cualquier desarrollador (o tú mismo en una entrevista técnica) pueda explicar la arquitectura, las decisiones de diseño y el flujo de datos de la aplicación con total claridad y profesionalismo.

---

## 🌟 1. Visión General (High-Level Overview)

**FlowScheduler** es una plataforma de gestión de horarios y colaboración diseñada para equipos de trabajo. A diferencia de las herramientas de calendario tradicionales, FlowScheduler integra la gestión estructurada (tareas y eventos) con la gestión visual (pizarras infinitas).

**Propuesta de Valor:**
Resuelve el problema de la coordinación de equipos al permitir que la planificación pase de ser una lista rígida de turnos a un espacio creativo donde los jefes y empleados pueden visualizar la organización del trabajo en tiempo real.

---

## 🛠️ 2. Stack Tecnológico y Justificación

| Componente | Tecnología | Justificación Técnica |
| :--- | :--- | :--- |
| **Backend** | Laravel 11 | Elegido por su robustez en el manejo de autenticación, el sistema de rutas intuitivo y el ORM Eloquent, que simplifica la gestión de relaciones complejas en la base de datos. |
| **Frontend** | React.js | Permite crear una interfaz reactiva y modular, esencial para manejar el estado complejo de las pizarras y la interactividad del tablero. |
| **Visual Canvas** | Excalidraw | Integrado para proporcionar una experiencia de dibujo profesional, permitiendo que la planificación sea visual y no solo textual. |
| **Base de Datos** | SQL (via Eloquent) | Necesaria para mantener la integridad referencial entre usuarios, equipos y turnos (Relaciones Many-to-Many). |

---

## 🏗️ 3. Arquitectura y Estructura de Carpetas

La aplicación sigue el patrón **MVC (Modelo-Vista-Controlador)**, separando la lógica de datos, la interfaz de usuario y el control del flujo.

### Mapa de Directorios Clave:
- `app/Models/`: **Capa de Datos**. Define la estructura de la información y las relaciones (ej. un Usuario pertenece a muchos Equipos).
- `app/Http/Controllers/`: **Capa de Lógica**. Orquestan la comunicación entre los modelos y las vistas. Aquí reside la "inteligencia" de la aplicación.
- `app/Policies/`: **Capa de Seguridad**. Controlan quién puede hacer qué (ej. `BoardPolicy` decide si un empleado puede editar una pizarra).
- `routes/web.php`: **Capa de Acceso**. Define todos los puntos de entrada (URLs) de la aplicación.
- `resources/js/components/`: **Capa de Interfaz**. Contiene los componentes de React, incluyendo el motor de la pizarra.

---

## 🔬 4. Inmersión en Módulos Core

### 🔐 Autenticación y RBAC (Role-Based Access Control)
El sistema utiliza un modelo de roles simple pero efectivo: **Boss (Jefe)** y **Employee (Empleado)**.
- **Boss**: Tiene permisos totales sobre sus equipos, puede crear pizarras y generar códigos de invitación.
- **Employee**: Tiene un perfil más restringido; puede unirse a equipos mediante códigos y gestionar sus propias tareas.
- **Implementación**: Se utilizan **Policies** de Laravel para validar permisos en tiempo real antes de ejecutar cualquier acción en el controlador.

### 🎨 El Sistema de Pizarras (Boards)
Es la funcionalidad más compleja del proyecto.
- **Sincronización**: La pizarra no guarda cada trazo individualmente en la DB, sino que serializa todo el estado del lienzo en un formato **JSON**.
- **Flujo de Guardado**: Para optimizar el rendimiento, se utiliza un mecanismo de *debounce* en el frontend; el estado se envía al servidor solo después de que el usuario deja de dibujar por unos milisegundos.

### 👥 Gestión de Equipos
Implementa una relación de **Muchos a Muchos (Many-to-Many)**.
- **Lógica de Invitación**: El Jefe genera un código único (`invitation_code`). Cuando un empleado lo introduce, el sistema crea un vínculo en la tabla pivote `team_user`.
- **Estructura**: `User` $\leftrightarrow$ `team_user` $\leftrightarrow$ `Team`.

### ✅ Gestión de Tareas (Todos)
Un sistema de productividad personal integrado.
- **Ciclo de Vida**: Creación $\rightarrow$ Asignación de Prioridad $\rightarrow$ Marcado como Completado $\rightarrow$ Eliminación.
- **Validaciones**: Se asegura que un usuario solo pueda modificar sus propias tareas mediante validaciones de ID en el controlador.

---

## 🚀 5. El "Viaje del Dato" (Data Journey)

*Ejemplo: Un empleado se une a un equipo mediante un código.*

1.  **Acción (Frontend)**: El usuario escribe el código en un input y hace clic en "Unirse". React dispara una petición `POST` a `/team/join`.
2.  **Enrutamiento (Route)**: `web.php` recibe la petición y la dirige al método `join` de `TeamController`.
3.  **Procesamiento (Controller)**: 
    - El controlador valida que el código exista en la tabla `teams`.
    - Verifica que el usuario tenga el rol de `employee`.
    - Comprueba que el usuario no esté ya en ese equipo.
4.  **Persistencia (Model/DB)**: El modelo `Team` utiliza el método `attach()` para insertar una nueva fila en la tabla pivote `team_user` vinculando el `user_id` con el `team_id`.
5.  **Respuesta (Response)**: El controlador devuelve una redirección al dashboard con un mensaje de éxito en la sesión.

---

## 🏆 6. Retos Técnicos y Soluciones (Interview Wins)

Cuando te pregunten *"¿Cuál fue el mayor desafío técnico?"*, puedes mencionar estos puntos:

**1. Persistencia de un Lienzo Infinito**
- **Problema**: Guardar miles de trazos individuales en la DB sería ineficiente y lento.
- **Solución**: Implementé la serialización del estado completo de Excalidraw en un campo JSON. Esto permite cargar y guardar la pizarra entera en una sola operación de base de datos, manteniendo la fluidez del dibujo.

**2. Seguridad de Acceso en Entornos Colaborativos**
- **Problema**: Evitar que un usuario acceda a pizarras o datos de equipos a los que no pertenece.
- **Solución**: Implementé **Laravel Policies**. En lugar de poner validaciones manuales en cada método, centralicé la lógica de permisos en clases Policy, haciendo que el código sea más limpio, mantenible y seguro.

**3. Experiencia de Usuario en el Onboarding**
- **Problema**: Reducir la fricción al crear un equipo para nuevos jefes.
- **Solución**: Creé un flujo de onboarding automatizado que detecta si un usuario con rol de `boss` no tiene equipo y le genera uno automáticamente con un código de invitación listo para usar.
