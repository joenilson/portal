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
require_once 'plugins/portal/extras/verot/class.upload.php';

/**
 * Description of portal_admin
 *
 * @author Joe Nilson <joenilson at gmail.com>
 */
class portal_admin extends fs_controller {

    public $page_name;
    public $page_title;
    public $page_header;
    public $page_body;
    public $page_footer;
    public $page_logo;
    public $page_template;
    public $portal_base;
    public $portal_contenido;
    public $portal_templates;
    public $portal_socialnetworks;
    public $page_info;
    public $socialnetworks;
    public $dir_portal;
    private $upload_photo;
    public $noimagen = "plugins/portal/view/images/no_logo.jpg";

    public function __construct() {
        parent::__construct(__CLASS__, "Administración Básica", 'portal', TRUE, TRUE, FALSE);
    }

    protected function private_core() {
        $this->portal_base = new portal_base();
        $this->portal_contenido = new portal_contenido();
        $this->portal_templates = new portal_templates();
        $this->portal_socialnetworks = new portal_socialnetworks();
        $this->share_extensions();
        //Creamos la carpeta para los archivos temporales como logo, firma y/o otros
        $basepath = dirname(dirname(dirname(__DIR__)));
        $this->dir_portal = $basepath . DIRECTORY_SEPARATOR. "images" . DIRECTORY_SEPARATOR . "portal";
        //Validamos si existe el directorio raiz dentro de la carpeta tmp para los archivos del portal
        if (!is_dir($this->dir_portal)) {
            mkdir($this->dir_portal);
        }

        $type = \filter_input(INPUT_POST, 'tipo');
        if (!empty($type)) {
            if ($type == 'basic-info') {
                $this->update_info();
            }elseif($type== 'activar-portal'){
                $this->tratar_portal(true);
            }elseif($type== 'desactivar-portal'){
                $this->tratar_portal();
            }elseif($type=='socialnetworks'){
                $this->tratar_socialnetworks();
            }
        }
        $this->page_info = $this->portal_base->get($this->empresa->id);
        $this->page_template = $this->portal_templates->get($this->page_info->page_template);
        $this->socialnetworks = $this->portal_socialnetworks->all();
    }

    public function tratar_socialnetworks(){
        $redes_sociales = array();
        $redes_sociales['fa fa-twitter fw']="https://www.twitter.com/";
        $redes_sociales['fa fa-facebook fw']="https://www.facebook.com/";
        $redes_sociales['fa fa-instagram fw']="https://www.instagram.com/";
        $redes_sociales['fa fa-linkedin fw']="https://www.linkedin.com/";
        $redes_sociales['fa fa-github fw']="https://www.github.com/";
        $accion = \filter_input(INPUT_POST, 'accion');
        $sn_id = \filter_input(INPUT_POST, 'sn_id');
        $sn_name = \filter_input(INPUT_POST, 'sn_name');
        $sn_url = \filter_input(INPUT_POST, 'sn_url');
        $sn_logo = \filter_input(INPUT_POST, 'sn_logo');
        $sn_status = \filter_input(INPUT_POST, 'sn_status');
        $sn_order = \filter_input(INPUT_POST, 'sn_order');
        if($accion=='eliminar'){
            $sn0 = new portal_socialnetworks();
            $sn = $sn0->get($sn_id);
            if($sn){
                if($sn->delete()){
                    $this->new_message('Red Social '.$sn_name.' eliminada correctamente.');
                }
            }else{
                $this->new_error_msg('Ocurrió un error al tratar la Red Social seleccionada, por favor revise la información ingresada.');
            }
        }else{
            $sn0 = new portal_socialnetworks();
            $sn0->sn_id = $sn_id;
            $sn0->sn_name = $sn_name;
            $sn0->sn_logo = $sn_logo;
            $sn0->sn_url = ($sn_id)?$sn_url:$redes_sociales[$sn_logo].$sn_url;
            $sn0->sn_order = $sn_order;
            $sn0->sn_status = $sn_status;
            if($sn0->save()){
                $this->new_message('Red Social '.$sn0->sn_name.' actualizada correctamente.');
            }else{
                $this->new_error_msg('Ocurrió un error al tratar la Red Social '.$sn0->sn_name.', por favor revise la información ingresada.');
            }
        }
    }

