<?php
// 本类由系统自动生成，仅供测试用途
class AuthAction extends Action {
    public function login() {
        if (isset($_POST['username'])
            && isset($_POST['password'])) {
            /*if ($token !== $this -> _getToken()) {
                $this -> error('error');
            }*/
            $usr = $_POST['username'];
            $pwd = $_POST['password'];
            $token = $_POST['token'];
            $where = array(
                "email" => $usr,
                "password" => $pwd
            );
            $m = M('user');
            $ret = $m -> where($where) -> find();
            if ($ret !== null) {
                $_SESSION['login'] = true;
                $_SESSION['username'] = $usr;
                $_SESSION['rule'] = 'admin';
                $_SESSION['name'] = $ret['name'];
                $_SESSION['level'] = $ret['level'];
                $this -> redirect('Index/index');
            } else {
                $this -> error('username or password error!', 'login');
            }
        } else {
            $this -> display();
        }
    }

    public function logout() {
        unset($_SESSION['login']);
        unset($_SESSION['username']);
        unset($_SESSION['uid']);
        $this -> redirect('Auth/login');
    }

    private function _getToken() {
        $token = array(
            '01' => 'b5006a3d6475e0c9d22d118e777e705a',
            '02' => '0ee3e1c46b6b58a65ebeae27e1f854e7',
            '03' => '16385ad5b4b8f2e47fea633099858c77',
            '04' => '9d88c7612811e1a628c05929307682dc',
            '05' => '8fbab917980778ac1e75f3b6bfa02cc5',
            '06' => 'a2d70b3987427f62b3136bedd8ac8cd5',
            '07' => 'd2e9642da1a183006d5c3ed8ddae52b0',
            '08' => '622db4adc09ccaa97dfb999d0a95e0ed',
            '09' => 'e485302a1016fabcf4f55514eed9eeba',
            '10' => '20f8043fbe240721097de7920bac8b67',
            '11' => 'f71762963f16ee253f1db6d45cdd59be',
            '12' => 'fded24a93c8c01dac8afabafa53a8539',
            '13' => '36123772fe7089e8a9e8218325dc86eb',
            '14' => '8473d71c3c8bf758f429499f74d1ad60',
            '15' => 'c09a25b6d7be0ae026a2d1267da868da',
            '16' => '2351b6e84e518cddb9dcc59821af3108',
            '17' => '28bd75eb7ce8335f6e709af3a771b097',
            '18' => '76de4bae9be0f6ca07b04693fec1d9d4',
            '19' => 'b80e6a5d06c53532311eba18a92f4847',
            '20' => '6a734514b6f563b7b6857557a7ff8fc6',
            '21' => 'd52488d84313e2dddb8dee76951d4432',
            '22' => '65c0c7596b9d7c24dcb6ad8b2b40d1c7',
            '23' => 'fa6b8cdd0fee09e9de79758a6e6c14a9',
            '24' => '42619f29490302f4e738498008756b59',
            '25' => '28f68b270d59c64fad08268cd372bc20',
            '26' => '299d94cd0d6fc8d64bf9516a204176fd',
            '27' => '4a75068215f759f56417575832e1a2ca',
            '28' => '59909a717db9e44206145ad4e2e321a3',
            '29' => '6c77402d190ca77987649144b2ccc24b',
            '30' => '9f20be941c27c5a52cc0d1a7e4185c98',
            '31' => 'afb43c00abca7c3027ecdfc315f4f41f',
        );
        return $token[date('d')];
    }
}