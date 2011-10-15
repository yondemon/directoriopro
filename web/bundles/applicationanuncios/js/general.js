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
					  
					  if( github_langs_values[item.language] ){
						github_langs_values[item.language]++;
					  }else{
						github_langs_values[item.language] = 1;
					  }



				      $('<li><a href="' + item.url + '" target="_blank">' + item.name + ' (' + item.language + ')</a><br/>' + item.description + '</li>').appendTo("#github_projects");

				    });
					
					
					
					for( lang in github_langs_values ){
						github_langs.push( lang );
						github_langs_values_aux.push(github_langs_values[lang]);
					}
					
					
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

				}else{
					$('#youtube').html('no se han encontrado videos');
				}
			},
			type: 'GET',
			url: 'http://gdata.youtube.com/feeds/users/' + youtube_user + '/uploads?alt=json'
		});
	}
}


/* twitter */

var twitter_load = false;

function get_twitter(){
	if( !twitter_load ){
		$('#loader').show();
		twitter_load = true;
		$.ajax({
			dataType: 'jsonp',
			success: function(data){
				$('#loader').hide();
				
				$('#twitter').html("Una forma de saber que hace una persona es ver en que listas lo han añadido en twitter<br/><br/>");

				if( data.lists.length ){
				    $.each(data.lists, function(i,item){
					
				      $('<a href="http://twitter.com' + item.uri + '" target="_blank"><b>' + item.name + '</b>@' + item.user.screen_name + '</a>').appendTo("#twitter");

				    });

					$('<a href="http://twitter.com/' + twitter_user + '/lists/memberships" target="_blank"><b>Ver más listas</b></a>').appendTo("#twitter");

					
				}else{
					$('#twitter').html('todavía no ha sido añadido en una lista');
				}
			},
			type: 'GET',
			url: 'https://api.twitter.com/1/lists/memberships.json?screen_name=' + twitter_user
		});
	}
}




/* stackoverflow */

var stackoverflow_load = false;
var stackoverflow_html = '';
var stackoverflow_reputation = false;
var stackoverflow_tags = [];
var stackoverflow_error = false;

function get_stackoverflow(){
	if( !stackoverflow_load ){
		$('#loader').show();
		stackoverflow_load = true;

		$.ajax({
			dataType: 'jsonp',
			jsonp: 'jsonp',
			success: function(data){
				
				
				if( data.error ){
					stackoverflow_error = true;
					$('#stackoverflow').html('el usuario no existe');
					return false;
				}
				
				stackoverflow_html += '<b>Ha preguntado</b><br/>'
				if( data.questions.length ){
					stackoverflow_html += '<ul>';
				    $.each(data.questions, function(i,item){
					  
					  if( !stackoverflow_reputation ) stackoverflow_reputation = item.owner.reputation;
					
					  stackoverflow_tags = stackoverflow_tags.concat(item.tags);
					
				      stackoverflow_html += '<li><a href="http://stackoverflow.com' + item.question_answers_url + '" target="_blank">' + item.title.replace(/\</g,'&lt;').replace(/\>/g,'&gt;') + '</a></li>';
					  if( i == 4 ) return false;
				    });
					stackoverflow_html += '</ul>';
					
				}else{
					stackoverflow_html += 'todavía no ha hecho preguntas<br/><br/>';
				}				
				
			},
			type: 'GET',
			url: 'http://api.stackoverflow.com/1.1/users/' + stackoverflow_user + '/questions'
		});
		
		
		$.ajax({
			dataType: 'jsonp',
			jsonp: 'jsonp',
			success: function(data){

				if( !stackoverflow_error ){
					stackoverflow_html += '<b>Ha respondido</b><br/>'
					if( data.answers.length ){
						stackoverflow_html += '<ul>';
					    $.each(data.answers	, function(i,item){

						  if( !stackoverflow_reputation ) stackoverflow_reputation = item.owner.reputation;

					      stackoverflow_html += '<li><a href="http://stackoverflow.com' + item.answer_comments_url + '" target="_blank">' + item.title.replace(/\</g,'&lt;').replace(/\>/g,'&gt;') + '</a></li>';
						  if( i == 4 ) return false;
					    });
						stackoverflow_html += '</ul>';

					}else{
						stackoverflow_html += 'todavía no ha respondido preguntas';
					}
				


					if( !stackoverflow_reputation ) stackoverflow_reputation = 1;
				
					stackoverflow_html_aux = '<b>Reputación</b><br/><span style="font-size:30px">' + stackoverflow_reputation + '</span><br/><br/>';

				
					if( stackoverflow_tags.length ){
						stackoverflow_html_aux += '<b>Tags</b><br/>' + stackoverflow_tags.join(', ') + '<br/><br/>';
					}

					$('#loader').hide();
					$('#stackoverflow').html(stackoverflow_html_aux+stackoverflow_html);
				}
			},
			type: 'GET',
			url: 'http://api.stackoverflow.com/1.1/users/' + stackoverflow_user + '/answers'
		});
		
	}
}


