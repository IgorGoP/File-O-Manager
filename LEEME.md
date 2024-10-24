# File OManager

## Descripción del Proyecto

File OManager es una solución abierta y flexible para la gestión de archivos que busca llenar las limitaciones de muchas soluciones existentes en términos de accesibilidad, funcionalidad y costos. Este proyecto tiene como objetivo facilitar la administración de archivos de manera eficiente y segura, ofreciendo también la posibilidad de personalización y expansión por parte de los usuarios.

File OManager ofrece una alternativa atractiva tanto para aquellos que buscan una opción gratuita como para quienes desean funcionalidades avanzadas y soporte profesional mediante un esquema comercial. Este enfoque híbrido permite que el software esté al alcance de todos, independientemente de sus recursos o necesidades.

## Características Principales

- **Carga y Descarga de Archivos**: Posibilidad de cargar y descargar archivos de forma rápida y segura.
- **Gestión de Directorios**: Organiza y administra carpetas y archivos desde una interfaz intuitiva.
- **Control de Permisos de Usuario**: Gestión granular de permisos según el rol del usuario (administrador, usuario común, invitado).
- **Interfaz Web Moderna**: Interfaz web desarrollada en HTML, CSS y JavaScript para facilitar la interacción.

## Tecnología Utilizada

- **Backend**: PHP para manejar la lógica del servidor.
- **Base de Datos**: MariaDB, para almacenar información sobre usuarios, permisos y archivos.
- **Frontend**: HTML, CSS, JavaScript.
- **Servidor**: Debian con Apache.

## Instalación

Para comenzar a usar File OManager, sigue estos pasos:

1. **Clonar el Repositorio**:
   ```bash
   git clone https://github.com/IgorGoP/File-OManager.git
   ```
2. **Configurar el Entorno**:
   - Asegúrate de tener un servidor Apache con PHP y MariaDB configurados.
   - Configura los archivos del entorno en `/config/` para establecer las conexiones a la base de datos.
3. **Inicializar la Base de Datos**:
   - Ejecuta los comandos SQL proporcionados en el archivo `docs/database.sql` para crear las tablas necesarias.
4. **Configurar Dependencias**:
   - Utiliza Composer para instalar las dependencias del proyecto:
     ```bash
     composer install
     ```

## Estructura del Proyecto

La estructura del proyecto es la siguiente:

- `/backend/` - Contiene el código PHP para manejar la lógica de servidor.
- `/frontend/` - Incluye el HTML, CSS y JavaScript para la interfaz de usuario.
- `/config/` - Archivos de configuración como `.env` para definir variables de entorno.
- `/docs/` - Documentación del proyecto, incluyendo instrucciones de instalación y uso.
- `/public/` - Recursos públicos, como imágenes, scripts o archivos estáticos.

## Licencia

Este proyecto está licenciado bajo la **Licencia MIT**, con términos adicionales para el uso comercial y la aceptación de donaciones. Esto significa que cualquier persona puede utilizar, modificar y distribuir el software, siempre y cuando incluya la licencia original y respete los siguientes términos adicionales:

- **Esquemas Gratuitos y de Pago**: File OManager tiene una versión gratuita con funcionalidades básicas y una versión de pago con características avanzadas.
- **Aceptación de Donaciones**: Se aceptan donaciones voluntarias para apoyar el desarrollo del proyecto.
- **Participación en Ingresos**: Si otros desarrolladores o empresas generan ingresos derivados del uso del código, deberán compartir un porcentaje de esos ingresos con el autor original.

Para más detalles, consulta el archivo [LICENSE.md](./LICENSE.md).

## Contribuir

Las contribuciones son bienvenidas. Si deseas contribuir al desarrollo de File OManager, sigue los siguientes pasos:

1. **Fork del Repositorio**.
2. **Crea una Rama para tu Funcionalidad** (`git checkout -b feature/NuevaFuncionalidad`).
3. **Haz Commit de tus Cambios** (`git commit -m 'Añadida nueva funcionalidad'`).
4. **Haz Push de la Rama** (`git push origin feature/NuevaFuncionalidad`).
5. **Crea un Pull Request**.

## Contacto

Para preguntas o asistencia adicional, puedes contactar al creador del proyecto a través de su perfil de GitHub: [IgorGoP](https://github.com/IgorGoP).

## Enlace al Proyecto

El proyecto está publicado en GitHub y puedes acceder a él en el siguiente enlace: [File OManager en GitHub](https://github.com/IgorGoP/File-OManager).

## Próximos Pasos

- Desarrollar y probar el módulo de autenticación de usuarios.
- Completar la documentación para la API y para cada uno de los módulos.
- Implementar características avanzadas para la versión de pago.

