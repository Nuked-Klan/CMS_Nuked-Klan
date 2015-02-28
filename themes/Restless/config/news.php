<?php

$data['image'] = preg_replace('#<a.*><img.*src="(.*?)".*?></a>#', '$1', $data['image']);
$data['date'] = str_replace('/', '.', $data['date']);

$this->newsContent = array(
                        'image' => $data['image'],
                        'title' => $data['titre'],
                        'nbComments' => $data['nb_comment'],
                        'texte' => $data['texte'],
                        'auteur' => $data['auteur'],
                        'date' => $data['date']
                    );