<?php
//by Lo9ic aka Abuy

error_reporting(E_ERROR);

function request($url, $data = null, $headers = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if($data):
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    endif;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($headers):
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
    endif;

    curl_setopt($ch, CURLOPT_ENCODING, "GZIP");
    return curl_exec($ch);
}


function getstr($str, $exp1, $exp2)
{
    $a = explode($exp1, $str)[1];
    return explode($exp2, $a)[0];
}



$chatID = "5167942500";



login:
$url = "https://shopee.co.id/api/v2/authentication/gen_qrcode";
$headers = array();
$headers[] = "X-Sap-Sec: xSgg2p6/ac63Zc63xm6cZcH3xm63ZcH3Zc67Zc63Kc23ZBH/Zc6LZc6330D9kFb3Zc6tZJ63yc23Z2Ejo/jbconni6/WO5p68b98OG0i8jxjrQvoIvsnXEJLAXIcVNw7T1hr9wRgB8FJrqpfFNcLSVPIR/O8P9HuTIXhWUbahJrdKV59+Qbn37Q5p0mZN1Y3m1tIuLYhU8rq/yUk7+y2oY/cDL8WB3GtD894fNFCf3igqkFVmX7z2BgkjcMGUIGZcAVaSSywsvFksr/je57l3KkpUZ46yMnOKHzPoh7WI5gHAig9Qlk26laIqnFrZOJtivkKv9rit9HRKEj2jlYV0Ls5ZmBqbH8UQGbaWu3MI65BgSeDbbaLqKkUUTo7QxEt4gYE3U0Wephtg7pN2FHcGIsfy6cWqhxqqFWwf55xyDXfIebMJ5VO8GI0dmuqc56zyCgY5Xp7qfR39Df3ZF/7s93mLM4ODJb3Zc61Dd4xfy0CgJ63Zc/MkqW9bc63ZQV3Zc6hZc63TD702tzKrwczBXf1QyDfc2/WL0pcZc63gy2FfT0bjd53Zc63bc6MZcb3xc6cZc63bc63ZQV3Zc6hZc630crHiXkW/s5tt+0A3hZBCQCBrd6cZc63fr+wF4kwjNH3Zc63";
$headers[] = "Sec-Ch-Ua-Mobile: ?0";
$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.5938.63 Safari/537.36";
$headers[] = "X-Sap-Ri: 984d1d65d5c5efa762001332010150c6601efa1e1627608bdc84";
$headers[] = "X-Api-Source: pc";
$headers[] = "X-Shopee-Language: id";
$headers[] = "X-Requested-With: XMLHttpRequest";
$headers[] = "If-None-Match-: 55b03-5673b3bafd16a131d8ce8d30602c178d";
$headers[] = "Sec-Fetch-Site: same-origin";
$headers[] = "Sec-Fetch-Mode: cors";
$headers[] = "Sec-Fetch-Dest: empty";
$headers[] = "Accept-Encoding: gzip, deflate, br";
$headers[] = "Accept-Language: en-US,en;q=0.9";
$getQR = request($url, null, $headers);
if(strpos($getQR, 'qrcode_base64')!==false)
{
    $qrcode_id = getstr($getQR, 'qrcode_id":"','"');
    $qrcode_id = urlencode($qrcode_id);
    $qrcode_base64 = getstr($getQR, 'qrcode_base64":"','"');
    $imageData = base64_decode($qrcode_base64);
    

    if ($imageData !== false) {
        // Prepare the image for uploading as a file
        $tempFileName = tempnam(sys_get_temp_dir(), 'telegram_photo');
        file_put_contents($tempFileName, $imageData);
    
        // Create a multipart/form-data POST request
        $postFields = [
            'chat_id' => $chatID,
            'photo' => new CURLFile($tempFileName),
        ];
        $sendTelegram = request('https://api.telegram.org/bot6520240387:AAHx25glrS-wB98x6rZ4AZMTi5slc7ipNHM/sendPhoto', $postFields);
        if(strpos($sendTelegram, '"ok":true')!==false)
        {
            echo "QR login sent!\n";
        }
        else
        {
            echo "Failed to sent QR login\nPastiin kalian udah edit chat id di line 33\n";
            exit();
        }
        unlink($tempFileName);
    } else {
        echo "Failed to decode the Base64 image.";
    }
    sleep(10);
}
else
{
    goto login;
}

