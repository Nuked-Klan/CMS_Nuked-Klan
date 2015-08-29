<?php

$data = $this->get('data');

$data['image'] = preg_replace('#<a.*><img.*src="(.*?)".*?></a>#', '$1', $data['image']);
$data['date'] = str_replace('/', '.', $data['date']);

$this->assign('newsImage', $data['image']);
$this->assign('newsTitle', $data['titre']);
$this->assign('newsNbComments', $data['nb_comment']);
$this->assign('newsText', $data['texte']);
$this->assign('newsAuthor', $data['auteur']);
$this->assign('newsDate', $data['date']);
$this->assign('newsLink', 'index.php?file=News&op=index_comment&news_id='.$data['id']);
$this->assign('newsCatLink', 'index.php?file=News&op=categorie&cat_id='.$data['catid']);
$this->assign('newsCatName', $data['cat']);