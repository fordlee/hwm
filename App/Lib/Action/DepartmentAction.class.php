<?php
// 本类由系统自动生成，仅供测试用途
class DepartmentAction extends Action {

	public function __construct() {
        parent::__construct();
        $this->CheckmSession();
    }

    private function CheckmSession(){
        if(!isset($_SESSION['username'])&& !isset($_SESSION['login']) && $_SESSION['login'] !== true){
            $this -> error('抱歉!您还没有登录或登录超时，请重新登录！',U('Auth/login'));
        }
    }

    public function deptList(){
        $m_d = M('department');
        $dept = $m_d -> select();
        $tree = getTreeHtml($dept, 0);
        $this -> assign('tree',$tree);

        $department = $m_d -> where('pid=0') -> select();
        $this -> assign('department',$department);
        $this -> display();
    }

    public function deptAdd(){
    	if((isset($_POST['dept_name']) && !empty($_POST['dept_name'])) &&
    		(isset($_POST['dept_leader']) && !empty($_POST['dept_leader'])) &&
    		(isset($_POST['description']) && !empty($_POST['description']))){
    		$data['dept_name'] = $_POST['dept_name'];
    		$data['dept_leader'] = $_POST['dept_leader'];
    		$data['description'] = $_POST['description'];
            
    		$m_d = M('department');
    		$where = array(
    			"dept_name" => $_POST['dept_name'],
    			"dept_leader" => $_POST['dept_leader']
    		);
    		$ret = $m_d -> where($where) -> find();
    		if($ret == null){
    			$data['pid'] = 0;
    			$data['dept_date'] = date("Y-m-d");
    			$m_d -> add($data);
    			$this -> success('添加成功！','deptList');
    		}else{
    			$this -> error('您已添加,请勿重复操作！');
    		}

    	}else{
    		$this -> display();
    	}
    }

    public function deptEdit(){
    	if((isset($_POST['dept_name']) && !empty($_POST['dept_name'])) &&
    		(isset($_POST['dept_leader']) && !empty($_POST['dept_leader'])) &&
    		(isset($_POST['description']) && !empty($_POST['description']))){
    		$id = $_POST['id'];
    		$data['dept_name'] = $_POST['dept_name'];
    		$data['dept_leader'] = $_POST['dept_leader'];
    		$data['description'] = $_POST['description'];

    		$m_d = M('department');
    		$where = array(
    			"dept_name" => $_POST['dept_name'],
    			"dept_leader" => $_POST['dept_leader'],
    			"description" => $_POST['description']
    		);
    		$ret = $m_d -> where($where) -> find();
    		if($ret == null){
    			$m_d -> where('id='.$id) -> save($data);
    			$this -> success('修改成功！','deptList');
    		}else{
    			$this -> error('没有修改内容！');
    		}
    	}else{
    		$id = $_GET['id'];
    		$m_d = M('department');
    		$item = $m_d -> where('id='.$id) -> find();
    		$this -> assign('item',$item);
    		$this -> display();
    	}
    }

    public function deptDel(){
    	if((isset($_GET['id']) && !empty($_GET['id'])) && ($_SESSION['level'] <2)){
            $id = $_GET['id'];
            $m_d = M('department');
    		$m_d -> where('id='.$id) -> delete();
    		$this -> success('删除成功！');
    	}else{
    		$this -> error('操作失败！');
    	}
    }
}