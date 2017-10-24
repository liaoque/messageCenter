<?php 
if(!defined('IN_BOOT')) {
	exit('Access Denied');
}
/**
 * 分页类
 *
 * @author wanxiaokuo <wanxiaokuo.1985@163.com>
 * @copyright 2006-2010 leshu.com
 *
 */

class Page  {
	public function __construct() {
	 	
	}
	var $count = 0;
	var $limit = 0;
	/**
	 * 单例模式
	 *
	 * @var object
	 */
	static private $instance = NULL;
	static function getInstance() {
		if(self::$instance == NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 分页验证
	 *
	 * @param integer $page
	 * @param integer $pagesize
	 * @param integer $postion
	 * @param integer $allpage
	 * @param integer $count
	 */
	public function setPageSize(&$page, $pagesize, &$postion, &$allpage, $count) {
		if (intval($page) <= 0) {
			$page = 1;
		}
		$allpage = ceil($count/$pagesize);
		if($allpage <= 0){
			$allpage = 1;
		}
		if ($page > $allpage) {
			$page = $allpage;
		}
		$postion = ($page-1)*$pagesize;
        $this->count = $count ? $count : 0;
        $this->limit = $pagesize;
	}
	
	/**
	 * php 分页形式
	 *
	 * @param integer $page
	 * @param integer $pagecount
	 * @param string $url
	 * @param array $param
	 * @return string
	 */
	public function getPageRe($page, $pagecount, $url, $param) {
		
		$str = '';
		if($page >= 1) {
			if(is_array($param)) {
				foreach ($param as $key=>$value) {
					$url .= "&".$key."=".urlencode($value);
				}
			}
			//每行展示rowcount个
			$rowcount = 5;
			$row = ceil($pagecount/$rowcount);
			$nowrow = intval($page/$rowcount);
			if($nowrow <= 1){//小于等于1
				if($nowrow == 1) {
					if(intval($page%$rowcount) > 0){
						$nowrow = 2;
					}else{
						$nowrow = 1;
					}
				}else{
					$nowrow = 1;
				}
			} else {
				if(intval($page%$rowcount) != 0){
					$nowrow = $nowrow + 1;
				}
			}

			$postion = ($nowrow-1)*$rowcount+1;
			$end = intval($postion+$rowcount);
			if($end > $pagecount) {
				$end = $pagecount+1;
			}
			$str .= '<p class="fl" style="display: block;margin-top: 10px;color:#999">共 ' . $this->count . ' 条信息  每页显示 ' . $this->limit . ' 条</p><div class="pagination"> <ul>';
			
			$str .= '<li><a href="'.$url.'&page=1" style="color:#999">首页</a></li><li><a href="'.$url.'&page='.($page-1).'" style="color:#999">上一页</a></li>';
				
			if($nowrow >= 2){
				//$str .= ' <span><a href="'.$url.'&page=1">1...</a></span>';
			}
			for($i=$postion; $i<$end; $i++) {
				if($i==$page){
					$str .= '<li class="current" ><a  style="display:inline-block; width:auto; height:24px;" href="javascript:void(0);" >'.$i.'</a></li>';
				}else {
					$str .= '<li><a  style="display:inline-block; width:auto; height:24px;" href="'.$url.'&page='.$i.'" style="color:#999">'.$i.'</a></li>';
				}
			}
			
			if($row > 1 && $row != $nowrow){
				//$str .= '<span><a href="'.$url.'&page='.($pagecount-1).'">...'.($pagecount-1).'</a></span><span><a href="'.$url.'&page='.$pagecount.'">'.$pagecount.'</a></span> ';
			}
			$str .= '<li><a href="'.$url.'&page='.($page+1).'" style="color:#999">下一页</a></li><li><a href="'.$url.'&page='.$pagecount.'" style="color:#999">尾页</a></li>';
			
			$str .= '</ul></div>';
		}
		return $str;
	}

	/**
	 * php 分页形式（新）
	 *
	 * @param integer $page
	 * @param integer $pagecount
	 * @param string $url
	 * @param array $param
	 * @return string
	 */
	public function getPageReNew($page, $pagecount, $url, $param){

		$str = '';
		if($page >= 1) {
			if(is_array($param)) {
				foreach ($param as $key=>$value) {
					$url .= "&".$key."=".urlencode($value);
				}
			}
			//每行展示rowcount个
			$rowcount = 5;
			$row = ceil($pagecount/$rowcount);
			$nowrow = intval($page/$rowcount);
			if($nowrow <= 1){//小于等于1
				if($nowrow == 1) {
					if(intval($page%$rowcount) > 0){
						$nowrow = 2;
					}else{
						$nowrow = 1;
					}
				}else{
					$nowrow = 1;
				}
			} else {
				if(intval($page%$rowcount) != 0){
					$nowrow = $nowrow + 1;
				}
			}

			$postion = ($nowrow-1)*$rowcount+1;
			$end = intval($postion+$rowcount);
			if($end > $pagecount) {
				$end = $pagecount+1;
			}

			$str="<div class='row'><div class='col-sm-5'><div class='dataTables_info'>共 " . $this->count . " 条信息  每页显示 " . $this->limit . " 条</div></div>";


			$str.="<div class='col-sm-7'> <div class='dataTables_paginate paging_simple_numbers'><ul class='pagination'>";


			$str .= '<li class="paginate_button previous"><a href="'.$url.'&page=1" style="color:#999">首页</a></li><li><a href="'.$url.'&page='.($page-1).'" style="color:#999">上一页</a></li>';

			for($i=$postion; $i<$end; $i++) {
				if($i==$page){
					$str .= '<li class="paginate_button active" ><a  style="display:inline-block; width:auto; height:32px;" href="javascript:void(0);" >'.$i.'</a></li>';
				}else {
					$str .= '<li class="paginate_button"><a  style="display:inline-block; width:auto; height:32px;" href="'.$url.'&page='.$i.'" style="color:#999">'.$i.'</a></li>';
				}
			}

			if($row > 1 && $row != $nowrow){
			}
			$str .= '<li class="paginate_button"><a href="'.$url.'&page='.($page+1).'" style="color:#999">下一页</a></li><li><a href="'.$url.'&page='.$pagecount.'" style="color:#999">尾页</a></li>';

			$str .= '</ul></div></div>';
		}
		return $str;



	}



	public function getPagePlate($page, $pagecount, $url, $param) {
		
		$str = '';
		if($page >= 1) {
			if(is_array($param)) {
				foreach ($param as $key=>$value) {
					$url .= "/".$key."-".urlencode($value);
				}
			}
			//每行展示rowcount个
			$rowcount = 5;
			$row = ceil($pagecount/$rowcount);
			$nowrow = intval($page/$rowcount);
			if($nowrow <= 1){//小于等于1
				if($nowrow == 1) {
					if(intval($page%$rowcount) > 0){
						$nowrow = 2;
					}else{
						$nowrow = 1;
					}
				}else{
					$nowrow = 1;
				}
			} else {
				if(intval($page%$rowcount) != 0){
					$nowrow = $nowrow + 1;
				}
			}

			$postion = ($nowrow-1)*$rowcount+1;
			$end = intval($postion+$rowcount);
			if($end > $pagecount) {
				$end = $pagecount+1;
			}

             /*<ul class="pagination clearfix fr">
        
          <li class="first">&lt;</li>
          <li class="current">1</li>
          <li>2</li>
          <li>3</li>
          <li>4</li>
          <li class="b0">...</li>
          <li>11</li>
          <li>12</li>
          <li class="last">&gt;</li> 
            </ul> */


			//$str .= '<p class="fl" style="display: block;margin-top: 10px;color:#999">共 ' . $this->count . ' 条信息  每页显示 ' . $this->limit . ' 条</p><div class="pagination"> <ul>';
			
			$str .= '<ul class="pagination clearfix fr"><li class="first"><a style="display:inline-block; width:28px; height:22px;" href="'.$url.'/page-'.($page-1).'">&lt;</a></li>';
			if($nowrow >= 2){
				//$str .= ' <span><a href="'.$url.'&page=1">1...</a></span>';
			}
			for($i=$postion; $i<$end; $i++) {
				if($i==$page){
					
					$str .= '<li class="current">'.$i.'</li>';
					
				}else {
					$str .= '<li><a style="display:inline-block; width:auto; height:22px;"  href="'.$url.'/page-'.$i.'" style="color:#999">'.$i.'</a></li>';
					
				}
			}
			
			if($row > 1 && $row != $nowrow){
				//$str .= '<span><a href="'.$url.'&page='.($pagecount-1).'">...'.($pagecount-1).'</a></span><span><a href="'.$url.'&page='.$pagecount.'">'.$pagecount.'</a></span> ';
			}
			$str .= '<li class="last"><a style="display:inline-block; width:auto; height:22px;" href="'.$url.'/page-'.($page+1).'" style="color:#999">&gt;</a></li>';
$str .= '</ul>';
		}
		return $str;
	}
	
	
	public function getPageBack($page, $pagecount, $url, $param) {
		
		$str = '';
		if($page >= 1) {
			if(is_array($param)) {
				foreach ($param as $key=>$value) {
					$url .= "&".$key."=".urlencode($value);
				}
			}
			//每行展示rowcount个
			$rowcount = 5;
			$row = ceil($pagecount/$rowcount);
			$nowrow = intval($page/$rowcount);
			if($nowrow <= 1){//小于等于1
				if($nowrow == 1) {
					if(intval($page%$rowcount) > 0){
						$nowrow = 2;
					}else{
						$nowrow = 1;
					}
				}else{
					$nowrow = 1;
				}
			} else {
				if(intval($page%$rowcount) != 0){
					$nowrow = $nowrow + 1;
				}
			}

			$postion = ($nowrow-1)*$rowcount+1;
			$end = intval($postion+$rowcount);
			if($end > $pagecount) {
				$end = $pagecount+1;
			}
			$str .= '<div class="pagination"> <ul>';
			
			$str .= '<li><a href="'.$url.'&page=1" style="color:#999">首页</a></li><li><a href="'.$url.'&page='.($page-1).'" style="color:#999">上一页</a></li>';
				
			if($nowrow >= 2){
				//$str .= ' <span><a href="'.$url.'&page=1">1...</a></span>';
			}
			for($i=$postion; $i<$end; $i++) {
				if($i==$page){
					$str .= '<li class="current"><a href="javascript:void(0);" >'.$i.'</a></li>';
				}else {
					$str .= '<li><a href="'.$url.'&page='.$i.'" style="color:#999">'.$i.'</a></li>';
				}
			}
			
			if($row > 1 && $row != $nowrow){
				//$str .= '<span><a href="'.$url.'&page='.($pagecount-1).'">...'.($pagecount-1).'</a></span><span><a href="'.$url.'&page='.$pagecount.'">'.$pagecount.'</a></span> ';
			}
			$str .= '<li><a href="'.$url.'&page='.($page+1).'" style="color:#999">下一页</a></li><li><a href="'.$url.'&page='.$pagecount.'" style="color:#999">尾页</a></li>';
			
			$str .= '</ul></div>';
		}
		return $str;
	}
	
	public function getPageGame($page, $pagecount, $url, $param) {
		
		$str = '';
		if($page >= 1) {
			if(is_array($param)) {
				foreach ($param as $key=>$value) {
					$url .= "/".$key."-".urlencode($value);
				}
			}
			//每行展示rowcount个
			$rowcount = 5;
			$row = ceil($pagecount/$rowcount);
			$nowrow = intval($page/$rowcount);
			if($nowrow <= 1){//小于等于1
				if($nowrow == 1) {
					if(intval($page%$rowcount) > 0){
						$nowrow = 2;
					}else{
						$nowrow = 1;
					}
				}else{
					$nowrow = 1;
				}
			} else {
				if(intval($page%$rowcount) != 0){
					$nowrow = $nowrow + 1;
				}
			}

			$postion = ($nowrow-1)*$rowcount+1;
			$end = intval($postion+$rowcount);
			if($end > $pagecount) {
				$end = $pagecount+1;
			}
			
			$str .= '<ul class="pagination fr clearfix">';
			
			$str .= '<li class="first li-disab"><a href="'.$url.'/page-'.($page-1).'">&lt;&lt;上一页</a></li>';
			
				
			if($nowrow >= 2){
				//$str .= ' <li><a href="'.$url.'&page=1">1...</a></li>';
			}
			for($i=$postion; $i<$end; $i++) {
				if($i==$page){
					$str .= '<li class="cur"><a href="javascript:void(0);" >'.$i.'</a></li>';
					
				}else {
					$str .= '<li><a href="'.$url.'/page-'.$i.'" style="color:#999">'.$i.'</a></li>';
				}
			}
			
			if($row > 1 && $row != $nowrow){
				$str .= '<li class="b0">...</li><li ><a href="'.$url.'/page-'.$pagecount.'">'.$pagecount.'</a></li>';
			}
			$str .= '<li class="last"><a href="'.$url.'/page-'.($page+1).'" >下一页&gt;&gt;</a></li>';
			$str .= '</ul>';
		}
		return $str;
	}

	public function getWebPhonePage($page, $pagecount, $url, $param) {
		
		$str = '';
		if($page >= 1) {
			if(is_array($param)) {
				foreach ($param as $key=>$value) {
					$url .= "/".$key."-".urlencode($value);
				}
			}
			//每行展示rowcount个
			$rowcount = 5;
			$row = ceil($pagecount/$rowcount);
			$nowrow = intval($page/$rowcount);
			if($nowrow <= 1){//小于等于1
				if($nowrow == 1) {
					if(intval($page%$rowcount) > 0){
						$nowrow = 2;
					}else{
						$nowrow = 1;
					}
				}else{
					$nowrow = 1;
				}
			} else {
				if(intval($page%$rowcount) != 0){
					$nowrow = $nowrow + 1;
				}
			}

			$postion = ($nowrow-1)*$rowcount+1;
			$end = intval($postion+$rowcount);
			if($end > $pagecount) {
				$end = $pagecount+1;
			}
			
			$str .= '<div class="pagination-wrap">
						<div class="pagination">
							<ul class="pagination-ul clearfix">';
			
			//$str .= '<li class="first li-disab"><a href="'.$url.'/page-'.($page-1).'">&lt;&lt;上一页</a></li>';
			$str .=	'<li ><a href="'.$url.'/page-'.($page-1).'">&lt;&lt;上一页</a></li>';
			if($nowrow >= 2){
				//$str .= ' <li><a href="'.$url.'&page=1">1...</a></li>';
			}
			for($i=$postion; $i<$end; $i++) {
				if($i==$page){
					//$str .= '<li class="cur"><a href="javascript:void(0);" >'.$i.'</a></li>';
					$str .='<li class="cur"><a href="javascript:;">'.$i.'</a></li>';
				}else {
				  //$str .= '<li><a href="'.$url.'/page-'.$i.'" style="color:#999">'.$i.'</a></li>';
					$str .='<li><a href="'.$url.'/page-'.$i.'">'.$i.'</a></li>';
				}
			}
			
			if($row > 1 && $row != $nowrow){
				//$str .= '<li class="b0">...</li><li ><a href="'.$url.'/page-'.$pagecount.'">'.$pagecount.'</a></li>';
				$str .='<li><a href="'.$url.'/page-'.$i.'">'.$i.'</a></li>';
			}
			//$str .= '<li class="last"><a href="'.$url.'/page-'.($page+1).'" >下一页&gt;&gt;</a></li>';
			$str .='<li><a href="'.$url.'/page-'.($page+1).'">下一页&gt;&gt;</a></li>';
			$str .=  '</ul>
					</div>
				</div>';
		}
		return $str;
	}
}

?>