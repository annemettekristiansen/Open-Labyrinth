<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_elements table in database 
 */
class Model_Leap_Map_Element extends DB_ORM_Model {
    private $mimes = array();
    
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'mime' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'path' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'args' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'width' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'height' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'h_align' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'v_align' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'width_type' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'height_type' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
        );
        
        $this->mimes[] = 'image/gif';
        $this->mimes[] = 'image/jpg';
        $this->mimes[] = 'image/jpeg';
        $this->mimes[] = 'image/png';
        $this->mimes[] = 'application/vnd.open';
        $this->mimes[] = 'application/x-shockw';  
        $this->mimes[] = 'application/x-shockwave-flash'; 
        $this->mimes[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $this->mimes[] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_elements';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getImagesByMap($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId)
                ->where('mime', 'IN', array('image/gif', 'image/jpg', 'image/jpeg'));
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }
    
    public function getAllMediaFiles($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId)
                ->where('mime', 'IN', array('image/gif', 'image/jpg', 'image/jpeg', 'application/x-shockwave-flash'));
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }
    
    public function getAllMediaFilesNotInIds($ids) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('id', 'NOT IN', $ids, 'AND')
                ->where('mime', 'IN', array('image/gif', 'image/jpg', 'image/jpeg', 'application/x-shockwave-flash'));
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }
    
    public function getAllFilesByMap($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId);
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }
    
    public function uploadFile($mapId, $values) {
        if($values['filename']['size'] < 1024 * 3 * 1024) {
            if(is_uploaded_file($values['filename']['tmp_name'])) {
                move_uploaded_file($values['filename']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/files/'.$values['filename']['name']);
                $fileName = 'files/'.$values['filename']['name'];
                
                $mime = File::mime($fileName);
                
                if(in_array($mime, $this->mimes)) {
                    $this->map_id = $mapId;
                    $this->path = $fileName;
                    $this->mime = File::mime($fileName);
                    $this->name = $values['filename']['name'];

                    $this->save();
                } else {
                    unlink($_SERVER['DOCUMENT_ROOT'].'/'.$fileName);
                }
            }
        }
    }
    
    public function deleteFile($fileId) {
        $this->id = $fileId;
        $this->load();
        
        unlink($_SERVER['DOCUMENT_ROOT'].'/'.$this->path);
        
        $this->delete();
    }
    
    public function getFilesSize() { 
        $totalsize = 0; 
        $totalcount = 0; 
        $dircount = 0; 
        $path = $_SERVER['DOCUMENT_ROOT'].'/files/';
        if ($handle = opendir($path)) { 
            while (false !== ($file = readdir($handle))) { 
                $nextpath = $path . '/' . $file; 
                if ($file != '.' && $file != '..' && !is_link ($nextpath)) { 
                    if (is_dir ($nextpath)) { 
                        $dircount++; 
                        $result = $this->getFilesSize($nextpath); 
                        $totalsize += $result['size']; 
                        $totalcount += $result['count']; 
                        $dircount += $result['dircount']; 
                    } else if (is_file ($nextpath)) { 
                        $totalsize += filesize ($nextpath); 
                        $totalcount++; 
                    } 
                } 
            } 
        } 
        closedir ($handle); 
        $total['size'] = $totalsize; 
        $total['count'] = $totalcount; 
        $total['dircount'] = $dircount; 
        return $total; 
    } 

    public function sizeFormat($size) 
    { 
        if($size<1024) 
        { 
            return $size." bytes"; 
        } 
        else if($size<(1024*1024)) 
        { 
            $size=round($size/1024,1); 
            return $size." KB"; 
        } 
        else if($size<(1024*1024*1024)) 
        { 
            $size=round($size/(1024*1024),1); 
            return $size." MB"; 
        } 
        else 
        { 
            $size=round($size/(1024*1024*1024),1); 
            return $size." GB"; 
        } 
    } 
    
    public function updateFile($fileId, $values) {
        $this->id = $fileId;
        $this->load();
        
        $this->mime = Arr::get($values, 'mrelmime', $this->mime);
        $this->name = Arr::get($values, 'mrelname', $this->name);
        $this->width = Arr::get($values, 'w', $this->width);
        $this->height = Arr::get($values, 'h', $this->height);
        $this->h_align = Arr::get($values, 'a', $this->h_align);
        $this->v_align = Arr::get($values, 'v', $this->v_align);
        $this->width_type = Arr::get($values, 'wv', $this->width_type);
        $this->height_type = Arr::get($values, 'hv', $this->height_type);
        
        $this->save();
    }
}

?>