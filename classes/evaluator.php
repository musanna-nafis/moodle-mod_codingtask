<?php
namespace mod_codingtask;

class evaluator {
    public static function run_code($code, $input = '',$language = 'cpp') {
        $url = 'https://emkc.org/api/v2/piston/execute';

        $language_map = [
            'cpp' => ['lang' => 'cpp', 'filename' => 'main.cpp'],
            'python' => ['lang' => 'python3', 'filename' => 'main.py'],
            'php' => ['lang' => 'php', 'filename' => 'index.php'],
            'csharp' => ['lang' => 'csharp', 'filename' => 'Program.cs'],
            'java' => ['lang' => 'java', 'filename' => 'Main.java'],
        ];

        if (!array_key_exists($language, $language_map)) {
            return ['stderr' => 'Unsupported language: ' . $language];
        }

        $lang_info = $language_map[$language];

        $postData = [
            'language' => $lang_info['lang'],
            'version' => '*',
            'files' => [
                [
                    'name' => $lang_info['filename'],
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
        return $result;
    }
}
