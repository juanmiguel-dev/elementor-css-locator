# CSS Lost & Found for Elementor

Un plugin de WordPress simple pero potente para encontrar, editar y gestionar todo el CSS personalizado que has añadido a través de Elementor.

[![Versión](https://img.shields.io/badge/Versión-0.3-orange)](https://github.com/juanmiguel-dev/elementor-css-locator)
[![WordPress Testeado](https://img.shields.io/badge/WordPress-6.4+-blue)](https://wordpress.org/download/)
[![Licencia](https://img.shields.io/badge/Licencia-GPLv3-blue)](https://github.com/juanmiguel-dev/elementor-css-locator/blob/main/LICENSE)

---

## Vista Previa del Plugin

![Captura del Localizador de CSS para Elementor](https://github.com/juanmiguel-dev/elementor-css-locator/raw/main/vista-css-locator.jpg)

---

## 🤔 ¿Por qué este plugin?

Elementor es fantástico, pero su flexibilidad puede llevar a un dolor de cabeza común: **"¿Dónde diablos puse ese código CSS?"**

Los estilos personalizados pueden estar escondidos en la pestaña "Avanzado" de un widget, en la configuración de una columna, en una sección, o incluso en los ajustes de la página. Rastrear un estilo específico para modificarlo o eliminarlo puede convertirse en una tarea tediosa y frustrante.

Este plugin fue creado para resolver ese problema. Escanea todo tu sitio y te presenta un mapa interactivo de cada fragmento de CSS personalizado, permitiéndote tomar el control de tus estilos de nuevo.

## ✨ Características

*   **🗺️ Dashboard Centralizado:** Visualiza todo el CSS personalizado de cada página, post o plantilla de Elementor en una única interfaz.
*   **✏️ Edición en Vivo:** ¿Necesitas hacer un cambio rápido? Modifica el CSS directamente desde el dashboard del plugin. Los cambios se guardan en el elemento correcto de Elementor mediante AJAX, ¡y la caché de Elementor se limpia automáticamente!
*   **🔍 Búsqueda Rápida:** Filtra instantáneamente por página, tipo de elemento o incluso por el propio código CSS para encontrar lo que necesitas en segundos.
*   **📤 Exportación Completa:** Con un solo clic, exporta todo el CSS encontrado a un único archivo `.css`. Ideal para realizar copias de seguridad, migrar estilos o refactorizar tu código en una hoja de estilos externa.
*   **🧩 Autocontenido:** Todo el plugin (PHP, CSS y JavaScript) está en un único archivo, haciendo que sea increíblemente fácil de entender y modificar.

## 🚀 Instalación

Para instalar este plugin desde GitHub, necesitas empaquetarlo en un archivo `.zip` que WordPress pueda entender.

1.  Descarga el archivo `css-locator.php` desde este repositorio.
2.  En tu computadora, crea una **nueva carpeta** y llámala `elementor-css-locator`.
3.  Mueve el archivo `css-locator.php` **dentro** de esa carpeta.
4.  Comprime la carpeta `elementor-css-locator` completa para crear un archivo `elementor-css-locator.zip`.
5.  En tu panel de WordPress, ve a **Plugins > Añadir nuevo**.
6.  Haz clic en el botón **Subir plugin** y selecciona el archivo `.zip` que acabas de crear.
7.  Una vez instalado, haz clic en **Activar plugin**.

## ⚙️ ¿Cómo se usa?

1.  Después de la activación, encontrarás un nuevo menú en tu panel de WordPress llamado **CSS Locator**.
2.  Al hacer clic, el plugin escaneará tu sitio y mostrará una lista desplegable de todas las páginas que contienen CSS personalizado de Elementor.
3.  Haz clic en el título de una página para expandir y ver los widgets/elementos específicos.
4.  Haz clic en un elemento para ver y editar su CSS en el área de texto.
5.  Si realizas cambios, aparecerá un botón de **Guardar**. Al hacer clic, tus cambios se aplicarán al instante.
6.  Utiliza la barra de **Búsqueda** para filtrar o el botón de **Exportar** para descargar todo el código.

## ✍️ Nota sobre la estructura del código

Este plugin fue intencionalmente desarrollado en un solo archivo para facilitar su distribución y para demostrar el concepto central de manera clara y concisa. Contiene el PHP, CSS y JavaScript en un único lugar.

Para un plugin más grande o destinado al repositorio oficial de WordPress, la mejor práctica sería separar estos elementos en sus propios archivos y encolarlos (`enqueue`) usando las funciones estándar de WordPress.

## 🤝 Contribuciones

Este es un proyecto de código abierto y las contribuciones son bienvenidas. Si tienes una idea para mejorar el plugin o has encontrado un error, no dudes en:

*   Abrir un [**Issue**](https://github.com/juanmiguel-dev/elementor-css-locator/issues) para reportar un problema o sugerir una nueva característica.
*   Hacer un **Fork** del repositorio, crear una nueva rama y enviar un **Pull Request** con tus mejoras.

## 📄 Licencia

Este plugin está licenciado bajo la **GNU General Public License v3.0**. Puedes leer la licencia completa en el archivo [LICENSE](https://github.com/juanmiguel-dev/elementor-css-locator/blob/main/LICENSE) de este repositorio.
