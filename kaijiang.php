<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 3/1/19
 * Time: 9:43 PM
 */
ini_set('date.timezone','Asia/Shanghai');
$db_host = "127.0.0.1";
$db_user = "root";
$db_pwd  = "";
$db_name  = "www.bjl.com";



while(1){
    $second = intval(date('s'));
    if($second == 3){
        $url = "https://mma.qq.com/cgi-bin/im/online";
        $html = get_fcontent($url);
        $regex = '/\:(.*?)\,/';
        $matches = array();
        preg_match($regex, $html, $matches);
        if(empty($matches[1])){
            $url = "https://www.qqff6.com/Home/GetAwardData?r=".time();
            $html = get_fcontent($url);
            $regex = '/awardNumber\"\:\"(.*?)\"/';
            $matches = array();
            preg_match($regex, $html, $matches);
            $codes =substr($matches[1],2,7);
            $arr = explode(',',$codes);
            $online = $arr[0]*1000+$arr[1]*100+$arr[2]*10+$arr[3]*1;
        }else{
            $online = intval($matches[1]);
            $codes = getcodes($online);
        }

        $time = time()-strtotime(date("Y-m-d"));
        $num = substr(intval($time/60)+10000,1,6);
        $date = date("Ymd").$num;
        echo $date.":".$codes.":".$online."\n";
        $mysqli = mysqli_connect($db_host, $db_user, $db_pwd, $db_name);
        if(!$mysqli ){
            echo mysqli_connect_error();
        }
        $sql = "insert into app_qq_data (`num`, `codes`,`online`) values ('$date', '$codes',$online)";
        $result = $mysqli->query($sql);
        mysqli_close($mysqli);
        sleep(1);
    }
}
function get_fcontent($url,  $timeout = 5 ) {
    $url = str_replace( "&amp;", "&", urldecode(trim($url)) );
    $cookie = tempnam ("/tmp", "CURLCOOKIE");
    $ch = curl_init();

        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );

        curl_setopt( $ch, CURLOPT_URL, $url);

        curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );

        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

        curl_setopt( $ch, CURLOPT_ENCODING, "" );

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );

        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );

        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        $content = curl_exec( $ch );
        curl_close ( $ch );
        return $content;
    }
function getcodes($online){
    $online = substr($online,-4);
    $num =  substr($online,0,1);
    $last4num = $num.',';
    $num =  substr($online,1,1);
    $last4num .= $num.',';
    $num =  substr($online,2,1);
    $last4num .= $num.',';
    $num =  substr($online,3,1);
    $last4num .= $num;

    return $last4num;
}