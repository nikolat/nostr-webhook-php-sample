<?php
require_once './config.php';
// mention判定
$isMention = false;
$rootTag = null;
$json = file_get_contents('php://input');
if ($json) {
	$data = json_decode($json, true);
	if ($data['pubkey'] == PUBLIC_KEY) {//自分自身の投稿には反応しない
		header('Content-Type: application/json; charset=utf-8');
		exit('{}');
	}
	foreach ($data['tags'] as $tag) {
		if ($tag[0] == 'p' && $tag[1] == PUBLIC_KEY) {
			$isMention = true;
		}
		else if ($tag[0] == 'e' && array_key_exists(3, $tag)) {
			if ($tag[3] == 'root') {
				$rootTag = $tag;
			}
		}
	}
}

//投稿作成
require_once 'vendor/autoload.php';
use Mdanter\Ecc\Crypto\Signature\SchnorrSignature;
require_once './talk.php';

$created_at = time() + 1;//1秒遅らせるのはマナーです
$kind = 1;
$tags = [];
$content = 'えんいー';//mention以外はこれ固定
if ($isMention) {
	//mentionに対してはmentionで返す
	if ($rootTag) {
		$tags = [['p', $data['pubkey']], $rootTag, ['e', $data['id'], '', 'reply']];
	}
	else {
		$tags = [['p', $data['pubkey']], ['e', $data['id'], '', 'root']];
	}
	//返答を作成
	$content = talk($data['content']);
}

$array = [0, PUBLIC_KEY, $created_at, $kind, $tags, $content];
$hash_content = json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$id = hash('sha256', $hash_content);
$sign = new SchnorrSignature();
$signature = $sign->sign(PRIVATE_KEY, $id);
$sig = $signature['signature'];
$event = array(
	'id' => $id,
	'pubkey' => PUBLIC_KEY,
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
