<?php

require __DIR__ . '/simple_html_dom.php';



$id = $_GET['id'];
$type = $_GET['type'];
$callback = isset( $_GET['callback'] ) ? $_GET['callback'] : false;
$data = array();


$urls = array(
	'chrome' => 'https://chrome.google.com/webstore/search?q=',
	'android' => 'https://market.android.com/developer?pub=',
	'masterbranch' => 'https://www.masterbranch.com/developer/',
	'itunes' => 'http://www.appannie.com/company/',
	'linkedin' => 'http://es.linkedin.com/in/'
);





$url = $urls[$type] . urlencode( $id );
$html = @file_get_html( $url );
if( $html ){


	switch( $type ){
		case 'chrome':
			$items = $html->find('a[class=title-a]');
			if( $items ){
				foreach( $items as $item ){
					$data[] = array(
						'url' => 'https://chrome.google.com' . $item->attr['href'],
						'title' => $item->find('div[class=mod-tiles-info] b',0)->innertext,
						'text' => $item->find('div[class=mod-tiles-category]',0)->innertext,
						'icon' => 'http:' . $item->find('img',0)->attr['src']
						);
				}
			}
			break;

		case 'android':
			$items = $html->find('li[class=goog-inline-block]');
			if( $items ){
				foreach( $items as $item ){
					$link = $item->find('a[class=title]',0);
					$data[] = array(
						'url' => 'https://market.android.com' . $link->attr['href'],
						'title' => $link->innertext,
						'text' => $item->find('p[class=snippet-content]',0)->innertext,
						'icon' => $item->find('img',0)->attr['src']
						);
				}
			}
			break;
		
		case 'masterbranch':
			$items = $html->find('div[id=project-list] h4');
			if( $items ){
				$score = $html->find('div[class=dev-score] div',0)->innertext;
				$beers = $html->find('div[class=beers] span',0)->innertext;
				foreach( $items as $item ){
					$projects[] = array(
						'url' => $item->find('a',0)->attr['href'],
						'title' => $item->find('img',0)->attr['title']
						);
				}
				$data = array(
					'score' => $score,
					'beers' => $beers,
					'beers_url' => $url . '/beers',
					'projects' => $projects
				);
			}
			break;

		case 'itunes':
			$items = $html->find('div[class=tracked_app]');
			if( $items ){
				foreach( $items as $item ){
					$link = $item->find('a',0);
					$data[] = array(
						'url' => 'http://www.appannie.com' . $link->attr['href'],
						'title' => $item->find('h3 a',0)->innertext,
						'icon' => $link->find('img',0)->attr['src']
						);
				}
			}
			break;
			
		case 'linkedin':
			$experience = $html->find('div[id=profile-experience]',0)->innertext;
			$education = $html->find('div[id=profile-education]',0)->innertext;
			
			if( $experience ) $experience = str_replace('href="/company','target="_blank" href="http://linkedin.com/company',$experience);
			
			die( $experience . $education );
			break;
	}
	
	
}



header('content-type: application/json; charset=utf-8');
$result = $callback . '(' . json_encode($data) . ')';

/*
echo '<pre>';
print_r($data);
echo '</pre>';
*/