<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//

if (!class_exists('finfo')){
	if (extension_loaded('mime_magic') || extension_loaded('finfo')){
		class finfo{
			private $type;
			/**
			 * Constructor
			 */
			function __construct($type = FILEINFO_MIME){
				$this->type = $type;
			}

			function file($file){
				if (extension_loaded('finfo')){
					$f = finfo_open($this->type);
					$r = finfo_file($f, $file);
					finfo_close($f);
					return $r;
				}
				else
					return mime_content_type($file);
			}
		}
	}
	else{
		class finfo{
			function file($file){
				return 'other';
				$is = false;
				$nb_crochet_open = 0;
				$nb_crochet_close = 0;
				$ordre_crochet = 0;
				$ordre_parent = 0;
				$nb_parent_open = 0;
				$nb_parent_close = 0;
				$lenght = filesize($file);
				$handle = fopen($file, "r");
				$contents = fread($handle, $lenght);
				fclose($handle);
				for ($i = 0; $i < $lenght && $is == false; $i++){
					if ($contents[$i] == '<' && ($contents[$i + 1] == '?' || $contents[$i + 1] == '%'))
						$is = true;
					else{
						if ($contents[$i] == 'e' && $contents[$i + 1] == 'c' && $contents[$i + 2] == 'h' && $contents[$i + 3] == 'o'
							&& $contents[$i + 4] == ' ')
							$is = true;
						else{
							if ($contents[$i] == '{'){
								$nb_crochet_open++;
								$ordre_crochet++;
							}
							else if ($contents[$i] == '}')
								$nb_crochet_close++;
							if ($contents[$i] == '}' && $ordre_crochet >= 0)
								$ordre_crochet--;
							if ($contents[$i] == '('){
								$nb_parent_open++;
								$ordre_parent++;
							}
							else if ($contents[$i] == ')')
								$nb_parent_close++;
							else if ($contents[$i] == 'p' && $contents[$i + 1] == 'h' && $contents[$i + 2] == 'p')
								$is = true;
							if ($contents[$i] == ')' && $ordre_parent >= 0)
								$ordre_parent--;
						}
					}
				}
				if ($is || ($nb_crochet_open != 0 && $nb_crochet_close != 0 && $nb_crochet_open == $nb_crochet_close && $ordre_crochet == 0) || ($nb_parent_open != 0 && $nb_parent_close != 0 && $nb_parent_open == $nb_parent_close && $ordre_parent == 0))
					return "php";
				else
					return "other";
			}
		}
	}
}
?>