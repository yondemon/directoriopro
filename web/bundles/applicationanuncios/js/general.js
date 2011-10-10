var dribbble_load = false;

function open_dribbble(obj){
	src = $(obj).find('img').attr('src').replace('_teaser','');
	$('#dribbble_big').attr('src',src);
	return false;
}

function get_dribbble(){
	if( !dribbble_load ){
		dribbble_load = true;
		$.ajax({
			//data: options,
			dataType: 'jsonp',
			success: function(data){
			
				if( data.shots.length ){
				    $.each(data.shots, function(i,item){

				      $('<li><a class="thumbnail quimby_search_image" href="http://drbl.in/' + item.id + '" onclick="return open_dribbble(this)"><img src="' + item.image_teaser_url + '"/></a></li>').appendTo("#dribbble_images");
				      if ( i == 3 ) return false;
				    });
					$('#dribbble_images A:first').click();
				}else{
					$('#dribbble_images').html('no se han encontrado imagenes');
				}
			},
			type: 'GET',
			url: 'http://api.dribbble.com/players/' + dribbble_user + '/shots'
		});
	}
}