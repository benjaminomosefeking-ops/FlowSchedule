# 🚀 Guía de Despliegue - FlowScheduler

Este proyecto es una aplicación Laravel. Debido a que utiliza PHP y una base de datos, **no puede ser alojado en GitHub Pages** (que es solo para sitios estáticos).

## 🌐 Opciones de Hosting Gratuito/Económico

Para desplegar FlowScheduler, te recomendamos las siguientes plataformas:

1. **Railway.app** (Recomendado): Muy sencillo, detecta Laravel automáticamente.
2. **Fly.io**: Excelente rendimiento, requiere instalar su CLI.
3. **Render.com**: Ofrece planes gratuitos para bases de datos PostgreSQL y servicios web.

## 🛠️ Pasos para el Despliegue (General)

1. **Subir a GitHub**: Asegúrate de que todo el código esté en un repositorio de GitHub.
2. **Conectar el Hosting**: Vincula tu repositorio de GitHub con la plataforma elegida.
3. **Configurar Variables de Entorno (`.env`)**:
   En el panel de control del hosting, añade las siguientes variables:
   - `APP_KEY`: Ejecuta `php artisan key:generate --show` localmente y copia el valor.
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_URL`: La URL que te asigne el hosting.
   - `DB_CONNECTION`: `mysql` o `pgsql` (según el hosting).
   - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Los datos de la base de datos proporcionados por el hosting.
   - `MAIL_MAILER`: `smtp`
   - `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`: Configura un servicio como **Mailtrap** o **SendGrid** para que el restablecimiento de contraseñas funcione.

4. **Ejecutar Migraciones**:
   La mayoría de los hostings permiten ejecutar un comando de inicio. Asegúrate de que se ejecute:
   `php artisan migrate --force`

## 📧 Nota sobre el Correo Electrónico
Para que la función de **"¿Olvidaste tu contraseña?"** funcione en producción, DEBES configurar un servidor de correo real en el archivo `.env`. Sin esto, el sistema intentará enviar el correo y dará un error.
