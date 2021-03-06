<?php
   /**
    * WPИ-XM Server Stack - Webinterface
    * Jens-André Koch © 2010 - onwards
    * http://wpn-xm.org/
    *
    *        _\|/_
    *        (o o)
    +-----oOO-{_}-OOo------------------------------------------------------------------+
    |                                                                                  |
    |    LICENSE                                                                       |
    |                                                                                  |
    |    WPИ-XM Serverstack is free software; you can redistribute it and/or modify    |
    |    it under the terms of the GNU General Public License as published by          |
    |    the Free Software Foundation; either version 2 of the License, or             |
    |    (at your option) any later version.                                           |
    |                                                                                  |
    |    WPИ-XM Serverstack is distributed in the hope that it will be useful,         |
    |    but WITHOUT ANY WARRANTY; without even the implied warranty of                |
    |    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                 |
    |    GNU General Public License for more details.                                  |
    |                                                                                  |
    |    You should have received a copy of the GNU General Public License             |
    |    along with this program; if not, write to the Free Software                   |
    |    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA    |
    |                                                                                  |
    +----------------------------------------------------------------------------------+
    *
    * @license    GNU/GPL v2 or (at your option) any later version..
    * @author     Jens-André Koch <jakoch@web.de>
    * @copyright  Jens-André Koch (2010 - onwards)
    * @link       http://wpn-xm.org/
    */

function index()
{
    $tpl_data = array(
        'load_jquery' => true
    );

    render('page-action', $tpl_data);
}

function showtab()
{
    /**
     * Tab Controller - handles GET requests for tab pages.
     * Calls to tab pages look like this: "index.php?page=config&action=showtab&tab=xy".
     * Each tab returns content for inline display in the tabs-content container.
     */
    $tab = filter_input(INPUT_GET, 'tab');
    $tab = strtr($tab, '-', '_'); // minus to underscore conversion
    $tabAction = 'showtab_' . $tab;
    if (false === is_callable($tabAction)) {
        throw new \Exception(sprintf('The controller method "%s" for the Tab "%s" was not found!', $tabAction, $tab));
    }
    $tabAction();
}

function showtab_help()
{
    render('config-showtab-help', array('no_layout' => true));
}

function showtab_mariadb()
{
    render('config-showtab-mariadb', array('no_layout' => true));
}

function showtab_mongodb()
{
    render('config-showtab-mongodb', array('no_layout' => true));
}

function showtab_nginx()
{
    render('config-showtab-nginx', array('no_layout' => true));
}

function showtab_nginx_domains()
{
    $projects = new Webinterface\Helper\Projects;
    $domains = new Webinterface\Helper\Domains;

    $tpl_data = array(
        'no_layout' => true,
        'project_folders' => $projects->fetchProjectDirectories(true),
        'domains' => $domains->listDomains()
    );

    render('config-showtab-nginx-domains', $tpl_data);
}

function showtab_php()
{
    $tpl_data = array(
        'no_layout' => true,
        'ini' => Webinterface\Helper\PHPINI::read(), // $ini array structure = 'ini_file', 'ini_array'
    );

    render('config-showtab-php', $tpl_data);
}

function showtab_php_ext()
{
    $phpext = new Webinterface\Helper\PHPExtensionManager();

    $tpl_data = array(
        'no_layout' => true,
        'available_extensions' => $phpext->getExtensionDirFileList(),
        'enabled_extensions' => $phpext->getEnabledExtensionsFromIni(),
        //'loaded_extensions' => $phpext->getExtensionsLoaded()
        'form' => renderPHPExtensionsFormContent()
    );

    render('config-showtab-phpext', $tpl_data);
}

function showtab_xdebug()
{
    $tpl_data = array(
        'no_layout' => true,
        'ini_settings' => ini_get_all('xdebug'),
    );

    render('config-showtab-xdebug', $tpl_data);
}

function update_phpextensions()
{
    $extensions = $_POST['extensions'];
    //var_dump($extensions); /* show extensions to enable */

    $extensionManager = new Webinterface\Helper\PHPExtensionManager();

    $enabledExtensions = array_flip($extensionManager->getEnabledExtensionsFromIni());

    $disableTheseExtensions = array_diff($enabledExtensions, $extensions);
    $disableTheseExtensions = array_values($disableTheseExtensions); // re-index

    //var_dump($disableTheseExtensions); /* show extensions to disable */

    foreach ($extensions as $extension) {
        $extensionManager->enable($extension);
    }

    foreach ($disableTheseExtensions as $extension) {
        $extensionManager->disable($extension);
    }

    // prepare response data
    $array = array(
        'enabled_extensions' => $extensions,
        'disabled_extensions' => $disableTheseExtensions,
        'responseText' => 'Extensions updated - PHP restarting...'
    );

    // send as JSON
    echo json_encode($array);
}

function renderPHPExtensionsFormContent()
{
    $phpext = new Webinterface\Helper\PHPExtensionManager();
    $available_extensions = $phpext->getExtensionDirFileList();
    $enabled_extensions = $phpext->getEnabledExtensionsFromIni();

    $html_checkboxes = '';
    $i = 1; // start at first element
    $itemsTotal = count($available_extensions); // elements total

    // use list of available_extensions to draw checkboxes
    foreach ($available_extensions as $name => $file) {
        // if extension is enabled, check the checkbox
        $checked = false;
        if (isset($enabled_extensions[$file])) {
            $checked = true;
        }

        /**
         * Deactivate the checkbox for the XDebug Extension.
         * XDebug is not loaded as normal PHP extension ([PHP]extension=).
         * It is loaded as a Zend Engine extension ([ZEND]zend_extension=).
         */
        $disabled = '';
        if (strpos($name, 'xdebug') !== false) {
            $disabled = 'disabled';
        }

        // render column opener (everytime on 1 of 12)
        if ($i === 1) {
            $html_checkboxes .= '<div class="control-group" style="float: left; width: 125px; margin: 10px;">';
        }

        // the input tag is wrapped by the label tag
        $html_checkboxes .= '<label class="checkbox';
        $html_checkboxes .= ($checked === true) ? ' active-element">' : ' not-active-element">';
        $html_checkboxes .= '<input type="checkbox" name="extensions[]" value="'.$file.'" ';
        $html_checkboxes .= ($checked === true) ? 'checked="checked" ' : '';
        $html_checkboxes .=  $disabled.'>';
        $html_checkboxes .= substr($name, 4);
        $html_checkboxes .= '</label>';

        if ($i === 12 or $itemsTotal === 1) {
            $html_checkboxes .= '</div>';
            $i = 0; /* reset column counter */
        }

        $i++;
        $itemsTotal--;
    }

   if (isAjaxRequest() and !isset($_GET['tab'])) {
       echo $html_checkboxes;
   } else {
       return $html_checkboxes;
   }
}

function update_phpini_setting()
{
    $section = ''; // @todo section? needed to save the directive? string (directive=>value) is unique!?
    $directive = filter_input(INPUT_POST, 'directive');
    $value = filter_input(INPUT_POST, 'value');

    Webinterface\Helper\PHPINI::setDirective($section, $directive, $value);

    Webinterface\Helper\Daemon::restartDaemon('php');

    echo '<div class="modal"><p class="info">Saved. PHP restarted.</div>';
}
