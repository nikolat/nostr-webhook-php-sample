<?php
$private_key = 'hex....';
$public_key = 'hex....';

// mention判定
$isMention = false;
$json = file_get_contents('php://input');
if ($json) {
	$data = json_decode($json, true);
	if (array_key_exists('tags', $data) && count($data['tags']) > 0) {
		foreach ($data['tags'] as $tag) {
			if ($tag[0] == 'p') {
				if ($tag[1] == $public_key) {
					$isMention = true;
					break;
				}
			}
		}
	}
}

//投稿作成
require_once 'vendor/autoload.php';
use Mdanter\Ecc\Crypto\Signature\SchnorrSignature;

$created_at = time() + 1;//1秒遅らせるのはマナーです
$kind = 1;
$tags = [];
if ($isMention) {//mentionに対してはmentionで返す
	$tags = [['p', $data['pubkey']], ['e', $data['id']]];
}
$content = 'えんいー';//とりあえず固定
$array = [0, $public_key, $created_at, $kind, $tags, $content];
$hash_content = json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$id = hash('sha256', $hash_content);
$sign = new SchnorrSignature();
$signature = $sign->sign($private_key, $id);
$sig = $signature['signature'];
$event = array(
	'id' => $id,
	'pubkey' => $public_key,
	'created_at' => $created_at,
	'kind' => $kind,
	'tags' => $tags,
	'content' => $content,
	'sig' => $sig
);
$jsonStr = json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

//出力
header('Content-Type: application/json; charset=utf-8');
echo $jsonStr;

/* https://github.com/public-square/phpecc
MIT License

Copyright (c) 2022 Ology Newswire, Inc

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
