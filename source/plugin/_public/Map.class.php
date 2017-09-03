<?php
/**
* @author xialei <xialeistudio@gmail.com>
*/
class Map
{
  private static $_instance;

  const REQ_GET = 1;
  const REQ_POST = 2;

  /**
  * ����ģʽ
  * @return map
  */
  public static function instance()
  {
    if (!self::$_instance instanceof self)
    {
      self::$_instance = new self;
    }
    return self::$_instance;
  }

  /**
  * ִ��CURL����
  * @author: xialei<xialeistudio@gmail.com>
  * @param $url
  * @param array $params
  * @param bool $encode
  * @param int $method
  * @return mixed
  */
  private function async($url, $params = array(), $encode = true, $method = self::REQ_GET)
  {
    $ch = curl_init();
    if ($method == self::REQ_GET)
    {
      $url = $url . '?' . http_build_query($params);
      $url = $encode ? $url : urldecode($url);
      curl_setopt($ch, CURLOPT_URL, $url);
    }
    else
    {
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    }
    curl_setopt($ch, CURLOPT_REFERER, '�ٶȵ�ͼreferer');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; en-us) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $resp = curl_exec($ch);
    curl_close($ch);
    return $resp;
  }

  /**
  * ��ȡ�ͻ���ip��ַ
  * ע��:�������Ҫ��ip��¼����������,����д��ʱ�ȼ��һ��ip�������Ƿ�ȫ.
  */
  public function getIP() {
    if (getenv('HTTP_CLIENT_IP')) {
      $ip = getenv('HTTP_CLIENT_IP'); 
    }
    elseif (getenv('HTTP_X_FORWARDED_FOR')) { //��ȡ�ͻ����ô������������ʱ����ʵip��ַ
      $ip = getenv('HTTP_X_FORWARDED_FOR');
    }
    elseif (getenv('HTTP_X_FORWARDED')) { 
      $ip = getenv('HTTP_X_FORWARDED');
    }
    elseif (getenv('HTTP_FORWARDED_FOR')) {
      $ip = getenv('HTTP_FORWARDED_FOR'); 
    }
    elseif (getenv('HTTP_FORWARDED')) {
      $ip = getenv('HTTP_FORWARDED');
    }
    else { 
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  /**
  * ip��λ
  * http://developer.baidu.com/map/geosdk-symbian-class.htm
  * @param string $ip
  * @return array
  * @throws Exception
  */
  public function locationByIP($ip)
  {
    try{
      //����Ƿ�Ϸ�IP
      if ($ip=='127.0.0.1') {
        throw new Exception('����IP���ܶ�λ');
      }
      if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        throw new Exception('IP��ַ���Ϸ�');
      } else {
        $params = array(
          'ak' => 'q0tCNzPLy9QBTxWPhCLb6zmUwa8OBF7W',// �ٶȵ�ͼAPI KEY
          'ip' => $ip,
          'coor' => 'bd09ll' //�ٶȵ�ͼGPS���ꡣbd09ll ����Ϊ��γ����������;bd09 ����Ϊī����ƽ������;gcj02 ����Ϊ��γ���������� 
        );
        $api = 'http://api.map.baidu.com/location/ip';
        $resp = $this->async($api, $params);
        $data = json_decode($resp, true);
      }

      //check forms filled in
      if($data['status'] != 0){
        //�д���
        // return 0;
        throw new Exception($data['message']);
      } else {
        //���ص�ַ��Ϣ
        return array(
          'address' => $data['content']['address'],
          'province' => $data['content']['address_detail']['province'],
          'city' => $data['content']['address_detail']['city'],
          'district' => $data['content']['address_detail']['district'],
          'street' => $data['content']['address_detail']['street'],
          'street_number' => $data['content']['address_detail']['street_number'],
          'city_code' => $data['content']['address_detail']['city_code'],
          'lng' => $data['content']['point']['x'],
          'lat' => $data['content']['point']['y']
        );
      }
    } catch(Exception $e){
      return $e->getMessage(); //����쳣��Ϣ��
    }
  }

  /**
  * GPS��λ
  * @param $lng
  * @param $lat
  * @return array
  * @throws Exception
  */
  public function locationByGPS($lng, $lat)
  {
    $params = array(
      'coordtype' => 'wgs84ll',
      'location' => $lat . ',' . $lng,
      'ak' => 'q0tCNzPLy9QBTxWPhCLb6zmUwa8OBF7W',//�ٶȵ�ͼAPI KEY
      'output' => 'json',
      'pois' => 0
    );
    $resp = $this->async('http://api.map.baidu.com/geocoder/v2/', $params, false);
    $data = json_decode($resp, true);
    if ($data['status'] != 0)
    {
      throw new Exception($data['message']);
    }
    return array(
      'address' => $data['result']['formatted_address'],
      'province' => $data['result']['addressComponent']['province'],
      'city' => $data['result']['addressComponent']['city'],
      'street' => $data['result']['addressComponent']['street'],
      'street_number' => $data['result']['addressComponent']['street_number'],
      'city_code'=>$data['result']['cityCode'],
      'lng'=>$data['result']['location']['lng'],
      'lat'=>$data['result']['location']['lat']
    );
  }

}

?>
