<?php
// Redimensionnement
// Proposé par cknight le 19/08/2005
// http://www.asp-php.net/ressources/bouts_de_code.aspx?id=465
if (!defined("INDEX_CHECK")){
exit('You can\'t run this file alone.');
}

// Classe principale
class Img{
   // Propiétés de l'objet
   var $name; // nom de l'image source
   var $org;  // dossier d'origine
   var $ext;  // extension de l'image
   var $error;// Varaible pour les erreurs
   var $dest; // dossier de destination
   var $lpoint; // position du dernier point

   /* constructeur
   * retourne void (rien)
   */
   function doImg($name, $org, $dest){
      $this->name = (string) $name;
      $this->org = (string) $org;
      $this->dest = (string) $dest;
      $this->lpoint = $this->LastPoint();
      $this->ext = $this->GetExtension();
   }

   /* Récupère la position du dernier point
   * retourne (int)
   */
   function LastPoint(){
      return strrpos($this->name, '.');
   }

   /* Récupère l'extension du fichier
   * retourne (string)
   */
   function GetExtension(){
      return substr($this->name, $this->lpoint);
   }

   /* Retourne l'erreur si il en a une
   * retourne (string)
   */
   function Error(){
      if(!empty($this->error)){
         return $this->error();
      }
   }
}

// Classe Fille
class thb extends Img{
   // Propiétés de l'objet
   var $suffix; // suffix à ajouter à l'image
   var $thb_name; // nom complet de l'image réduite
   var $size; // taille pour réduction
   var $quality; // quamité de l'image réduite
   var $name; // nom de l'image source
   var $org; // dossier d'origine de l'image
   var $dest; // dossier de destination

   /* constructeur
   * (string) nom
   * (string) $dossier d'origine
   * (string) dossier de destination
   * retourne void (rien)
   */
   function doImg($name, $org, $dest){
      $this->name = (string) $name;
      $this->dest = (string) $dest;
      $this->org = (string) $org;
      $this->lpoint = Img::LastPoint();
      $this->ext = Img::GetExtension();
   }

   /* Ajout des paramètres
   * (string) suffix de l'image
   * (int) taille (px)
   * (int) qualité (%)
   * retourne void (rien)
   */
   function SetParam($suf,$size, $quality){
      $this->suffix = (string) $suf;
      $this->size = (int) $size;
      $this->quality = (int) $quality;
   }

   /* Ajout des paramètres séparament
   * (int) taille ($x)
   * retourne void (rien)
   */
   function SetSize($size){
      $this->size = (int) $size;
   }

   /*
   * (int) Qualité (%)
   * retourne void (rien)
   */
   function SetQuality($quality){
      $this->quality = (int) $quality;
   }

   /*
   * (string) suffix
   * retourne void (rien)
   */
   function SetSuffix($suf){
      $this->suffix = (string) $suf;
   }

   /*
   * récupère le nom +chemin de l'image résultante
   * retourne un (string)
   */
   function GetThbName(){
      $thb = substr($this->name, 0, Img::LastPoint());
      $thb.= $this->suffix.$this->ext;
      return $this->thb_name = $this->dest.$thb;
   }

   /* Lance le redimenssionnement
   * retourne un (bool)
   */
   function doThb(){
      if($this->Resize()){
         return true;
      }
      else{
         return false;
      }
   }

   /* Récupère le nom + le chemin de l'image source
   * retourne void (rien)
   */
   function GetOrigine(){
      return $this->org.$this->name;
   }

   /*
   * Fonctions privées
   */

   /* Fonction de redimensionnement
   * * retourne un (bool)
   */
   function Resize(){
      $source = $this->org.$this->name;
      $destination = $this->GetThbName();

      if (!file_exists($source)){
         $this->error = 'Erreur : Le Fichier n\'existe pas !';
      }
      if(!function_exists("Imagecreatefromjpeg")){
         $this->error = 'Erreur : La Librairie GD n\'est pas instal&eacute;e !';
      }

      switch($this->ext){
         case '.jpg':
         case '.jpeg':
         case '.JPG':
            $src_img=imagecreatefromjpeg($source);
            break;
         case '.png':
            $src_img=imagecreatefrompng($source);
            break;
         case '.gif':
            $src_img=imagecreatefromgif($source);
            break;
         default:
            $this->error = 'Erreur: Extension non autoris&eacute;e';
            break;
      }

      if(!$src_img){
         $this->error = 'Erreur : Lecture impossible de l\'image '.$source.' !';
      }

      //Taille de l'image originale
      $w = imagesx($src_img);
      $h = imagesy($src_img);

      //Récupère les proportions
      if($w<$h){
         $p = $w / $h;
         $height = $this->size;
         $width = $p * $height;
      }
      else{
         $p = $h / $w;
         $width = $this->size;
         $height = $p * $width;
      }

      $dst_img = ImageCreateTrueColor($width, $height);
      if(!$dst_img){
           $this->error = 'Erreur : Buffer non cr&eacute;&eacute; : '.$dst_img;
       }

      imagecopyresampled($dst_img,$src_img,0,0,0,0,$width,$height,$w,$h);

       if(imagejpeg($dst_img,$destination,$this->quality)){
          return true;
       }
       else{
          return false;
       }
   }
}

/*
* Utilisation: redimenssioner sans écraser la source.
//
// instanciation de l'objet
$thb = new thb;
// appel du constructeur
// nim de la source: image.jpeg
// chemin source: ./ (dossier courant)
// destianation: ./ (dossier courant)
$thb->doImg('image.jpg', './', './');
// Config des parametres
// prefix: _thb
// taille du + grd coté 250px
// qualité 100%
$thb->SetParam('_thb', 100, 100);
// pour connaitre le nom et chemin de l'image réduite
// résultat: ./image_thb.jpeg
$thumb = $thb->GetThbName();
// pour connaitre le nom et chemin de l'image d'origine
// résultat: ./image.jpeg
$source = $thb->GetOrigine();
// Lance le redimensionenemt
$thb->doThb();
*/
?>