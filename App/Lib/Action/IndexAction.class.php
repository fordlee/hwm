<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
	public function __construct() {
        parent::__construct();
        $this->CheckmSession();
    }

    private function CheckmSession(){
        if(!isset($_SESSION['username'])&& !isset($_SESSION['login']) && $_SESSION['login'] !== true){
            $this -> error('抱歉!您还没有登录或登录超时，请重新登录！',U('Auth/login'));
        }
    }

    public function index(){
		$this -> display('index2');
    }

    public function dataDemo(){
        $column=
        array(
            array(
                "cname"=>"click",
                "type"=>"1",
                "formula"=>""
                ),
            array(
                "cname"=>"impression",
                "type"=>"1",
                "formula"=>""
                ),
            array(
                "cname"=>"ctr",
                "type"=>"2",
                "formula"=>"clicks / impressions"
                )  

        );

        $values=array(

            array(
                "click"=>159,
                "impressions"=>1000
            ),
            array(
                "click"=>159,
                "impressions"=>1000
            ),
             array(
                "click"=>159,
                "impressions"=>1000
            )           
        );

        foreach ($values as $key => $value) {
            foreach ($column as $key1 => $value1) {
                # code...
            }
            # code...
        }




        array(

            "click"=>159,
            "impression"=>1000,
            "ctr"=>159/1000
        );

        $exp="\$p=( 100 - 50 ) / 100;";
        eval($exp);
        echo $p;

        /*try{
            $exp="\$ctr_exp=(100-50)/100;";
            eval($exp);
        }catch(e){}
        if(defined(name)$ctr_exp){$ctr=ctr_exp;}*/

    }

}