<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/7 0007
 * Time: 下午 1:55
 */

namespace app\api\service\kajuan;


use think\Exception;

class Ticket
{
    // 获取卡劵ticket
    private $ticketUrl;
    const TOKEN_CACHED_KEY = 'ticket';
    const TOKEN_EXPIRE_IN = 7000;

    function __construct($gzh_accseeToken)
    {
        $url = config('wx_config.ticket_url');
        $url = sprintf($url, $gzh_accseeToken);
        $this->ticketUrl = $url;
    }

    // 建议用户规模小时每次直接去微信服务器取最新的ticket
    public function get()
    {
        $ticket = $this->getFromCache();
        if(!$ticket){
            return $this->getFromWxServer();
        }
        else{
            return $ticket;
        }
    }

    // 去缓存中检查ticket是否还有效
    private function getFromCache(){
        $ticket = cache(self::TOKEN_CACHED_KEY);

        if($ticket){
            return $ticket['ticket'];
        }
        return null;
    }

    // 去微信的接口获取ticket
    private function getFromWxServer()
    {
        $ticket = curl_get($this->ticketUrl);
        $ticket = json_decode($ticket, true);
        if (!$ticket)
        {
            throw new Exception('获取卡劵ticket异常,kajuan/ticket类');
        }
        if(!empty($ticket['errcode'])){
            throw new Exception($ticket['errmsg']);
        }
        // 保存到缓存
        $this->saveToCache($ticket);
        return $ticket['ticket'];
    }

    // 保存到缓存
    private function saveToCache($ticket){
        cache(self::TOKEN_CACHED_KEY, $ticket, self::TOKEN_EXPIRE_IN);
    }
}