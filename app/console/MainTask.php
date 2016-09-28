<?php


class MainTask extends \Phalcon\Cli\Task
{
    public function mainAction()
    {
            set_time_limit(0);

//        foreach (range(12,100) as $key => $val)
//        {
            $time = str_replace('.','',$_SERVER['REQUEST_TIME_FLOAT']);

            $deviceId = 'e'.mt_rand(10000000,99999999).mt_rand(1000000,9999999);

            $command = "curl 'https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxverifyuser?r={$time}&lang=zh_CN&pass_ticket=v9bBHsmW0IX8mxyON%252FXUW2i6o7iiEoMZLftuVc0SwrLQM1CWaeBQjaEpsK4GY%252BIe' -H 'Origin: https://wx.qq.com' -H 'Accept-Encoding: gzip, deflate' -H 'Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.6' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36' -H 'Content-Type: application/json;charset=UTF-8' -H 'Accept: application/json, text/plain, */*' -H 'Referer: https://wx.qq.com/?&lang=zh_CN' -H 'Cookie: pac_uid=1_823890165; tvfe_boss_uuid=c79616ca341a2bd4; webwxuvid=47d5198ade8ebd9e20f3e813bdffeabac39308fc4cb34491e15c5bdae82598bc722d111becd1b2363b552b5dc3be7e35; OUTFOX_SEARCH_USER_ID_NCOO=645349421.9272022; RK=1TMy68biOx; pgv_pvi=5584728064; pgv_pvid=6519531525; pgv_info=ssid=s6529990739; pt2gguin=o0823890165; uin=o0823890165; skey=@VINflhZOJ; ptisp=ctc; qzone_check=823890165_1474095320; ptcz=bd6890871dab4eb8a7e820083a0925c75e40dce245bb1da3bf1ec5c857c019e6; qqmusic_uin=; qqmusic_key=; qqmusic_fromtag=; pgv_si=s3692735488; MM_WX_NOTIFY_STATE=1; MM_WX_SOUND_STATE=1; mm_lang=zh_CN; wxloadtime=1474101978_expired; wxpluginkey=1474100712; wxuin=1525075320; wxsid=R8O/n+blXKHJwRcD; webwx_data_ticket=gSf65j/EbetRAWcpCuZwThas' -H 'Connection: keep-alive' --data-binary $'{\"BaseRequest\":{\"Uin\":1525075320,\"Sid\":\"R8O/n+blXKHJwRcD\",\"Skey\":\"@crypt_a48ea4_add6379463bc424e96d17934141fdf2c\",\"DeviceID\":\"{$deviceId}\"},\"Opcode\":2,\"VerifyUserListSize\":1,\"VerifyUserList\":[{\"Value\":\"gpyyzg\",\"VerifyUserTicket\":\"\"}],\"VerifyContent\":\"\",\"SceneListCount\":1,\"SceneList\":[7],\"skey\":\"@crypt_a48ea4_add6379463bc424e96d17934141fdf2c\"}' --compressed";

            echo (shell_exec($command))."\r\n";
            echo "-----------------------\r\n";
//            echo $val."\r\n";
//            sleep(60);
//        }


    }


    public function md5Action()
    {
        echo md5('ichuangyi99')."\r\n";
        echo ('ichuangyi99')."\r\n";
    }

    public function sendAction()
    {

        $id = time().mt_rand(10000,99999);
        $deviceId = 'e'.mt_rand(10000000,99999999).mt_rand(1000000,9999999);
        $command = "curl 'https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxsendmsg' -H 'Origin: https://wx.qq.com' -H 'Accept-Encoding: gzip, deflate' -H 'Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.6' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36' -H 'Content-Type: application/json;charset=UTF-8' -H 'Accept: application/json, text/plain, */*' -H 'Referer: https://wx.qq.com/' -H 'Cookie: pac_uid=1_823890165; tvfe_boss_uuid=c79616ca341a2bd4; webwxuvid=47d5198ade8ebd9e20f3e813bdffeabac39308fc4cb34491e15c5bdae82598bc722d111becd1b2363b552b5dc3be7e35; OUTFOX_SEARCH_USER_ID_NCOO=645349421.9272022; RK=1TMy68biOx; pgv_pvi=5584728064; pgv_si=s3692735488; wxloadtime=1474101978_expired; qz_gdt=f4fn2vzcaiapeyhkydfa; pgv_info=ssid=s6529990739; pgv_pvid=6519531525; o_cookie=823890165; pgv_gdtid=f4fn2vzcaiapeyhkydfa; mm_lang=zh_CN; MM_WX_NOTIFY_STATE=1; MM_WX_SOUND_STATE=1; wxpluginkey=1474103679; wxuin=1525075320; wxsid=R8O/n+blXKHJwRcD; webwx_data_ticket=gSf65j/EbetRAWcpCuZwThas; pt2gguin=o0823890165; uin=o0823890165; skey=@rAgKjGQew; ptisp=ctc; qzone_check=823890165_1474104852; ptcz=bd6890871dab4eb8a7e820083a0925c75e40dce245bb1da3bf1ec5c857c019e6; qqmusic_uin=; qqmusic_key=; qqmusic_fromtag=' -H 'Connection: keep-alive' --data-binary '{\"BaseRequest\":{\"Uin\":1525075320,\"Sid\":\"R8O/n+blXKHJwRcD\",\"Skey\":\"@crypt_a48ea4_add6379463bc424e96d17934141fdf2c\",\"DeviceID\":\"$deviceId\"},\"Msg\":{\"Type\":1,\"Content\":\"关注\",\"FromUserName\":\"@206ca2258147004d8b3370f0d302f8ce\",\"ToUserName\":\"gpyyzg\",\"LocalID\":\"$id\",\"ClientMsgId\":\"$id\"},\"Scene\":0}' --compressed";

        echo shell_exec($command)."\r\n";
    }
}