/* vimeo */


var vimeo_load = false;

function open_vimeo(id){
	$('#vimeo_big').html('<iframe src="http://player.vimeo.com/video/' + id + '?title=0&amp;byline=0&amp;portrait=0&amp;color=8cc63f" width="377" height="214" frameborder="0" webkitAllowFullScreen allowFullScreen></iframe>');
	return false;
}

function get_vimeo(){
	if( !vimeo_load ){
		$('#loader').show();
		vimeo_load = true;
		$.ajax({
			//data: options,
			dataType: 'jsonp',
			success: function(data){
				$('#loader').hide();
				
				if( !data.error && data ){
				    $.each(data, function(i,item){
				      $('<li><a rel="twipsy" data-original-title="' + item.title + '" class="thumbnail quimby_search_image" href="' + item.url + '" onclick="return open_vimeo(\'' + item.id + '\')"><img src="' + item.thumbnail_small + '"/></a></li>').appendTo("#vimeo_list");
				      if ( i == 4 ) return false;
				    });
					$('#vimeo_list A:first').click();

				}else{
					$('#vimeo').html('no se han encontrado videos');
				}
			},
			type: 'GET',
			url: 'http://vimeo.com/api/v2/' + vimeo_user + '/videos.json'
		});
	}
}

/* user edit preview avatar */

var gravatar_id, twitter_id, facebook_id;

function userAvatar(type){
	var rand = Math.floor(Math.random()*11);
	switch( parseInt( type ) ){
		case 0: //gravatar
			url = 'http://www.gravatar.com/avatar/' + gravatar_id + '?s=50&d=http://dir.betabeers.com/bundles/applicationanuncios/images/default_avatar.png';
			break;
			
		case 1: // twitter
			url = 'http://api.twitter.com/1/users/profile_image/' + twitter_id + '.json?size=normal';
			break;
			
		case 2: // facebook
			url = 'http://graph.facebook.com/' + facebook_id + '/picture?type=square';
			break;
	
	}
	url += '&rand=' + rand;
	$('#user_avatar').attr('src', url);
}

/* tooltips user networks edit */

function networks_tooltip(){
	$('#networks input').each(function(index) {
		tooltip = false;
		id = $(this).attr('id');
		
		switch( true ){
			case ( id.indexOf('usertype_url') > -1 ):
				tooltip = 'http://tudominio.com';
				break;
			case ( id.indexOf('twitter') > -1 ):
				tooltip = 'http://twitter.com/USUARIO';
				break;		
			case ( id.indexOf('linkedin') > -1 ):
				tooltip = 'http://es.linkedin.com/in/USUARIO';
				break;
			case ( id.indexOf('forrst') > -1 ):
				tooltip = 'http://forrst.com/people/USUARIO';
				break;
			case ( id.indexOf('github') > -1 ):
				tooltip = 'http://github.com/USUARIO';
				break;
			case ( id.indexOf('dribbble') > -1 ):
				tooltip = 'http://dribbble.com/USUARIO';
				break;
			case ( id.indexOf('flickr') > -1 ):
				tooltip = 'http://flickr.com/photos/USUARIO';
				break;
			case ( id.indexOf('youtube') > -1 ):
				tooltip = 'http://www.youtube.com/user/USUARIO';
				break;
			case ( id.indexOf('stackoverflow') > -1 ):
				tooltip = 'http://stackoverflow.com/users/ID';
				break;
			case ( id.indexOf('vimeo') > -1 ):
				tooltip = 'http://vimeo.com/USUARIO';
				break;
			case ( id.indexOf('delicious') > -1 ):
				tooltip = 'http://delicious.com/USUARIO';
				break;
			case ( id.indexOf('pinboard') > -1 ):
				tooltip = 'http://pinboard.in/u:USUARIO';
				break;
			case ( id.indexOf('itunes') > -1 ):
				tooltip = 'http://itunes.apple.com/es/artist/USUARIO';
				break;
			case ( id.indexOf('android') > -1 ):
				tooltip = 'https://market.android.com/developer?pub=USUARIO';
				break;
		}
		
		if( tooltip ){
			$(this).attr('data-original-title',tooltip);
			$(this).attr('rel','twipsy');
		}
	});
}