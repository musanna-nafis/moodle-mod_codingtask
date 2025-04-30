<?php
namespace mod_codingtask;

class evaluator {
    public static function run_code($code, $input = '') {
        $url = 'https://emkc.org/api/v2/piston/execute';

        $postData = [
            'language' => 'cpp',
            'version' => '*',
            'files' => [
                [
                    'name' => 'main.cpp',
                    'content' => $code
                ]
            ],
            'stdin' => $input
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return ['stderr' => 'Curl error: ' . curl_error($ch)];
        }

        curl_close($ch);

        $result = json_decode($response, true);

        // Debug log (for development, can be removed later)
        // file_put_contents(__DIR__ . '/debug_log.txt', print_r($result, true));

        return $result;
    }
}
