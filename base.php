<?php
require_once 'vendor/autoload.php';
require_once './config.php';
require_once './talk.php';
use Mdanter\Ecc\Crypto\Signature\SchnorrSignature;

function makeJson($mode) {
	// mention判定
	$isMention = false;
	$rootTag = null;
	$json = file_get_contents('php://input');
	$data = null;
	if ($json) {
		$data = json_decode($json, true);
		if ($data['pubkey'] == PUBLIC_KEY) {//自分自身の投稿には反応しない
			header('Content-Type: application/json; charset=utf-8');
			return '{}';
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
	$created_at = time() + 1;//1秒遅らせるのはマナーです
	$kind = 1;
	$tags = [];
	$content = 'えんいー';
	if ($mode == 'mention' && $isMention) {
		//mentionに対してはmentionで返す
		if ($rootTag) {
			$tags = [['p', $data['pubkey'], ''], $rootTag, ['e', $data['id'], '', 'reply']];
		}
		else {
			$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', 'root']];
		}
		//返答を作成
		$content = talk($data['content']);
	}
	else if ($mode == 'airrep' && !$isMention && $data) {
		//エアリプ
		$content = airrep($data['content']);
	}
	else if ($mode == 'fav' && $data) {
		//ふぁぼ
		$kind = 7;
		$content = '⭐';
		$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', '']];
	}
	else {
		return '{}';
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
	return $jsonStr;
}
