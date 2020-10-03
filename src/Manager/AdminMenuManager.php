<?php

namespace SimplyFramework\Manager;

use Simply;
use SimplyFramework\Admin\Menu\AdminMenu;
use SimplyFramework\Contract\ManagerInterface;
use SimplyFramework\Form\FormGenerator;
use SimplyFramework\Metabox\Metabox;

/**
 * Class MetaboxManager
 * Manage view metabox default
 *
 * @package SimplyFramework\Manager
 */
class AdminMenuManager implements ManagerInterface {
    private $adminMenus;

    /**
     * @var AdminMenu[]
     */
    private $adminMenusInstance;

    /**
     * @var FormGenerator
     */
    private $formGenerator;

    public function __construct(array $adminMenus, FormGenerator $formGenerator) {
        $this->adminMenus = $adminMenus;
        $this->formGenerator = $formGenerator;
    }

    public function initialize() {
        add_action('admin_menu', function () {
            foreach ($this->adminMenus as $aMenuData) {
                $position = null;
                if (array_key_exists('position', $aMenuData)) {
                    $position = $aMenuData['position'];
                }

                // Can be submenu or menu page
                if (!$aMenuData['is_submenu']) {
                    $iconUrl = null;
                    if (array_key_exists('icon_url', $aMenuData)) {
                        $iconUrl = $aMenuData['icon_url'];
                    }
                    $slug = add_menu_page($aMenuData['page_title'], $aMenuData['menu_title'], $aMenuData['capability'], $aMenuData['menu_slug'], [$this, 'renderAdminMenu'], $iconUrl, $position);
                } else {
                    $slug = add_submenu_page($aMenuData['parent_slug'], $aMenuData['page_title'], $aMenuData['menu_title'], $aMenuData['capability'], $aMenuData['menu_slug'], [$this, 'renderAdminMenu'], $position);
                }
                $this->adminMenusInstance[$slug] = new AdminMenu($aMenuData['page_title'], $aMenuData['menu_slug'], $aMenuData['fields'], $this->formGenerator);
            }
        });
    }

    /**
     * Function fired in all admin menu registered with the framework
     */
    public function renderAdminMenu() {
        $currentScreen = get_current_screen();
        $this->adminMenusInstance[$currentScreen->id]->render();
    }
}
