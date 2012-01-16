<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * This block display random image from lightboxgallery. 
 *
 * Depends on lightboxgallery module.
 *
 * @package    block_lightboxgallery_random 
 * @copyright  2011 Paweł Suwiński 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class block_lightboxgallery_random extends block_base {

    const LIGHTBOXGALLERY_MODULE_NAME = 'lightboxgallery';

    function init() {
        global $CFG;

        $this->title = get_string('blockname', __CLASS__);
        $this->version = 2011120902;

        self::block_meet_dependencies();
    }

    function get_content() {
        global $COURSE, $CFG;

        if (empty($this->instance)) {
            return $this->content;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }
        
        if(isset($this->config->title) && !empty($this->config->title)){
            $this->title= $this->config->title ;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        if(!$CFG->block_lightboxgallery_random_avaible)
        {
            $this->content->text = get_string('lightboxgalleryunavaible',__CLASS__);
            return $this->content;
        }

        if (
                !isset($this->config->gallery) || 
                !$gallery = get_record(self::LIGHTBOXGALLERY_MODULE_NAME, 'id',$this->config->gallery)
        ) {
            $this->content->text = get_string('nogallery',__CLASS__);
            return $this->content;
        }

        if(!isset($this->config->title) || empty($this->config->title)){
            $this->title= $gallery->name ;
        }

        if (! $cm = get_coursemodule_from_instance(
            self::LIGHTBOXGALLERY_MODULE_NAME, $gallery->id, $COURSE->id)) {
            $this->content->text = 'Course module error!'; 
            return $this->content;
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $context)) {
            $this->content->text = get_string('galleryishidden',__CLASS__);
            return $this->content;
        }

        require_once($CFG->dirroot.'/mod/'.self::LIGHTBOXGALLERY_MODULE_NAME.'/lib.php');
        $dataroot = $CFG->dataroot . '/' . $COURSE->id . '/' . $gallery->folder;
        $webroot = lightboxgallery_get_image_url($gallery->id);
        $image = lightboxgallery_directory_images($dataroot);

        if (empty($image)) {
            $this->content->text = get_string('noimages',__CLASS__);
            return $this->content;
        }

		$image = $image[rand(0 , count($image) - 1)];
        $imageurl = $webroot.'/'.$image;

        $imagetitle = get_field(
            'lightboxgallery_image_meta','description',
            'metatype','caption',
            'gallery',$gallery->id,
            'image',$image
      );
        
        if(empty($imagetitle)){
            $imagetitle = $image;
        }

      if(isset($this->config->fitwidth) && $this->config->fitwidth == 1) {
        $html_attributes=$this->html_attributes();

        $this->content->text ='
 <style type="text/css">
        div#'.$html_attributes['id'].' div.content img { width: 100% ;}
 </style>
';                                            
        }
        $this->content->text .='
<center>
<a  href="'.$imageurl.'" title="'.$imagetitle.'" onclick="this.target=\''.$imagetitle.'\'; return openpopup(\''.preg_replace('#^'.$CFG->wwwroot.'#','',$imageurl).'\', \''.$imagetitle.'\', null, false);">'.lightboxgallery_image_thumbnail($COURSE->id, $gallery, $image).'</a>
</center>
';
      if(isset($this->config->showurl) && $this->config->showurl == 1) {
            $this->content->text .='<a href="'.$CFG->wwwroot.'/mod/'.
                self::LIGHTBOXGALLERY_MODULE_NAME.'/view.php?l='.$gallery->id.'">'.
                get_string('gotogallery',__CLASS__).'</a>';
      }

        return $this->content;
    }

    /**
    * Check if the lightboxgallery module is installed and visible.
    * If not it this block is deactivated.
    *
    * For purpose of user_can_addto it can be use as static method. 
    *
    * @return bool lightboxgallery module  is avaible or not not
    */
    function block_meet_dependencies() {
        global $CFG;

        if(!isset($CFG->block_lightboxgallery_random_avaible)) {
            $CFG->block_lightboxgallery_random_avaible = (
                $module = get_record("modules", "name", self::LIGHTBOXGALLERY_MODULE_NAME)
            ) ? (bool) $module->visible : false ;
        }

        if(
            !$CFG->block_lightboxgallery_random_avaible &&
            $block_id = get_field(
                'block', 'id', 'visible', '1', 'name', 
                strtolower(substr(__CLASS__, strpos(__CLASS__, '_') + 1))
            )
        ){
            set_field('block', 'visible', '0', 'id', $block_id);      
        } 

        return $CFG->block_lightboxgallery_random_avaible;
    }

    function user_can_edit(){
        return self::block_meet_dependencies();
    }

    function user_can_addto(&$page) {
        return self::block_meet_dependencies();
    }

    function instance_allow_config() {
        return true;
    }

    function instance_allow_multiple() {
        return true;
    }

}
?>
