rank();
paih();
function rank(){
	
	//最新
	$.post('/blog/selectnewlyAjax',{
		data:0
	},function(data){
		if(data.type==0){
			
		}else{
			
			var html = '';
			var data = data.data;
			
			var len = data.length;
			for(var i=0;i<len;i++){
				
				html += '<li><a href="/blog/new/id/'+data[i].id+'" title="'+data[i].abstract+'" target="_blank">'+data[i].abstract+'</a></li>';
				
			}
			//alert(html);
			$(".rank").html(html);
			
			
			
		}
	},'json');
}

function paih(){
	
	//最新
	$.post('/blog/selectmaxAjax',{
		data:0
	},function(data){
		if(data.type==0){
			
		}else{
			
			var html = '';
			var data = data.data;
			
			var len = data.length;
			for(var i=0;i<len;i++){
				
				html += '<li><a href="/blog/new/id/'+data[i].id+'" title="'+data[i].abstract+'" target="_blank">'+data[i].abstract+'</a></li>';
				
			}
			//alert(html);
			$(".paih").html(html);
			
			
			
		}
	},'json');
}


//function paih