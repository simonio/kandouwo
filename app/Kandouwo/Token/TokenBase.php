<?php

namespace Kandouwo\Token;

/* 运行结果

备注：
token的结构：签名-base64(版本号)-base64(encrypt(data))
data中的字段用换行符分割，字段的顺序约定好就行，目前是（用户名，密码，ip，时间戳，once）

data:
string 'ak_meng@126.com
123456
192.168.1.1
1413640916
1234567890123456' (length=62)
构造 Token
string 'E5NB9yFj7bdS/RKi+Gt7V/FGjkw=-MS4w-Z1h04uhfqEYJDIc0vI8zx9UUfNTtku6YglMO3SgzXcSUTe9+H8RCwUYhwTSfgmxgggrfmBncNDab4WQaA4oWFQ==' (length=122)
解析 Token
array (size=3)
'sign' => string 'E5NB9yFj7bdS/RKi+Gt7V/FGjkw=' (length=28)
'version' => string '1.0' (length=3)
'data' => string 'ak_meng@126.com
123456
192.168.1.1
1413640916
1234567890123456' (length=62)
*/


/**
 * @brief 使用HMAC-SHA1算法生成oauth_signature签名值
 *
 * @param $key  密钥
 * @param $str  源串
 *
 * @return 签名值
 */
function getSignature($str, $key)
{
  $signature = "";
  if (function_exists('hash_hmac'))
  {
    $signature = base64_encode(hash_hmac("sha1", $str, $key, true));
  } else
  {
    $blocksize = 64;
    $hashfunc = 'sha1';
    if (strlen($key) > $blocksize)
    {
      $key = pack('H*', $hashfunc($key));
    }
    $key = str_pad($key, $blocksize, chr(0x00));
    $ipad = str_repeat(chr(0x36), $blocksize);
    $opad = str_repeat(chr(0x5c), $blocksize);
    $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) .
      $str))));
    $signature = base64_encode($hmac);
  }
  return $signature;
}

class AESMcrypt
{
  /**
   * 设置默认的加密key 
   * @var str 
   */
  public static $defaultKey = "";

  /**
   * 设置默认加密向量 
   * @var str 
   */
  private $iv = 'y6aebpcoNgBT0+h1';

  /**
   * 设置加密算法 
   * @var str 
   */
  private $cipher;

  /**
   * 设置加密模式 
   * @var str 
   */
  private $mode;

  public function __construct($cipher = MCRYPT_RIJNDAEL_128, $mode =
    MCRYPT_MODE_CBC)
  {
    $this->cipher = $cipher;
    $this->mode = $mode;
  }

  /**
   * 对内容加密，注意此加密方法中先对内容使用padding pkcs7，然后再加密。 
   * @param str $content    需要加密的内容 
   * @return str 加密后的密文 
   */
  public function Encode($content, $key)
  {
    if (empty($content))
    {
      return null;
    }
    $srcdata = $content;
    $block_size = mcrypt_get_block_size($this->cipher, $this->mode);
    $padding_char = $block_size - (strlen($content) % $block_size);
    $srcdata .= str_repeat(chr($padding_char), $padding_char);
    return mcrypt_encrypt($this->cipher, $key, $srcdata, $this->mode, $this->iv);
  }

  /**
   * 对内容解密，注意此加密方法中先对内容解密。再对解密的内容使用padding pkcs7去除特殊字符。 
   * @param String $content    需要解密的内容 
   * @return String 解密后的内容 
   */
  public function Decode($content, $key)
  {
    if (empty($content))
    {
      return null;
    }

    $content = mcrypt_decrypt($this->cipher, $key, $content, $this->mode, $this->iv);
    $block = mcrypt_get_block_size($this->cipher, $this->mode);
    $pad = ord($content[($len = strlen($content)) - 1]);
    return substr($content, 0, strlen($content) - $pad);
  }
}

class TokenManagerBase
{

  public static function ArrayToString($arr, $split = "-")
  {
    $str = '';
    for ($i = 0; $i < count($arr); $i++)
    {
      if ($i != count($arr) - 1)
      {
        $str .= $arr[$i] . $split;
      } else
      {
        $str .= $arr[$i];
      }
    }
    return $str;
  }

  public static function StringToArray($str, $split = "-")
  {
    $arr = explode($split, $str);
    return $arr;
  }

  /**
   * 解析Token
   *
   * $str: base64[签名+扩展字段(版本号)+应用ID+随机初始变量()+加密后的验证信息（应用ID、当前时间、当前IP、用户ID、用户密码哈希）]
   * $key: 私密钥
   *
   * 返回值：
   * [
   * 	'signMethod' => string
   * 	'version' => string
   * 	'timestamp' => string(距1970 00:00:00 GMT的秒数)
   * 	'once' => string
   * 	'data' => 数据（键值数组）
   * ]
   *
   */
  //
  public function ParseToken($tokenString, $key)
  {
    $tokenArray = TokenManagerBase::StringToArray($tokenString, '-');
    if ($tokenArray == null)
    {
      return null;
    }
    if (count($tokenArray) != 3)
    {
      return null;
    }

    $token['sign'] = $tokenArray[0];
    $token['version'] = base64_decode($tokenArray[1]);
    $token['data'] = $tokenArray[2];

    if ($token['sign'] != getSignature($tokenArray[1] . '-' . $token['data'], $key))
    {
      $token = null;
    } else
    {
      $encrypter = new AESMcrypt();
      $token['data'] = $encrypter->Decode(base64_decode($token['data']), $key);
    }
    return $token;
  }

  /**
   * 构造Token
   *
   * $signMethod 签名方法
   * $version Token的版本号
   * $timestamp 时间戳(距1970 00:00:00 GMT的秒数)
   * $once 单次值（32位随机字符串）
   * $data 数据（键值数组）
   * $key 私密钥
   *
   * 返回值：base64转码后的Token字符串
   */
  public function ComposeToken($signMethod, $version, $timestamp, $once, $data, $key)
  {
    // 内部信息
    $data[] = $timestamp;
    $data[] = $once;

    $strIn = TokenManagerBase::ArrayToString($data, "\n");

    // 使用签名方法加密 $arrayIn
    $encrypter = new AESMcrypt();
    $data = $encrypter->Encode($strIn, $key);
    $strOut = base64_encode($version) . '-' . base64_encode($data);
    $sign = getSignature($strOut, $key);

    // 返回base64(签+信息)
    return $sign . '-' . $strOut;
  }
}
