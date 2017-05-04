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
 * Description of portal_contenido
 *
 * @author Joe Nilson <joenilson at gmail.com>
 */
class portal_contenido extends fs_model {

    public $page_id;
    public $page_template;
    public $content_id;
    public $content_title;
    public $content_data;
    public $content_order;
    public $content_status;


    public function __construct($t = false) {
        parent::__construct('portal_contenido', 'plugins/portal/');
        if ($t) {
            $this->page_id = $t['page_id'];
            $this->content_id = $t['content_id'];
            $this->content_title = $t['content_title'];
            $this->content_data = $t['content_data'];
            $this->content_order = $t['content_order'];
            $this->content_status = $this->str2bool($t['content_status']);
            $this->page_template = $t['page_template'];
        } else {
            $this->page_id = NULL;
            $this->content_id = NULL;
            $this->content_title = NULL;
            $this->content_data = NULL;
            $this->content_order = NULL;
            $this->content_status = FALSE;
            $this->page_template = NULL;
        }

    }

    protected function install() {
        $contenido = htmlentities('
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-sm-6">
                    <hr class="section-heading-spacer">
                    <div class="clearfix"></div>
                    <h2 class="section-heading">Pagina en construccion:<br>Gracias por su visita</h2>
                    <p class="lead">Proximanente grandes contenidos. Visite nuestra página para una variada información.</p>
                </div>
                <div class="col-lg-5 col-lg-offset-2 col-sm-6">

                </div>
            </div>

        </div>');


        return "INSERT INTO " . $this->table_name . " (page_id, page_template, content_id, content_title, content_data, content_order, content_status) VALUES (" .
                $this->intval(1) . ", " .
                $this->intval(1) . ", " .
                $this->intval(1) . ", " .
                $this->var2str('En contruccion') . ", " .
                $this->var2str($contenido) . ", " .
                $this->intval(1) . ", " .
                $this->var2str('TRUE') . ");";
    }

    public function get($content_id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE content_id = ". $this->intval($content_id) . ";";
        $data = $this->db->select($sql);
        if ($data) {
            $datos = new portal_contenido($data[0]);
            return $datos;
        } else {
            return false;
        }
    }

    public function all($page_id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE page_id = ". $this->intval($page_id) . " ORDER BY content_order;";
        $data = $this->db->select($sql);
        if ($data) {
            $lista = array();
            foreach($data as $d){
                $lista[] = new portal_contenido($d);
            }
            return $lista;
        } else {
            return false;
        }
    }

    public function all_activos($page_id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE page_id = ". $this->intval($page_id) . " AND content_status = TRUE ORDER BY content_order;";
        $data = $this->db->select($sql);
        if ($data) {
            $lista = array();
            foreach($data as $d){
                $lista[] = new portal_contenido($d);
            }
            return $lista;
        } else {
            return false;
        }
    }

    public function getNextId(){
        $sql = "SELECT max(id) AS id FROM ".$this->table_name.";";
        $data = $this->db->select($sql);
        $id = $data[0]['id']++;
        return $id;
    }

    public function exists() {
        if (is_null($this->content_id)) {
            return false;
        } else {
            return $this->get($this->content_id);
        }
    }

    public function save() {
        if ($this->exists()) {
            return $this->update();
        } else {
            $sql = "INSERT INTO " . $this->table_name . " (page_id, page_template, content_id, content_title, content_order, content_data, content_status) VALUES (" .
                    $this->intval($this->page_id) . ", " .
                    $this->intval($this->page_template) . ", " .
                    $this->intval($this->getNextId()) . ", " .
                    $this->var2str($this->content_title) . ", " .
                    $this->var2str($this->content_order) . ", " .
                    $this->var2str($this->content_data) . ", " .
                    $this->var2str($this->content_status) . ");";
            if ($this->db->exec($sql)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function update() {
        $sql = "UPDATE " . $this->table_name . " SET " .
                "page_id = " . $this->var2str($this->content_id) . ", " .
                "content_title = " . $this->var2str($this->content_title) . ", " .
                "content_order = " . $this->var2str($this->content_order) . ", " .
                "content_data = " . $this->var2str($this->content_data) . ", " .
                "content_status = " . $this->var2str($this->content_status) . ", " .
                "page_template = " . $this->intval($this->page_template)
                . " WHERE content_id = " . $this->intval($this->content_id) . ";";
        return $this->db->exec($sql);
    }

    public function delete() {
        $sql = "DELETE FROM " . $this->table_name . " WHERE page_id = " . $this->intval($this->page_id) . " AND content_id = ".$this->intval($this->content_id).";";
        if ($this->db->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }

}
