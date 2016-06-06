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

    public function dataEntryList(){
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

    public function dataEntry(){
    	if(isset($_POST['reportdate']) && !empty($_POST['reportdate'])){
    		$proid = $_POST['proid'];
            $reportdate = $_POST['reportdate'];
            
            $data['dept_id'] = $proid;
            $data['date'] = $reportdate;
            $data['operator'] = $_SESSION['name'];
            $data['operator_time'] = date('Y-m-d H:i:s');
            $m_r = M('report');
            $report_id = $m_r -> add($data);

            $m_r_c = M('report_column');
            $rcfields = $m_r_c -> where('dept_id='.$proid) -> select();
            foreach ($rcfields as $k => $v) {
                $rcid = $v['id'];
                $cname = $v['cname'];
                $cvalue = $_POST[$cname];

                $item['report_id'] = $report_id;
                $item['reportc_id'] = $rcid;
                $item['date'] = $reportdate;
                $item['cname'] = $cname;
                $item['cvalue'] = $cvalue;
                
                $m_r_v = M('report_value');
                $ret = $m_r_v -> add($item);
            }
            if($ret !== false){
                $this -> success('数据录入成功！');
            }else{
                $this -> success('数据录入失败！');
            }
    	}else{
            $proid = $_GET['id'];
    		$m_d = M('department');
    		$deptInfo = $m_d -> select();
    		$this -> assign('deptInfo',$deptInfo);
    		$m_r_c = M('report_column');
    		$rField = $m_r_c -> where('dept_id='.$proid) -> select();

            $this -> assign('proid',$proid);
    		$this -> assign('rField',$rField);
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
        $m_r_c = M('report_column');
        $m_r_v = M('report_value');
        
        $dateRange = $this -> _getDateRange();
        for ($i = 0,$j = count($proitems); $i < $j; $i++) {
            $pros[$i]['proid'] = $proitems[$i]['id'];
            $pros[$i]['proname'] = $proitems[$i]['dept_name'];
        }

        $where = array(
            'date' => array('between', array($dateRange['begin'], $dateRange['end']))
        );
        $reports = $m_r ->where($where) -> select();
        foreach ($reports as $k1 => $v1) {
            $rets = $m_r_v -> join('report_column on report_value.reportc_id = report_column.id')
                           -> where(array("report_id" => $v1['id'])) -> select();
            $item[$k1]['report_id'] = $v1['id'];
            $item[$k1]['dept_id'] = $v1['dept_id'];
            $item[$k1]['date'] = $v1['date'];
            foreach ($rets as $k2 => $v2) {
                $key = $v2['cname'];
                $value = $v2['cvalue'];
                if($v2['type'] == 1){
                    $item[$k1][$key] = $value;
                    $graphYdata[$k1][$key] = $value;
                }
                if($v2['type'] == 2){
                    $formula = $v2['formula'];
                    $arr = explode(' ', $formula);
                    foreach ($arr as $k3 => $v3) {
                        $expk = $v3;
                        $expv = $item[$k1][$expk];
                        if($expv !== null){
                            $exp .= $expv;
                        }else{
                            $exp .= $expk;
                        }
                    }
                    eval("\$ret=$exp;");
                    $item[$k1][$key] = $ret;
                    $graphYdata[$k1][$key] = $ret;
                    unset($exp);
                }
            }
        }
        $this -> assign('prodatas',$item);
        var_dump($item);
        //die();
        $xdate = $this -> _getXDate($item);
        $this -> assign('xdate',$xdate);
        foreach ($graphYdata as $k => $v) {
            foreach ($v as $k1 => $v1) {    
                $ydatas[$k1] = $this -> _getKindData($graphYdata,$k1);
                $this -> assign('',$ydatas);
            }
        }
        echo $xdate;
        var_dump($ydatas);
        die();
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