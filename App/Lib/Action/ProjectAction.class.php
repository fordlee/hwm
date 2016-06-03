<?php
// 本类由系统自动生成，仅供测试用途
class ProjectAction extends Action {

	public function __construct() {
        parent::__construct();
        $this->CheckmSession();
    }

    private function CheckmSession(){
        if(!isset($_SESSION['username'])&& !isset($_SESSION['login']) && $_SESSION['login'] !== true){
            $this -> error('抱歉!您还没有登录或登录超时，请重新登录！',U('Auth/login'));
        }
    }

    public function proList(){
    	$m_d = M('department');
        $dept = $m_d -> select();
        $tree = getTreeHtml($dept, 0);
        $this -> assign('tree',$tree);
    	
    	$username = $_SESSION['username'];
    	$m_u = M('user');
    	$useritem = $m_u -> join('department on user.dept_id = department.id') 
    					 -> where(array("email" => $username)) 
    					 -> find();
    	$deptInfo = array();
    	if($useritem !== null){
    		$dept_id = $useritem['dept_id'];
    		$ret1 = $m_d -> where(array("pid" => $dept_id)) -> select();
    		$deptInfo = $useritem;
    		$deptInfo['dept_child'] = $ret1;
    	}
    	$this -> assign('level',$useritem['level']);
    	$this -> assign('deptInfo',$deptInfo);
    	$this -> display();
    }

    public function proAdd(){
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
                $pid = $_POST['pid'];
    			$data['pid'] = $pid;
    			$data['dept_date'] = date("Y-m-d");
    			$m_d -> add($data);
    			$this -> success('添加成功！','proList');
    		}else{
    			$this -> error('您已添加,请勿重复操作！');
    		}

    	}else{
            $pid = $_GET['dept_id'];
            $this -> assign('pid',$pid);
    		$this -> display();
    	}
    }

    public function proEdit(){
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
    			$this -> success('修改成功！','proList');
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

    public function proDel(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $_GET['id'];
            $m_d = M('department');
            $m_d -> where('id='.$id) -> delete();
            $this -> success('删除成功！');
        }else{
            $this -> error('操作失败！');
        }
    }

    public function proFieldSet(){
        $m_r_c = M('report_column');
        if(isset($_POST['proColumn']) 
            && isset($_POST['proTitle'])
            && isset($_POST['type'])){
            $proid = $_POST['proid'];
            $proColumn = $_POST['proColumn'];
            $proTitle = $_POST['proTitle'];
            $type = $_POST['type'];
            $formula = $_POST['formula'];
            $data = array(
                "dept_id" => $proid,
                "cname" => $proColumn,
                "ctitle" => $proTitle,
                "type" => $type,
                "formula" => $formula
            );
            
            $ret = $m_r_c -> add($data);
            if($ret !== false){
                $this -> success('添加成功！');
            }else{
                $this -> error('添加失败！');
            }
        }else{
            $proid = $_GET['id'];
            $ret = $m_r_c -> field('cname,ctitle,formula') -> where("dept_id=".$proid) -> select();
            $this -> assign('ret',$ret);
            $this -> assign('proid',$proid);
            $this -> display();
        }
    }

    public function proFieldEdit(){
        
    }
}