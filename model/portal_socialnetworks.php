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
 * Description of portal_socialnetworks
 *
 * @author Joe Nilson <joenilson at gmail.com>
 */
class portal_socialnetworks extends fs_model {
    public $sn_id;
    public $sn_name;
    public $sn_url;
    public $sn_logo;
    public $sn_order;
    public $sn_status;

    public $empresa;
    public function __construct($t = false) {
        parent::__construct('portal_socialnetworks','plugins/portal/');
        if($t){
            $this->sn_id = $t['sn_id'];
            $this->sn_name = $t['sn_name'];
            $this->sn_url = $t['sn_url'];
            $this->sn_logo = $t['sn_logo'];
            $this->sn_order = $t['sn_order'];
            $this->sn_status = $this->str2bool($t['sn_status']);
        }else{
            $this->sn_id = NULL;
            $this->sn_name = NULL;
            $this->sn_url = NULL;
            $this->sn_logo = NULL;
            $this->sn_order = NULL;
            $this->sn_status = FALSE;
        }
    }

    protected function install() {
        return "INSERT INTO ".$this->table_name." (sn_name, sn_url, sn_logo, sn_order, sn_status) VALUES ".
                "('Facebook','https://www.facebook.com','images/facebook_logo.png',1,TRUE),".
                "('Twitter','https://www.twitter.com','images/twitter_logo.png',2,TRUE),".
                "('Instagram','https://www.instagram.com','images/instagram_logo.png',3,TRUE),".
                "('LinkedIn','https://www.linkedin.com','images/linkedin_logo.png',4,TRUE);";
    }

    public function all(){
        $sql = "SELECT * FROM ".$this->table_name." ORDER BY sn_id;";
        $data = $this->db->select($sql);
        $lista = array();
        if($data){
            foreach($data as $values){
                $lista[] = new portal_socialnetworks($values);
            }
            return $lista;
        }else{
            return false;
        }
    }

    public function activos(){
        $sql = "SELECT * FROM ".$this->table_name." WHERE sn_status = TRUE ORDER BY sn_id;";
        $data = $this->db->select($sql);
        $lista = array();
        if($data){
            foreach($data as $values){
                $lista[] = new portal_socialnetworks($values);
            }
            return $lista;
        }else{
            return false;
        }
    }

    public function get($sn_id){
        $sql = "SELECT * FROM ".$this->table_name." WHERE sn_id = ".$this->intval($sn_id).";";
        $data = $this->db->select($sql);
        if($data){
            $datos = new portal_socialnetworks($data[0]);
            return $datos;
        }else{
            return false;
        }
    }

    public function exists() {
        if(empty($this->sn_id)){
            return false;
        }else{
            return $this->get($this->sn_id);
        }
    }

    public function save() {
        if($this->exists()){
            return $this->update();
        }else{
            $sql = "INSERT INTO ".$this->table_name." (sn_name, sn_url, sn_order, sn_logo, sn_status) VALUES (".
                $this->var2str($this->sn_name).", ".
                $this->var2str($this->sn_url).", ".
                $this->var2str($this->sn_order).", ".
                $this->var2str($this->sn_logo).", ".
                $this->var2str($this->sn_status).");";
            if($this->db->exec($sql)){
                return true;
            }else{
                return false;
            }
        }
    }

    public function update(){
        $sql = "UPDATE ".$this->table_name." SET ".
                "sn_name = ".$this->var2str($this->sn_name).", ".
                "sn_url = ".$this->var2str($this->sn_url).", ".
                "sn_order = ".$this->intval($this->sn_order).", ".
                "sn_logo = ".$this->var2str($this->sn_logo).", ".
                "sn_status = ".$this->var2str($this->sn_status).
                " WHERE sn_id = ".$this->intval($this->sn_id).";";
        return $this->db->exec($sql);
    }

    public function delete() {
        $sql = "DELETE FROM ".$this->table_name." WHERE sn_id = ".$this->intval($this->sn_id).";";
        if($this->db->exec($sql)){
            return true;
        }else{
            return false;
        }
    }
}
