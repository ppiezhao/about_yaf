<?php
/**
 * @name PageModel
 * @desc page下标显示
 * @author work
 */
class PageModel {
    public function __construct() {
    }   
    //当前页$start  每页显示的条数 $offect 总页数$totle
	//每页显示5个数字按钮 
    public function getPage($start,$offect,$totle) {
		if($totle <=1){
			$page = '<a title="Total record"><b>'.$totle.'</b></a>';
			$page .='<b class="now">'.$start.'</b>';
			return $page;
		}
		//第一页
		$biao = (int)(($start-1)/5);
		$i=$biao*5+1;
		$len = ($biao+1)*5+1;
		
		//最后一页
		$lastye = $totle%$offect;
		$last = (int)($totle/$offect);
		//echo "<br/>";
		if($lastye){
			$last++;
		}
		//echo $last;
		$page = '<a title="Total record"><b>'.$totle.'</b></a>';
		if($start == 1){
			
			for(;$i<$len;$i++){
				if($i == $start){
					$page .='<b class="now">'.$start.'</b>';
				}else if($i > $last){
					break;
				}else{
					$page .= '<a  class="midden">'.$i.'</a>';
				}
			}
			$page .='<a  class="lastone">></a>';
			$page .='<a  class="endlast" for="'.$last.'">>></a>';
			
			
		}else if($start == $last){
			
			$page .= '<a href="#" class="preall"><<';
			$page .= '</a><a  class="preone"><</a>';
			for(;$i<$len;$i++){
				if($i == $start){
					$page .='<b class="now">'.$start.'</b>';
				}else if($i > $last){
					break;
				}else{
					$page .= '<a  class="midden">'.$i.'</a>';
				}
			}
		}else{
			$page .= '<a class="preall"><<';
			$page .= '</a><a class="preone"><</a>';
			for(;$i<$len;$i++){
				if($i == $start){
					$page .='<b class="now">'.$start.'</b>';
				}else if($i > $last){
					break;
				}else{
					$page .= '<a  class="midden">'.$i.'</a>';
				}
			}
			$page .='<a  class="lastone">></a>';
			$page .='<a  class="endlast" for="'.$last.'">>></a>';
			
		}
		
		return $page;
        
		 

		
				/* //中间页
				$page = '<div class="page"><a title="Total record"><b>168</b> </a><a href="/newstalk/index.html"><<</a><a href="/newstalk/index_4.html"><</a><a href="/newstalk/index_3.html">3</a><a href="/newstalk/index_4.html">4</a><b>5</b><a href="/newstalk/index_6.html">6</a><a href="/newstalk/index_7.html">7</a><a href="/newstalk/index_6.html">></a><a href="/newstalk/index_7.html">>></a></div> 
		</div>'
				//最后页
				$page = '<div class="page"><a title="Total record"><b>168</b> </a><a href="/newstalk/index.html"><<</a><a href="/newstalk/index_6.html"><</a><a href="/newstalk/index_5.html">5</a><a href="/newstalk/index_6.html">6</a><b>7</b></div> 
		</div>' */
    }

    public function insertSample($arrInfo) {
        return true;
    }
}
