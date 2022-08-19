<?php
class Data_Processor{
	
	public $dir_data;
	public $dir_name;
	public $dir_parent;
	public $working_dir;

	public function get_list($dir){
		$this->dir_parent = DIR_PATH. DIRECTORY_SEPARATOR .'template_library' ;
		$this->dir_name = $dir;
		
		$this->working_dir = $this->dir_parent. DIRECTORY_SEPARATOR . $this->dir_name;
		
		$this->dir_data = $this->get_dir_data();
	    
		return [
			'templates' => $this->templates_data(),
			'tags' => $this->tag_data(),
			'tags_type' => $this->categories_data(),
		];
	}
	
	
	public function get_api_data($id){
    	return is_readable(DIR_PATH. DIRECTORY_SEPARATOR .'advance_json'. DIRECTORY_SEPARATOR . $id . '.json') ? DIR_PATH. DIRECTORY_SEPARATOR .'advance_json'. DIRECTORY_SEPARATOR . $id . '.json' : '';
	}

	public function get_data($id){
		$id_decode = self::hash_id($id, 'decode');
		return is_readable(DIR_PATH. DIRECTORY_SEPARATOR .'template_library'. DIRECTORY_SEPARATOR . $id_decode. DIRECTORY_SEPARATOR . 'data-pro.json') ? DIR_PATH. DIRECTORY_SEPARATOR .'template_library'. DIRECTORY_SEPARATOR . $id_decode. DIRECTORY_SEPARATOR . 'data-pro.json' : DIR_PATH. DIRECTORY_SEPARATOR .'template_library'. DIRECTORY_SEPARATOR . $id_decode. DIRECTORY_SEPARATOR . 'data.json';
	}		
	public function templates_data(){
		$output = [];
		$liveUrl = '';
		foreach($this->dir_data as $key=>$value){
			
			foreach($value as $data){
			    $is_pro = is_readable(DIR_PATH. DIRECTORY_SEPARATOR .'template_library'. DIRECTORY_SEPARATOR . $this->dir_name. DIRECTORY_SEPARATOR .$key. DIRECTORY_SEPARATOR .$data.'/data-pro.json') ? true : false;
			    
			    $preview = is_readable( DIR_PATH. DIRECTORY_SEPARATOR .'template_library'. DIRECTORY_SEPARATOR . $this->dir_name. DIRECTORY_SEPARATOR .$key. DIRECTORY_SEPARATOR .$data.'/preview.jpg') ? DIR_URL.'/template_library/'.$this->dir_name.'/'.$key.'/'.$data.'/preview.jpg' : DIR_URL.'/template_library/'.$this->dir_name.'/'.$key.'/'.$data.'/preview.png';
			    
			    $thumbnail = is_readable( DIR_PATH. DIRECTORY_SEPARATOR .'template_library'. DIRECTORY_SEPARATOR . $this->dir_name. DIRECTORY_SEPARATOR .$key. DIRECTORY_SEPARATOR .$data.'/thumbnail.png' ) ? DIR_URL.'/template_library/'.$this->dir_name.'/'.$key.'/'.$data.'/thumbnail.png' : $preview;
			    
			    $liveUrl = ($is_pro) ? 'https://wordpress-theme.spider-themes.net/landpagy/' : 'https://wordpress-theme.spider-themes.net/landpagy/';
			    if($this->dir_name == 'page'){
			        $liveUrl .= 'templates/' . str_replace('_', '-', strtolower($data)) .'/';
			    } else {
                    echo '';
			        //$liveUrl .= str_replace('_', '-', strtolower($key)) .'/';
			    }
			    
			    
				$output[] = [
					'template_id' => self::hash_id($this->dir_name. DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $data), //md5 has of last 3 lvl directory names
					'title' => self::format($data), // 3rd level directory
					'tags' => [ // 2nd level directory
						0 => $key
					],
					'keywords' => [ // static
					   self::format($data), $key
					],
					'source' => 'spiderthemes-api', // static
					'is_pro' => $is_pro,
					'hasPageSettings' => '', // static
					'liveurl' => $liveUrl, // static
					'thumbnail' => $thumbnail, // preview.jpg
					'preview' => $preview, // preview.jpg
					'type' => ($this->dir_name == 'page') ? 'page' : 'section', // $tab value
					'author' => 'Arif Rahman', //static
					'modified' => '2022-08-15 15:32:17' //static
				];
			}
		}
		
		return $output;
	}
	
	public function categories_data(){
		
		$output = [];

		foreach($this->dir_data as $key=>$value){
			
			foreach($value as $data){
			    if( array_key_exists($data, $output) ){
			        continue;
			    }
				$output[$data] = self::format($data);
			}
		}
		
		return $output;
	}
	
	public function tag_data(){
	    $output = [];

		foreach($this->dir_data as $key=>$value){
			
			$output[$key] = self::format($key);
		}
		
		return $output;
	}

	public function categories_data_old(){
		
		$output[] = [
				'slug' => '', //static
				'title' => 'All', //  static
			  ];

		foreach($this->dir_data as $key=>$value){
			
			foreach($value as $data){
				$output[] = [
					'slug' => $data, //static
					'title' => self::format($data), //  static
				  ];
			}
		}
		
		return $output;
	}
		
	public function get_dir_data(){
			if(!file_exists($this->working_dir)){
				return false;
			}

			$iterator = new RecursiveIteratorIterator(
						new RecursiveDirectoryIterator($this->working_dir), 
					RecursiveIteratorIterator::SELF_FIRST);
			$results = [];	
			
			foreach($iterator as $file) {
				if($file->isDir()) {
					
					$getPath = trim( str_replace( $this->working_dir, '', $file->getRealpath() ) , DIRECTORY_SEPARATOR);

					if($this->dir_parent != $getPath){
						$exp_path = explode(DIRECTORY_SEPARATOR, $getPath);
						
						if(sizeof($exp_path) > 0){
							
							if(isset($exp_path[0]) && !empty($exp_path[0])){
								$key = $exp_path[0];
								if( $key == 'home'){
								    continue;
								}
								$value = isset($exp_path[1]) ? $exp_path[1] : '';
								if(array_key_exists($key, $results)){
									if(!empty($value) && !in_array($value, $results[$key]) ){
										$results[$key][] = $value;
									}
								}else{
									if(!empty($value)){
										$results[$key] = [$value];
									}
								}
							}
						}
					}
				}
			}
			return $results;
		}
		
	public static function format($str){
		return ucwords(str_replace(['__', '_'], ' ', $str));
	}
	
	public static function hash_id($data, $type = 'encode'){
		$output = '';
		switch($type){
			case 'encode':
				$output = base64_encode($data);
			break;
			
			case 'decode':
				$output = base64_decode($data);
			break;
		}
		return $output;
	}
}
