<?php

namespace App\Extension;

class YoutubeEmbedExtension extends \Twig_Extension{

	public function getFilters(){
		return [
			new \Twig_SimpleFilter('youtubeembed', [$this, 'youtubeembedFilter']), 
		];
	}

	public function youtubeembedFilter($youtubelink){

		return preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>",$youtubelink);

		
	}
}
