<?php
// 本类由系统自动生成，仅供测试用途
class ReportAction extends Action {
	public function __construct() {
        parent::__construct();
        $this->CheckmSession();
    }

    private function CheckmSession(){
        if(!isset($_SESSION['username'])&& !isset($_SESSION['login']) && $_SESSION['login'] !== true){
            $this -> error('抱歉!您还没有登录或登录超时，请重新登录！',U('Auth/login'));
        }
    }

    public function dataEntry(){
    	if((isset($_POST['dept']) && !empty($_POST['dept']))
    		&&(isset($_POST['pro']) && !empty($_POST['pro']))
            &&(isset($_POST['reportdate']) && !empty($_POST['reportdate']))
    		&&(isset($_POST['income']) && !empty($_POST['income']))
    		&&(isset($_POST['outcome']) && !empty($_POST['outcome']))){
    		$pro = $_POST['pro'];
            $reportdate = $_POST['reportdate'];
    		$income = $_POST['income'];
    		$outcome = $_POST['outcome'];
            
            $data['dept_id'] = $pro;
            $data['date'] = $reportdate;
            $data['income'] = $income;
            $data['outcome'] = $outcome;
            $data['operator'] = $_SESSION['name'];
            $data['operator_time'] = date('Y-m-d H:i:s');
            $m_r = M('report');
            //var_dump($data);die();
            $ret = $m_r -> add($data);
            if($ret !== false){
                $this -> success('数据录入成功！');
            }else{
                $this -> success('数据录入失败！');
            }
    	}else{
            $m_u = M('user');

    		$m_d = M('department');
    		$deptInfo = $m_d -> select();
    		$this -> assign('deptInfo',$deptInfo);
    		$m_r_e = M('report_ext');
    		$rc = $m_r_e -> where('dept_id='.$proid) -> select();//var_dump($rc);die();
    		$this -> assign('rc',$rc);
    		$this -> display();
    	}
    }

    public function dataReport(){
        $email = $_SESSION['username'];
        $m_u = M('user');
        $dept_id = $m_u -> where(array("email" => $email)) -> getField('dept_id');
        $m_d = M('department');
        $proitems = $m_d -> where('pid='.$dept_id) -> select();
        $m_r = M('report');
        $dateRange = $this -> _getDateRange();
        for ($i = 0,$j = count($proitems); $i < $j; $i++) {
            $where = array(
                'dept_id' => $proitems[$i]['id'],
                'date' => array('between', array($dateRange['begin'], $dateRange['end']))
            );
            $prodatas[$i] = $m_r -> order('date asc')
                                 -> field('dept_id,date,income,outcome,round(income-outcome,2) as profit,round((income-outcome)/outcome,2) as profitrate') 
                                 -> where($where)
                                 -> select();
            for($n = 0,$m = count($prodatas[$i]); $n < $m; $n++){
                $prodatas[$i][$n]['proname'] = $proitems[$i]['dept_name'];
            }
            
            $pros[$i]['proid'] = $proitems[$i]['id'];
            $pros[$i]['proname'] = $proitems[$i]['dept_name'];
        }
        //var_dump($prodatas);die();
        $prodatas = $prodatas[0];
        $xdate = $this -> _getXDate($prodatas);
        $income= $this -> _getKindData($prodatas,'income');
        $outcome=$this -> _getKindData($prodatas,'outcome');
        $profit=$this -> _getKindData($prodatas,'profit');
        $profitrate=$this-> _getKindData($prodatas,'profitrate');
        
        $this -> assign('xdate',$xdate);
        $this -> assign('income',$income);
        $this -> assign('outcome',$outcome);
        $this -> assign('profit',$profit);
        $this -> assign('profitrate',$profitrate);
        $this -> assign('prodatas',$prodatas);
        $this -> assign('pros',$pros);

        $dateRange = $this -> _getDateSwap($dateRange);
        $this -> assign('dateRange',$dateRange);
        
    	$this -> display();
    }

    public function reportSelect(){
        $dateRange = $_POST['dateRange'];
        $proid = $_POST['proid'];
        
        $email = $_SESSION['username'];
        $m_u = M('user');
        $dept_id = $m_u -> where(array("email" => $email)) -> getField('dept_id');
        $m_d = M('department');
        $proitems = $m_d -> where('pid='.$dept_id) -> select();
        $m_r = M('report');
        $dateRange = $this -> _getDateSwap($dateRange);
        for ($i = 0,$j = count($proitems); $i < $j; $i++) {
            $where = array(
                'dept_id' => $proitems[$i]['id'],
                'date' => array('between', array($dateRange['begin'], $dateRange['end']))
            );
            $prodatas[$i] = $m_r -> order('date asc') 
                                 -> field('dept_id,date,income,outcome,round(income-outcome,2) as profit,round((income-outcome)/outcome,2) as profitrate') 
                                 -> where($where)
                                 -> select();
            for($n = 0,$m = count($prodatas[$i]); $n < $m; $n++){
                $prodatas[$i][$n]['proname'] = $proitems[$i]['dept_name'];
            }
            
            $pros[$i]['proid'] = $proitems[$i]['id'];
            $pros[$i]['proname'] = $proitems[$i]['dept_name'];
        }

        $proarr = array();
        foreach ($prodatas as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                if($v2['dept_id'] == $proid){
                    $proarr = $prodatas[$k1];
                    break;
                }
            }
        }
        //var_dump($proarr);die();
        $xdate = $this -> _getXDate($proarr);
        $income= $this -> _getKindData($proarr,'income');
        $outcome=$this -> _getKindData($proarr,'outcome');
        $profit=$this -> _getKindData($proarr,'profit');
        $profitrate=$this-> _getKindData($proarr,'profitrate');
        
        $this -> assign('xdate',$xdate);
        $this -> assign('income',$income);
        $this -> assign('outcome',$outcome);
        $this -> assign('profit',$profit);
        $this -> assign('profitrate',$profitrate);
        $this -> assign('prodatas',$proarr);
        $this -> assign('pros',$pros);
        $this -> assign('proid',$proid);
        $dateRange = $this -> _getDateSwap($dateRange);
        $this -> assign('dateRange',$dateRange);
        
        $this -> display('dataReport');
    }

    private function _getKindData($data,$kind){
        foreach ($data as $k => $v) {
            $str .= $v[$kind].',';
        }
        $ret = substr($str,0,-1);
        return $ret;
    }

    private function _getXDate($data){
        foreach ($data as $k => $v) {
            $xdate .= '"'.$v[date].'"'.",";
        }
        $xdate = substr($xdate, 0, -1);
        return $xdate;
    }

    private function _getDateSwap($dateRange){
        if(is_array($dateRange)){
            //将数组时间转为字符串时间
            $dateRange['begin'] = date("m/d/Y",strtotime($dateRange['begin']));
            $dateRange['end'] = date("m/d/Y",strtotime($dateRange['end']));
            $dateRange = implode(' - ',$dateRange);
        }else{
            //将字符串03/01/2016 - 03/25/2016转为数组
            $date = explode(' - ', $dateRange);
            $dateRange = array();
            $dateRange['begin'] = date('Y-m-d',strtotime($date[0]));
            $dateRange['end'] = date('Y-m-d',strtotime($date[1]));
        }
        return $dateRange;
    }

    private function _getDateRange() {
        $today = strtotime(date('Y-m-d'));
        $ret = array (
            'begin' => date('Y-m-d', strtotime('-6 day', $today)),
            'end' => date('Y-m-d')
        );
        return $ret;
    }

}
?>