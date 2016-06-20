<?php
// 本类由系统自动生成，仅供测试用途
class AccountAction extends Action {

	public function __construct() {
        parent::__construct();
        $this->CheckmSession();
    }

    private function CheckmSession(){
        if(!isset($_SESSION['username'])&& !isset($_SESSION['login']) && $_SESSION['login'] !== true){
            $this -> error('抱歉!您还没有登录或登录超时，请重新登录！',U('Auth/login'));
        }
    }

    public function accList(){
    	$m_u = M('user');
    	$acclist = $m_u -> join('department on user.dept_id = department.id') -> field('user.id,user.dept_id,dept_name,user.name,user.email,user.telephone,user.level') 
                        -> select();
        $this -> assign('acclist',$acclist);
        $this -> display();
    	
    }

    public function accAdd(){
    	if((isset($_POST['email']) && !empty($_POST['email']))
    		&&(isset($_POST['password']) && !empty($_POST['password']))){
    		$dept_id = $_POST['deptname'];
    		$name = $_POST['fullname'];
    		$level = $_POST['level'];
    		$email = $_POST['email'];
    		$password = $_POST['password'];
    		$telephone = $_POST['tel'];
    		$item = array(
    			"dept_id" => $dept_id,
    			"name" => $name,
    			"level" => $level,
    			"email" => $email,
    			"password" => $password,
    			"telephone" => $telephone,
    			"time" => time()
    		);
            $m_u = M('user');
            $ret = $m_u -> where(array("email" => $email)) -> find();
            
    		if($ret == null){
    			$m_u -> add($item);
    			$this -> success('添加成功！','accList');
    		}else{
    			$this -> error('该邮箱已经使用！');
    		}
    	}else{
            $m_d = M('department');
    		$deptInfo = $m_d -> field('id,dept_name') -> where('pid=0') -> select();
    		$this -> assign('deptInfo',$deptInfo);
    		$this -> display();
    	}
    }

    public function accEdit(){
        if((isset($_POST['email']) && !empty($_POST['email']))
            &&(isset($_POST['password']) && !empty($_POST['password']))){
            $userid = $_POST['userid'];
            $dept_id = $_POST['deptname'];
            $name = $_POST['fullname'];
            $level = $_POST['level'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $telephone = $_POST['tel'];
            $item = array(
                "dept_id" => $dept_id,
                "name" => $name,
                "email" => $email,
                "password" => $password,
                "telephone" => $telephone
            );
            $m_u = M('user');
            $ret = $m_u -> where($item) -> find();
            if($ret == null){
                $m_u -> where('id='.$userid) -> save($item);
                $this -> success('修改成功！','accList');
            }else{
                $this -> error('没有修改内容！');
            }
        }else{
            $id = $_GET['id'];
            $email = $_SESSION['username'];
            $m_d = M('department');
            $deptInfo = $m_d -> field('id,dept_name') -> where('pid=0') -> select();
            $this -> assign('deptInfo',$deptInfo);
            $where = array(
                "id" => $id,
                "email" => $email
            );
            $m_u = M('user');
            $ret = $m_u -> where('id='.$id) -> find();
            $isFind = $m_u -> where($where) -> find();
            $isSelf = $isFind !== null ? true : false;
            $this -> assign('isSelf',$isSelf);
            $this -> assign('userid',$id);
            $this -> assign('userInfo',$ret);
            $this -> display();
            
        }
    }


    public function accDel(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $_GET['id'];
            $email = $_SESSION['username'];
            $where = array(
                "id" => $id,
                "email" => $email
            );
            $m_u = M('user');
            $ret = $m_u -> where($where) -> delete();
            if($ret == false){
                $this -> error('你不能够删除他人信息！');
            }else{
                $this -> success('删除成功！');
            }
        }else{
            $this -> error('操作失败！');
        }
    }

}