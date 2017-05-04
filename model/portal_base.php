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
require_model('empresa.php');

/**
 * Description of portal_base
 *
 * @author Joe Nilson <joenilson at gmail.com>
 */
class portal_base extends fs_model {

    public $page_id;
    public $page_name;
    public $page_title;
    public $page_subtitle;
    public $page_footer;
    public $page_email;
    public $page_logo;
    public $page_status;
    public $page_template;
    public $empresa;

    public function __construct($t = false) {
        parent::__construct('portal_base', 'plugins/portal/');
        if ($t) {
            $this->page_id = $t['page_id'];
            $this->page_name = $t['page_name'];
            $this->page_title = $t['page_title'];
            $this->page_subtitle = $t['page_subtitle'];
            $this->page_logo = $t['page_logo'];
            $this->page_footer = $t['page_footer'];
            $this->page_email = $t['page_email'];
            $this->page_status = $t['page_status'];
            $this->page_template = $t['page_template'];
        } else {
            $this->page_id = NULL;
            $this->page_name = NULL;
            $this->page_title = NULL;
            $this->page_subtitle = NULL;
            $this->page_logo = NULL;
            $this->page_footer = NULL;
            $this->page_email = NULL;
            $this->page_status = NULL;
            $this->page_template = NULL;
        }

        $this->empresa = new empresa();
    }

    protected function install() {
        $this->empresa = new empresa();
        return "INSERT INTO " . $this->table_name . " (page_id, page_name, page_title, page_footer, page_email, page_subtitle, page_logo, page_status) VALUES (" .
                $this->intval($this->empresa->id) . ", " .
                $this->var2str($this->empresa->nombre) . ", " .
                $this->var2str($this->empresa->nombrecorto) . ", " .
                $this->var2str($this->empresa->horario) . ", " .
                $this->var2str($this->empresa->email) . ", " .
                "'','','M',1);";
    }

    public function get($page_id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE page_id = " . $this->intval($page_id) . ";";
        $data = $this->db->select($sql);
        if ($data) {
            $datos = new portal_base($data[0]);
            return $datos;
        } else {
            return false;
        }
    }

    public function exists() {
        if (empty($this->page_id)) {
            return false;
        } else {
            return $this->get($this->page_id);
        }
    }

    public function save() {
        if ($this->exists()) {
            return $this->update();
        } else {
            $sql = "INSERT INTO " . $this->table_name . " (page_name, page_title, page_footer, page_email, page_subtitle, page_logo, page_status, page_template) VALUES (" .
                    $this->var2str($this->page_name) . ", " .
                    $this->var2str($this->page_title) . ", " .
                    $this->var2str($this->page_footer) . ", " .
                    $this->var2str($this->page_email) . ", " .
                    $this->var2str($this->page_subtitle) . ", " .
                    $this->var2str($this->page_logo) . ", " .
                    $this->var2str($this->page_status) . ", " .
                    $this->var2str($this->page_template) . ")";
            if ($this->db->exec($sql)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function update() {
        $sql = "UPDATE " . $this->table_name . " SET " .
                "page_name = " . $this->var2str($this->page_name) . ", " .
                "page_title = " . $this->var2str($this->page_title) . ", " .
                "page_footer = " . $this->var2str($this->page_footer) . ", " .
                "page_email = " . $this->var2str($this->page_email) . ", " .
                "page_subtitle = " . $this->var2str($this->page_subtitle) . ", " .
                "page_logo = " . $this->var2str($this->page_logo) . ", " .
                "page_status = " . $this->var2str($this->page_status) . ", " .
                "page_template = " . $this->var2str($this->page_template)
                . " WHERE page_id = " . $this->intval($this->page_id) . ";";
        return $this->db->exec($sql);
    }

    public function get_logo() {
        if ($this->page_logo) {
            return 'tmp/'.FS_TMP_NAME.'portal/'.$this->page_logo;
        } else {
            return false;
        }
    }

    public function set_logo($nombre_logo){
        if($nombre_logo){
            $sql = "UPDATE ".$this->table_name." SET page_logo = ".$this->var2str($nombre_logo)." WHERE page_id = ".$this->intval($this->page_id).";";
            return $this->db->exec($sql);
        }else{
            return false;
        }
    }

    public function delete() {
        $sql = "DELETE FROM " . $this->table_name . " WHERE page_id = " . $this->intval($this->page_id) . ";";
        if ($this->db->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }

}