    public function tratar_portal($activar = false) {
        $GLOBALS['config2']['homepage'] = ($activar)?'portal':'admin_home';
        $file = fopen('tmp/' . FS_TMP_NAME . 'config2.ini', 'w');
        if ($file) {
            foreach ($GLOBALS['config2'] as $i => $value) {
                if (is_numeric($value)) {
                    fwrite($file, $i . " = " . $value . ";\n");
                } else {
                    fwrite($file, $i . " = '" . $value . "';\n");
                }
            }
            fclose($file);
        }
        $accion = ($activar)?'activada':'desactivada';
        $this->new_message('Página Portada '.$accion.' correctamente.');
    }

    public function update_info() {
        $page_id = \filter_input(INPUT_POST, 'page_id');
        $page_name = \filter_input(INPUT_POST, 'page_name');
        $page_title = \filter_input(INPUT_POST, 'page_title');
        $page_subtitle = \filter_input(INPUT_POST, 'page_subtitle');
        $page_email = \filter_input(INPUT_POST, 'page_email');
        $page_status = \filter_input(INPUT_POST, 'page_status');
        $page_footer = \filter_input(INPUT_POST, 'page_footer');
        $this->page_info = $this->portal_base->get($page_id);
        $this->page_info->page_title = $page_title;
        $this->page_info->page_subtitle = $page_subtitle;
        $this->page_info->page_name = $page_name;
        $this->page_info->page_email = $page_email;
        $this->page_info->page_status = $page_status;
        $this->page_info->page_footer = $page_footer;
        if ($this->page_info->save()) {
            $this->upload_photo = new Upload($_FILES['page_logo']);
            if ($this->upload_photo->uploaded) {
                $this->guardar_logo();
            }
            $this->new_message("Información actualizada correctamente!");
        } else {
            $this->new_error_msg("Hubo un error al procesar los datos, por favor intente nuevamente.");
        }
    }

    //Para guardar el logo hacemos uso de la libreria de class.upload.php que esta en extras/verot/
    //Con esta libreria estandarizamos todas las imagenes en PNG y les hacemos un resize a 200px
    public function guardar_logo() {
        $this->page_logo = $this->page_info->get_logo();
        if (file_exists($this->page_logo)) {
            unlink($this->page_logo);
        }
        $newname = str_pad($this->page_info->page_id, 6, 0, STR_PAD_LEFT);

        // Grabar la imagen con un nuevo nombre y con un resize de 200px
        $this->upload_photo->file_new_name_body = $newname;
        $this->upload_photo->image_resize = true;
        $this->upload_photo->image_convert = 'png';
        $this->upload_photo->image_x = 200;
        $this->upload_photo->file_overwrite = true;
        $this->upload_photo->image_ratio_y = true;
        $this->upload_photo->Process($this->dir_portal);
        if ($this->upload_photo->processed) {
            $this->upload_photo->clean();
            $this->page_info->set_logo($newname . ".png");
            $this->page_info->page_logo = $newname . ".png";
        } else {
            $this->new_error_msg('error : ' . $$this->upload_photo->error);
        }
    }

    public function share_extensions() {
        $extensiones = array(
            array(
                'name' => 'htmleditor_admin_css',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'head',
                'text' => '<link rel="stylesheet" type="text/css" media="screen" href="plugins/portal/view/css/summernote/summernote.css"/>',
                'params' => ''
            ),
            array(
                'name' => 'portal_admin_css',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'head',
                'text' => '<link rel="stylesheet" type="text/css" media="screen" href="plugins/portal/view/css/portal_admin.css"/>',
                'params' => ''
            ),
            array(
                'name' => 'portal_css',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'head',
                'text' => '<link rel="stylesheet" type="text/css" media="screen" href="plugins/portal/view/css/portal.css"/>',
                'params' => ''
            ),
            array(
                'name' => 'portal_css',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'head',
                'text' => '<script src="plugins/portal/view/js/summernote/summernote.min.js" type="text/javascript"></script>',
                'params' => ''
            ),
        );

        foreach ($extensiones as $ext) {
            $fsext0 = new fs_extension($ext);
            if (!$fsext0->save()) {
                $this->new_error_msg('Imposible guardar los datos de la extensión ' . $ext['name'] . '.');
            }
        }
    }

}
