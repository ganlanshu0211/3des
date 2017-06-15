<?php

/**在调用PHP中的mcrypt_get_block_size()函数时需要：
 * 启用Mcrypt函数集。linux下要安装libmcrypt，windows下找到php.ini文件里
 * extension=php_mcrypt.dll
 * extension=php_mcrypt_filter.dll
 * php服务器，java服务器，3des加密解密，
 * @author esyy@qq.com
**/
namespace Shop\Model;
use Think\Model;
class Pay3desModel extends Model {
	protected $autoCheckFields =false;
    private $enckey;
    private $deskey;
    //private $iv;
 
    public function __construct(){
        $this->enckey = C('PAY_ENCKEY');
        $this->deskey = C('PAY_DESKEY');
    }
    function encrypt($input) { // 数据加密 
        $size = mcrypt_get_block_size ( MCRYPT_3DES, 'ecb' ); 
        $input = $this->pkcs5_pad ( $input, $size ); 
        $key = str_pad ( $this->enckey, 24, '0' ); 
        $td = mcrypt_module_open ( MCRYPT_3DES, '', 'ecb', '' ); 
        $iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND ); 
        @mcrypt_generic_init ( $td, $key, $iv ); 
        $data = mcrypt_generic ( $td, $input ); 
        mcrypt_generic_deinit ( $td ); 
        mcrypt_module_close ( $td ); 
        $data = strtoupper(bin2hex( $data )); 
        return $data; 
    } 
    function decrypt($encrypted) { // 数据解密 
        $encrypted = $this->myhex2bin( $encrypted ); 
        $key = str_pad ( $this->deskey, 24, '0' ); 
        $td = mcrypt_module_open ( MCRYPT_3DES, '', 'ecb', '' ); 
        $iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND ); 
        $ks = mcrypt_enc_get_key_size ( $td ); 
        @mcrypt_generic_init ( $td, $key, $iv ); 
        $decrypted = mdecrypt_generic ( $td, $encrypted ); 
        mcrypt_generic_deinit ( $td ); 
        mcrypt_module_close ( $td ); 
        $y = $this->pkcs5_unpad ( $decrypted ); 
        return $y; 
    } 
    function pkcs5_pad($text, $blocksize) { 
        $pad = $blocksize - (strlen ( $text ) % $blocksize); 
        return $text . str_repeat ( chr ( $pad ), $pad ); 
    } 
    function pkcs5_unpad($text) { 
        $pad = ord ( $text {strlen ( $text ) - 1} ); 
        if ($pad > strlen ( $text )) { 
            return false; 
        } 
        if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad) { 
            return false; 
        } 
        return substr ( $text, 0, - 1 * $pad ); 
    } 
    function PaddingPKCS7($data) { 
        $block_size = mcrypt_get_block_size ( MCRYPT_3DES, MCRYPT_MODE_CBC ); 
        $padding_char = $block_size - (strlen ( $data ) % $block_size); 
        $data .= str_repeat ( chr ( $padding_char ), $padding_char ); 
        return $data; 
    } 
	
	private function myhex2bin($data) {
		$len = strlen($data);
		return pack("H".$len,$data);
	}
}