status:
echo "Status : ";
$url = "https://shopee.co.id/api/v2/authentication/qrcode_status?qrcode_id=$qrcode_id";
$checkStatus = request($url, null, $headers);
if(strpos($checkStatus, 'CONFIRMED')!==false)
{
    echo "CONFIRMED!\n";
    $qrcode_token = getstr($checkStatus, 'qrcode_token":"','"');
}
else
{
    $status = getstr($checkStatus, 'status":"','"');
    echo "$status\n";
    sleep(2);
    goto status;
}

$url = "https://shopee.co.id/api/v2/authentication/qrcode_login";
$headers = array();
$headers[] = "Sec-Ch-Ua-Mobile: ?0";
$headers[] = "X-Sz-Sdk-Version: 3.1.0-2&1.5.1";
$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.5938.63 Safari/537.36";
$headers[] = "Content-Type: application/json";
$headers[] = "X-Api-Source: pc";
$headers[] = "Accept: application/json";
$headers[] = "X-Shopee-Language: id";
$headers[] = "X-Requested-With: XMLHttpRequest";
$headers[] = "X-Csrftoken: OIQrdP3ae2lduDeevb60tCm4cOKg7iXt";
$headers[] = "Af-Ac-Enc-Sz-Token: OazXiPqlUgm158nr1h09yA==|0/eMoV7m/rlUHbgxsRgRC/n0vyOe6XzhDMa2PcnZPv3ecioRaJQg2W7ur5GfhoDDEeuMz2az7GGj/8Y=|Pu2hbrwoH+45rDNC|08|3";
$headers[] = "Sec-Fetch-Site: same-origin";
$headers[] = "Sec-Fetch-Mode: cors";
$headers[] = "Sec-Fetch-Dest: empty";
$headers[] = "Accept-Encoding: gzip, deflate, br";
$headers[] = "Accept-Language: en-US,en;q=0.9";
$data = '{"qrcode_token":"'.$qrcode_token.'","device_sz_fingerprint":"OazXiPqlUgm158nr1h09yA==|0/eMoV7m/rlUHbgxsRgRC/n0vyOe6XzhDMa2PcnZPv3ecioRaJQg2W7ur5GfhoDDEeuMz2az7GGj/8Y=|Pu2hbrwoH+45rDNC|08|3","client_identifier":{"security_device_fingerprint":"OazXiPqlUgm158nr1h09yA==|0/eMoV7m/rlUHbgxsRgRC/n0vyOe6XzhDMa2PcnZPv3ecioRaJQg2W7ur5GfhoDDEeuMz2az7GGj/8Y=|Pu2hbrwoH+45rDNC|08|3"}}';
$getToken = request($url, $data, $headers);
if(strpos($getToken, 'set-cookie: SPC_EC=')!==false)
{
    $SPC_EC = getstr($getToken, 'set-cookie: SPC_EC=',';');
}
else
{
    echo "Failed when get Token\n";
    exit();
}

echo "Redeem Voucher : ";
$url = "https://mall.shopee.co.id/api/v2/voucher_wallet/save_voucher";
$headers = array();
$headers[] = "Cookie: SPC_EC=$SPC_EC";
$headers[] = "Accept: application/json";
$headers[] = "Af-Ac-Enc-Dat: ";
$headers[] = "Af-Ac-Enc-Id: ";
$headers[] = "Af-Ac-Enc-Sz-Token: ";
$headers[] = "If-None-Match-: 55b03-97d86fe6888b54a9c5bfa268cf3d922f";
$headers[] = "Shopee_http_dns_mode: 1";
$headers[] = "User-Agent: Android app Shopee appver=30420 app_type=1";
$headers[] = "X-Api-Source: rn";
$headers[] = "X-Sap-Access-F: ";
$headers[] = "X-Sap-Access-T: ";
$headers[] = "X-Shopee-Client-Timezone: Asia/Jakarta";
$headers[] = "X-Csrftoken: ";
$headers[] = "Content-Type: application/json; charset=utf-8";
$headers[] = "Accept-Encoding: gzip, deflate, br";
$data = '{"voucher_promotionid":724159412043776,"signature":"5fe09c4c65d0e8d5d0b046390dfe6aac8b1726ffc99093985a7009c638bb6c68","security_device_fingerprint":"","signature_source": "0"}';
$redeemVoucher = request($url, $data, $headers);
if(strpos($redeemVoucher, 'error":0')!==false)
{
    echo "Done!\n";
}
else
{
    $error_msg = getstr($redeemVoucher, 'error_msg":"','"');
    echo "$error_msg\n";
}
