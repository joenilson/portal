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

/**
 * Description of portal_templates
 *
 * @author Joe Nilson <joenilson at gmail.com>
 */
class portal_templates extends fs_model {

    public $template_id;
    public $template_name;
    public $template_type;
    public $template_folder;
    public $template_status;
    public $empresa;

    public function __construct($t = false) {
        parent::__construct('portal_templates', 'plugins/portal/');
        if ($t) {
            $this->template_id = $t['template_id'];
            $this->template_name = $t['template_name'];
            $this->template_type = $t['template_type'];
            $this->template_folder = $t['template_folder'];
            $this->template_status = $t['template_status'];
        } else {
            $this->template_id = NULL;
            $this->template_name = NULL;
            $this->template_type = NULL;
            $this->template_folder = NULL;
            $this->template_status = NULL;
        }
    }

    protected function install() {
        return "INSERT INTO " . $this->table_name . " (template_name, template_type, template_folder, template_status) VALUES (" .
                "'Plantilla Basica','pagina_unica','basica',true".");";
    }

    public function get($template_id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE template_id = " . $this->intval($template_id) . ";";
        $data = $this->db->select($sql);
        if ($data) {
            $datos = new portal_templates($data[0]);
            return $datos;
        } else {
            return false;
        }
    }

    public function exists() {
        if (empty($this->template_id)) {
            return false;
        } else {
            return $this->get($this->template_id);
        }
    }

    public function save() {
        if ($this->exists()) {
            return $this->update();
        } else {
            $sql = "INSERT INTO " . $this->table_name . " (template_name, template_type, template_folder, template_status) VALUES (" .
                    $this->var2str($this->template_name) . ", " .
                    $this->var2str($this->template_type) . ", " .
                    $this->var2str($this->template_folder) . ", " .
                    $this->var2str($this->template_status) . ")";
            if ($this->db->exec($sql)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function update() {
        $sql = "UPDATE " . $this->table_name . " SET " .
                "template_name = " . $this->var2str($this->template_name) . ", " .
                "template_type = " . $this->var2str($this->template_type) . ", " .
                "template_folder = " . $this->var2str($this->template_folder) . ", " .
                "template_status = " . $this->var2str($this->template_status)
                . " WHERE template_id = " . $this->intval($this->template_id) . ";";
        return $this->db->exec($sql);
    }

    public function delete() {
        $sql = "DELETE FROM " . $this->table_name . " WHERE template_id = " . $this->intval($this->template_id) . ";";
        if ($this->db->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }

}
