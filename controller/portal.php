<?php

/*
 * Copyright (C) 2016 Joe Nilson <joenilson at gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_model('portal_base.php');
require_model('portal_contenido.php');
require_model('portal_templates.php');
require_model('portal_socialnetworks.php');

/**
 * Description of portal
 *
 * @author Joe Nilson <joenilson at gmail.com>
 */
class portal extends fs_controller {

    public $portal_base;
    public $portal_contenido;
    public $portal_templates;
    public $portal_template;
    public $portal_socialnetworks;
    public $page_info;
    public $page_contenido;
    public $page_socialnetworks;
    public $zona_privada;

    public function __construct() {
        parent::__construct(__CLASS__, 'Portada', 'portal', FALSE, TRUE, FALSE);
    }

    //Zona privada de administracion
    protected function private_core() {
        $this->zona_privada = true;
        $this->share_connections();
    }

    //Zona publica de visualizacion
    protected function public_core() {
        $this->share_connections();
        $this->template = 'portal';
        $this->page_info = $this->portal_base->get($this->empresa->id);
        $this->page_contenido = $this->portal_contenido->all_activos($this->page_info->page_id);
        $this->page_template = $this->portal_template->get($this->page_info->page_template);
        $this->page_socialnetworks = $this->portal_socialnetworks->activos();
        $this->zona_privada = false;
    }

    public function share_connections() {
        $this->portal_base = new portal_base();
        $this->portal_contenido = new portal_contenido();
        $this->portal_template = new portal_templates();
        $this->portal_socialnetworks = new portal_socialnetworks();
    }

    public function get_css_template_location($filename) {
        $found = FALSE;
        foreach ($GLOBALS['plugins'] as $plugin) {
            if (file_exists('plugins/' . $plugin . '/view/templates/' . $filename)) {
                return FS_PATH . 'plugins/' . $plugin . '/view/templates/' . $filename . '?updated=' . date('YmdH');
            }
        }

        /// si no está en los plugins estará en el núcleo
        //return FS_PATH . 'view/css/' . $filename . '?updated=' . date('YmdH');
    }

    public function full_url() {
        $url = $this->empresa->web;

        if (isset($_SERVER['SERVER_NAME'])) {
            if ($_SERVER['SERVER_NAME'] == 'localhost') {
                $url = 'http://' . $_SERVER['SERVER_NAME'];

                if (isset($_SERVER['REQUEST_URI'])) {
                    $aux = parse_url(str_replace('/index.php', '', $_SERVER['REQUEST_URI']));
                    $url .= $aux['path'];
                }
            }
        }

        if (substr($url, -1) == '/') {
            $url = substr($url, 0, -1);
        }

        return $url;
    }

}
