<?php
require_once 'vendor/autoload.php';
require_once './config.php';
require_once './talk.php';
use Mdanter\Ecc\Crypto\Signature\SchnorrSignature;
use function BitWasp\Bech32\convertBits;
use function BitWasp\Bech32\encode;

function makeJson($mode) {
	// mention判定
	$isMention = false;
	$isMentionOther = false;
	$mentionOtherTag = null;
	$rootTag = null;
	$emojiTags = [];
	$json = file_get_contents('php://input');
	$data = null;
	if ($json) {
		$data = json_decode($json, true);
		if ($data['pubkey'] == PUBLIC_KEY) {//自分自身の投稿には反応しない
			header('Content-Type: application/json; charset=utf-8');
			return '{}';
		}
		foreach ($data['tags'] as $tag) {
			if ($tag[0] == 'p') {
				if ($tag[1] == PUBLIC_KEY) {
					$isMention = true;
				}
				else {
					$isMentionOther = true;
					$mentionOtherTag = $tag;
				}
			}
			else if ($tag[0] == 'e' && array_key_exists(3, $tag)) {
				if ($tag[3] == 'root') {
					$rootTag = $tag;
				}
			}
			else if ($tag[0] == 'emoji') {
				$emojiTags[] = $tag;
			}
		}
	}
	
	//投稿作成
	$created_at = time() + 1;//1秒遅らせるのはマナーです
	$kind = 1;
	$tags = null;
	$content = null;
	if ($mode == 'mention' && $isMention) {
		//返答を作成
		[$content, $tags] = talk($data, $emojiTags, $rootTag, $isMentionOther, $mentionOtherTag);
	}
	else if ($mode == 'airrep' && !$isMention && !$isMentionOther && $data) {
		//エアリプ
		[$content, $tags] = airrep($data, $emojiTags);
	}
	else if ($mode == 'fav' && $data) {
		//ふぁぼ
		$kind = 7;
		[$content, $tags] = fav($data);
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

function npubEncode($hex)
{
	return encodeBytes('npub', $hex);
}
function noteEncode($hex)
{
	return encodeBytes('note', $hex);
}
function encodeBytes($prefix, $hex)
{
	$data = hex2ByteArray($hex);
	$words = convertBits($data, count($data), 8, 5);
	return encode($prefix, $words);
}
function hex2ByteArray($hexString) {
	$string = hex2bin($hexString);
	$ary = unpack('C*', $string);
	return array_slice($ary, 0, count($ary));
}
