<?php

namespace App;

class Privnote
{
    public function note($text)
    {
        $password = $this->gen_pwd();
        $data = $this->encrypt_data($text, $password);

        $curl = curl_init('https://privnote.com/legacy/');

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'data' => $data,
                'has_manual_pass' => false,
                'duration_hours' => 0,
                'dont_ask' => false,
                'data_type' => 'T',
                'notify_email' => '',
                'notify_ref' => ''
            ]),
            CURLOPT_HTTPHEADER => [
                'Host: privnote.com',
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:123.0) Gecko/20100101 Firefox/123.0',
                'Accept: */*',
                'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                // 'Accept-Encoding: gzip, deflate, br',
                'Referer: https://privnote.com/',
                'Content-type: application/x-www-form-urlencoded',
                'X-Requested-With: XMLHttpRequest',
                'Origin: https://privnote.com',
                'Connection: keep-alive',
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: same-origin',
                'TE: trailers'
            ]
        ]);

        $json = curl_exec($curl);

        dump($json);

        $json = json_decode($json, true);

        return $json['note_link'];
    }

    private function gen_pwd()
    {
        return substr(base64_encode(openssl_random_pseudo_bytes('32')), 0, 9);
    }

    private function encrypt_data($message, $passphrase)
    {
        $salt = openssl_random_pseudo_bytes(8);
        $key = $dx = openssl_digest($passphrase . $salt, 'md5', true);
        $dx = openssl_digest($dx . $passphrase . $salt, 'md5', true);
        $key .= $dx;
        $iv = openssl_digest($dx . $passphrase . $salt, 'md5', true);
        return base64_encode('Salted__' . $salt . openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv));
    }
}
