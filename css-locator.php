<?php
/**
 * Plugin Name:       CSS lost in Elementor Locator
 * Description:       Find, edit, and export all custom CSS added in Elementor. A simple tool to map and manage your styles.
 * Version:           0.3
 * Author:            LABORATORIO DE SINERGIA HUMANO-IA
 * Author URI:        https://aistudio.google.com/
 * Text Domain:       css-locator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Main CSS Locator Class
 *
 * @class CSS_Locator
 */
final class CSS_Locator {

    const VERSION = '0.3';
    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
        add_action( 'wp_ajax_save_elementor_css', [ $this, 'save_elementor_css' ] );
    }

    public function admin_menu() {
        add_menu_page(
            __( 'CSS Locator', 'css-locator' ),
            __( 'CSS Locator', 'css-locator' ),
            'manage_options',
            'css-locator',
            [ $this, 'admin_page' ],
            'dashicons-search',
            6
        );
    }

    public function admin_page() {
        ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

        <div class="wrap" id="css-locator-app">
            <header class="cl-header">
                <h1><?php _e( 'CSS Locator', 'css-locator' ); ?></h1>
                <p><?php _e( 'A map of all your custom CSS added through Elementor.', 'css-locator' ); ?></p>
            </header>
            
            <div class="controls">
                <div class="search-wrapper">
                     <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="css-search" placeholder="<?php _e( 'Search CSS or elements...', 'css-locator' ); ?>">
                </div>
                <button id="export-css-btn" class="button button-primary"><?php _e( 'Export All CSS', 'css-locator' ); ?></button>
            </div>

            <div id="css-tree">
                 <div class="loading-spinner"></div>
            </div>
        </div>
        <style>
            :root {
                --cl-bg: #f8f9fa;
                --cl-text: #2c3e50;
                --cl-text-light: #7f8c8d;
                --cl-border: #e2e8f0;
                --cl-primary: #e53e3e;
                --cl-primary-hover: #c53030;
                --cl-white: #ffffff;
                --cl-success: #48bb78;
                --cl-font-sans: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                --cl-font-mono: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
            }
            #css-locator-app { background-color: var(--cl-bg); color: var(--cl-text); padding: 20px; font-family: var(--cl-font-sans); }
            .cl-header { margin-bottom: 24px; }
            .cl-header h1 { font-weight: 700; }
            .cl-header p { font-size: 16px; color: var(--cl-text-light); margin-top: 4px; }
            .controls { margin-bottom: 20px; display: flex; gap: 10px; align-items: center; justify-content: space-between; }
            .search-wrapper { position: relative; }
            .search-icon { position: absolute; top: 50%; left: 12px; transform: translateY(-50%); color: var(--cl-text-light); }
            #css-search { width: 350px; padding: 10px 10px 10px 40px; border-radius: 8px; border: 1px solid var(--cl-border); transition: all 0.2s ease-in-out; background-color: var(--cl-white); }
            #css-search:focus { border-color: var(--cl-primary); box-shadow: 0 0 0 2px rgba(229, 62, 62, 0.2); outline: none; }
            #export-css-btn { background-color: var(--cl-primary); border-color: var(--cl-primary); border-radius: 8px; padding: 8px 16px; font-weight: 500; transition: background-color 0.2s ease; }
            #export-css-btn:hover { background-color: var(--cl-primary-hover); border-color: var(--cl-primary-hover); }
            #css-tree ul { list-style-type: none; padding-left: 28px; border-left: 1px solid var(--cl-border); }
            #css-tree li { margin: 4px 0; position: relative; }
            #css-tree li::before { content: ''; position: absolute; top: 18px; left: -28px; border-top: 1px solid var(--cl-border); width: 20px; height: 0; }
            .page-title, .element-title { display: flex; align-items: center; gap: 8px; font-weight: 500; cursor: pointer; padding: 8px; border-radius: 6px; transition: background-color 0.2s ease-in-out; }
            .page-title:hover, .element-title:hover { background-color: #e9ecef; }
            .page-title { font-size: 18px; }
            .css-code-wrapper { margin-left: 36px; margin-top: 8px; padding-left: 16px; border-left: 1px dashed var(--cl-border); }
            .css-code { background-color: var(--cl-white); color: var(--cl-text); padding: 16px; border-radius: 8px; margin-top: 10px; white-space: pre-wrap; font-family: var(--cl-font-mono); border: 1px solid var(--cl-border); outline: none; transition: all 0.2s ease-in-out; width: calc(100% - 36px); min-height: 100px; }
            .css-code:focus { border-color: var(--cl-primary); box-shadow: 0 0 0 2px rgba(229, 62, 62, 0.2); }
            .toggle-icon { display: flex; align-items: center; justify-content: center; transition: transform 0.2s ease-in-out; }
            .collapsed .toggle-icon { transform: rotate(-90deg); }
            .save-css-btn { margin-top: 8px; background-color: var(--cl-success); border-color: var(--cl-success); border-radius: 6px; display: none; }
            .save-css-btn:hover { background-color: #38a169; border-color: #38a169; }
            .save-status { margin-left: 10px; font-style: italic; font-size: 12px; color: var(--cl-text-light); }
            .loading-spinner { border: 4px solid #f3f3f3; border-top: 4px solid var(--cl-primary); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 40px auto; }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const cssData = <?php echo json_encode( $this->get_elementor_css_data() ); ?>;
                const treeContainer = document.getElementById('css-tree');
                const searchInput = document.getElementById('css-search');
                const exportBtn = document.getElementById('export-css-btn');
                const ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
                const nonce = '<?php echo wp_create_nonce('css_locator_nonce'); ?>';

                const ICONS = {
                    page: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #f6ad55;"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>`,
                    element: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #4a5568;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>`,
                    toggle: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>`
                };

                function buildTree() {
                    if (Object.keys(cssData).length === 0) {
                        treeContainer.innerHTML = '<p>No custom CSS found in Elementor.</p>';
                        return;
                    }
                    
                    let treeHTML = '<ul>';
                    for (const pageId in cssData) {
                        const page = cssData[pageId];
                        treeHTML += `<li data-page-id="${pageId}"><span class="page-title">${ICONS.page}<span class="toggle-icon">${ICONS.toggle}</span>${page.title}</span><ul class="page-elements">`;
                        page.elements.forEach(element => {
                            treeHTML += `<li data-element-id="${element.id}" data-element-type="${element.type}"><span class="element-title">${ICONS.element}<span class="toggle-icon">${ICONS.toggle}</span>${element.type} (ID: ${element.id})</span>`;
                            treeHTML += `<div class="css-code-wrapper" style="display:none;">`;
                            treeHTML += `<textarea class="css-code">${element.css}</textarea>`;
                            treeHTML += `<button class="save-css-btn button button-primary" data-page-id="${pageId}" data-element-id="${element.id}">Save</button><span class="save-status"></span>`;
                            treeHTML += `</div></li>`;
                        });
                        treeHTML += '</ul></li>';
                    }
                    treeHTML += '</ul>';
                    treeContainer.innerHTML = treeHTML;
                    
                    document.querySelectorAll('.page-elements').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('.page-title, .element-title').forEach(el => el.parentElement.classList.add('collapsed'));
                }
                
                treeContainer.addEventListener('click', function(e) {
                    const target = e.target;
                    const pageTitle = target.closest('.page-title');
                    const elementTitle = target.closest('.element-title');

                    if (pageTitle) {
                         const li = pageTitle.parentElement;
                         const elementsList = li.querySelector('.page-elements');
                         elementsList.style.display = elementsList.style.display === 'none' ? 'block' : 'none';
                         li.classList.toggle('collapsed');
                    } else if (elementTitle) {
                        const li = elementTitle.parentElement;
                        const codeWrapper = li.querySelector('.css-code-wrapper');
                        codeWrapper.style.display = codeWrapper.style.display === 'none' ? 'block' : 'none';
                        li.classList.toggle('collapsed');
                    } else if (target.classList.contains('save-css-btn')) {
                        saveCSS(target);
                    }
                });

                treeContainer.addEventListener('input', function(e) {
                    if (e.target.classList.contains('css-code')) {
                        const saveBtn = e.target.closest('.css-code-wrapper').querySelector('.save-css-btn');
                        saveBtn.style.display = 'inline-block';
                    }
                });

                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    document.querySelectorAll('#css-tree > ul > li').forEach(pageLi => {
                        let pageVisible = false;
                        pageLi.querySelectorAll('ul.page-elements > li').forEach(elementLi => {
                            const elementType = elementLi.dataset.elementType.toLowerCase();
                            const elementId = elementLi.dataset.elementId.toLowerCase();
                            const cssCode = elementLi.querySelector('.css-code').value.toLowerCase();
                            const isMatch = elementType.includes(searchTerm) || elementId.includes(searchTerm) || cssCode.includes(searchTerm);
                            
                            if (isMatch) {
                                elementLi.style.display = 'block';
                                pageVisible = true;
                            } else {
                                elementLi.style.display = 'none';
                            }
                        });
                        if (pageVisible) {
                            pageLi.style.display = 'block';
                            const pageElements = pageLi.querySelector('.page-elements');
                            if(pageElements) pageElements.style.display = 'block';
                            pageLi.classList.remove('collapsed');
                        } else {
                            pageLi.style.display = 'none';
                        }
                    });
                });

                exportBtn.addEventListener('click', function() {
                    let fullCss = `/* CSS Exported from CSS Locator - ${new Date().toLocaleString()} */\n\n`;
                    for (const pageId in cssData) {
                        const page = cssData[pageId];
                        fullCss += `/*\n * Page: ${page.title} (ID: ${pageId})\n */\n\n`;
                        page.elements.forEach(element => {
                            const cssValue = document.querySelector(`li[data-page-id="${pageId}"] li[data-element-id="${element.id}"] .css-code`).value;
                            fullCss += `/* Element: ${element.type} (ID: ${element.id}) */\n`;
                            fullCss += `selector {\n${cssValue}\n}\n\n`;
                        });
                    }

                    const blob = new Blob([fullCss], { type: 'text/css' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'elementor-custom-styles.css';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                });

                function saveCSS(button) {
                    const wrapper = button.closest('.css-code-wrapper');
                    const status = wrapper.querySelector('.save-status');
                    const code = wrapper.querySelector('.css-code').value;
                    const pageId = button.dataset.pageId;
                    const elementId = button.dataset.elementId;

                    status.textContent = 'Saving...';
                    button.disabled = true;

                    const formData = new FormData();
                    formData.append('action', 'save_elementor_css');
                    formData.append('nonce', nonce);
                    formData.append('post_id', pageId);
                    formData.append('element_id', elementId);
                    formData.append('css', code);

                    fetch(ajax_url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            status.textContent = 'Saved!';
                        } else {
                            status.textContent = `Error: ${result.data.message}`;
                        }
                    })
                    .catch(error => {
                        status.textContent = 'Request failed.';
                    })
                    .finally(() => {
                        button.disabled = false;
                        setTimeout(() => { 
                            status.textContent = '';
                            button.style.display = 'none';
                        }, 3000);
                    });
                }
                
                buildTree();
            });
        </script>
        <?php
    }

    public function get_elementor_css_data() {
        $data = [];
        $args = [
            'post_type'      => ['page', 'post', 'e-landing-page'],
            'posts_per_page' => -1,
            'meta_key'       => '_elementor_data',
        ];
        $posts_with_elementor = new WP_Query( $args );

        if ( $posts_with_elementor->have_posts() ) {
            while ( $posts_with_elementor->have_posts() ) {
                $posts_with_elementor->the_post();
                $post_id = get_the_ID();
                $elementor_data = get_post_meta( $post_id, '_elementor_data', true );

                if ( is_string($elementor_data) && !empty($elementor_data) ) {
                    $elements = json_decode( $elementor_data, true );
                    if (is_array($elements)) {
                        $found_css = [];
                        $this->find_css_in_elements( $elements, $found_css );
                        if (!empty($found_css)) {
                            $data[ $post_id ] = [
                                'title' => get_the_title(),
                                'elements' => $found_css
                            ];
                        }
                    }
                }
            }
            wp_reset_postdata();
        }
        return $data;
    }

    private function find_css_in_elements( $elements, &$found_css ) {
        foreach ( $elements as $element ) {
            if ( isset( $element['settings']['custom_css'] ) && $element['settings']['custom_css'] !== '' ) {
                 $found_css[] = [
                    'id' => $element['id'],
                    'type' => $element['elType'],
                    'css' => $element['settings']['custom_css']
                ];
            }
            if ( ! empty( $element['elements'] ) ) {
                $this->find_css_in_elements( $element['elements'], $found_css );
            }
        }
    }

    public function save_elementor_css() {
        check_ajax_referer( 'css_locator_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        $element_id = isset( $_POST['element_id'] ) ? sanitize_text_field( $_POST['element_id'] ) : '';
        $new_css = isset( $_POST['css'] ) ? $_POST['css'] : '';

        if ( ! $post_id || ! $element_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( [ 'message' => 'Invalid permissions or data.' ] );
        }

        $elementor_data_raw = get_post_meta( $post_id, '_elementor_data', true );
        if ( empty($elementor_data_raw) ) {
            wp_send_json_error( [ 'message' => 'Elementor data not found.' ] );
        }

        $elementor_data = json_decode($elementor_data_raw, true);

        $data_updated_flag = false;
        $updated_data = $this->update_css_in_elements($elementor_data, $element_id, $new_css, $data_updated_flag);

        if (!$data_updated_flag) {
            wp_send_json_error( [ 'message' => 'Element not found in data.' ] );
            return;
        }

        $updated_data_string = wp_slash( json_encode( $updated_data ) );
        $result = update_post_meta($post_id, '_elementor_data', $updated_data_string);

        if ($result === false) {
             wp_send_json_error(['message' => 'Failed to update post meta.']);
        }

        if ( class_exists( '\Elementor\Plugin' ) ) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }

        wp_send_json_success();
    }

    private function update_css_in_elements($elements, $target_id, $new_css, &$data_updated_flag) {
        for ($i = 0; $i < count($elements); $i++) {
            if ($elements[$i]['id'] === $target_id) {
                $elements[$i]['settings']['custom_css'] = $new_css;
                $data_updated_flag = true;
                return $elements;
            }
            if (!empty($elements[$i]['elements'])) {
                $updated_children = $this->update_css_in_elements($elements[$i]['elements'], $target_id, $new_css, $data_updated_flag);
                if ($data_updated_flag) {
                     $elements[$i]['elements'] = $updated_children;
                     return $elements;
                }
            }
        }
        return $elements;
    }
}

function CSS_Locator() {
    return CSS_Locator::instance();
}
$GLOBALS['css_locator'] = CSS_Locator();
