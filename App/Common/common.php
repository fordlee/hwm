<?php
	function getTree($data, $pid){
		$tree = '';
		foreach($data as $k => $v)
		{
		   if($v['pid'] == $pid){         
		    //父亲找到儿子
		    $v['pid'] = getTree($data, $v['id']);
		    $tree[] = $v;
		   }
		}
		return $tree;
	}

	function getTreeHtml($data, $pid){
		$html = '';
		foreach($data as $k => $v){
		   if($v['pid'] == $pid){        
		    //父亲找到儿子
		    $html .= "<li><span class='folder'>".$v['dept_name']."</span>";
		    $html .= getTreeHtml($data, $v['id']);
		    $html = $html."</li>";
		   }
		}
		return $html ? '<ul>'.$html.'</ul>' : $html ;
	}

	

?>