/* dribbble */

var dribbble_load = false;

function open_dribbble(obj){
	url = $(obj).attr('href');
	src = $(obj).find('img').attr('src').replace('_teaser','');
	$('#dribbble_big li').html('<a href="' + url +'" class="thumbnail" target="_blank"><img src="' + src + '"/></a>');
	return false;
}

function get_dribbble(){
	if( !dribbble_load ){
		$('#loader').show();
		dribbble_load = true;
		$.ajax({
			dataType: 'jsonp',
			success: function(data){
				$('#loader').hide();
				if( data.shots.length ){
				    $.each(data.shots, function(i,item){

				      $('<li><a class="thumbnail quimby_search_image" href="http://drbl.in/' + item.id + '" onclick="return open_dribbble(this)"><img src="' + item.image_teaser_url + '"/></a></li>').appendTo("#dribbble_images");
				      if ( i == 4 ) return false;
				    });
					$('#dribbble_images A:first').click();
				}else{
					$('#dribbble').html('no se han encontrado imagenes');
				}
			},
			type: 'GET',
			url: 'http://api.dribbble.com/players/' + dribbble_user + '/shots'
		});
	}
}

/* flickr */

var flickr_load = false;
var flickr_api = '8d90a5ce3814eb72cd5a86da09530127';

function open_flickr(obj){
	url = $(obj).attr('href');
	src = $(obj).find('img').attr('src').replace('_s','');
	$('#flickr_big li').html('<a href="' + url +'" class="thumbnail" target="_blank"><img src="' + src + '"/></a>');
	return false;
}

function get_flickr(){
	if( !flickr_load ){
		$('#loader').show();
		flickr_load = true;
		$.ajax({
			dataType: 'json',
			success: function(data){
				$('#loader').hide();
				
				if( data.user ){
					user_id = data.user.id;
					
					$.ajax({
						dataType: 'json',
						success: function(data){

							if( data.photos.photo.length ){
							    $.each(data.photos.photo, function(i,item){

							      $('<li><a class="thumbnail quimby_search_image" href="http://www.flickr.com/photos/' + user_id + '/' + item.id + '" onclick="return open_flickr(this)"><img src="http://farm' + item.farm + '.static.flickr.com/' + item.server + '/' + item.id + '_' + item.secret + '_s.jpg"/></a></li>').appendTo("#flickr_images");

							    });
								$('#flickr_images A:first').click();
							}else{
								$('#flickr').html('no se han encontrado imagenes');
							}
						},
						type: 'GET',
						url: 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=' + flickr_api +  '&user_id=' + user_id + '&per_page=5&format=json&nojsoncallback=1'
					});
					
					data.user.id
					
					
					
				}else{
					$('#flickr').html('no se han encontrado imagenes');
				}
				
				
			},
			type: 'GET',
			url: 'http://api.flickr.com/services/rest/?method=flickr.urls.lookupUser&api_key=' + flickr_api + '&url=flickr.com%2Fphotos%2F' + flickr_user + '%2F&format=json&nojsoncallback=1'
		});
	}
}


/* github */

var github_load = false;
var github_langs = [];
var github_langs_values = {};
var github_langs_values_aux = [];
https://chart.googleapis.com/chart?cht=p&chd=t:10,20,30,40&chs=200x100&chl=January|February|March|April

function get_github(){
	if( !github_load ){
		$('#loader').show();
		github_load = true;
		$.ajax({
			//data: options,
			dataType: 'jsonp',
			success: function(data){
				$('#loader').hide();
				
				if( data.repositories.length ){
				    $.each(data.repositories, function(i,item){
					  
					  github_langs_values[item.language] = 1;


				      $('<li><a href="' + item.url + '" target="_blank">' + item.name + ' (' + item.language + ')</a><br/>' + item.description + '</li>').appendTo("#github_projects");
				      //if ( i == 4 ) return false;
				    });
					$('#github_images A:first').click();
					
					
					
					
					
					for( lang in github_langs_values ){
						github_langs.push( lang );
						github_langs_values_aux.push(github_langs_values[lang]);
					}
					
					//console.log(github_langs_values_aux);
					
					url = 'https://chart.googleapis.com/chart?cht=p&chd=t:' + github_langs_values_aux.join(',') + '&chs=300x150&chl=' + github_langs.join('|');
					$('#github_graph').html('<img src="' + url + '"/>');
					
				}else{
					$('#github').html('no se han encontrado proyectos');
				}
			},
			type: 'GET',
			url: 'https://github.com/api/v2/json/repos/show/' + github_user
		});
	}
}

/* youtube */


var youtube_load = false;

function open_youtube(id){
	$('#youtube_big').html('<iframe width="420" height="315" src="http://www.youtube.com/embed/' + id + '" frameborder="0" allowfullscreen></iframe>');
	return false;
}

function get_youtube(){
	if( !youtube_load ){
		$('#loader').show();
		youtube_load = true;
		$.ajax({
			//data: options,
			dataType: 'jsonp',
			success: function(data){
				$('#loader').hide();
				
				if( data.feed.entry ){
				    $.each(data.feed.entry, function(i,item){
					  
					  id = item.id.$t.split('/')[5];
					

				      $('<li><a rel="twipsy" data-original-title="' + item.title.$t + '" class="thumbnail quimby_search_image" href="' + item.link[0].href + '" onclick="return open_youtube(\'' + id + '\')"><img src="http://img.youtube.com/vi/' + id + '/1.jpg"/></a></li>').appendTo("#youtube_list");
				      if ( i == 4 ) return false;
				    });
					$('#youtube_list A:first').click();
					
					$('#youtube_list').twipsy()

					$("a[rel=twipsy]").twipsy({live: true});
					
					
				}else{
					$('#youtube').html('no se han encontrado videos');
				}
			},
			type: 'GET',
			url: 'http://gdata.youtube.com/feeds/users/' + youtube_user + '/uploads?alt=json'
		});
	}
}