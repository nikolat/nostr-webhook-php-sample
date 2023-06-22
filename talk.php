<?php
function talk($content) {
	$res = 'えんいー';
	if (preg_match('/占って|占い/', $content)) {
		$types = array('牡羊座', '牡牛座', '双子座', '蟹座', '獅子座', '乙女座', '天秤座', '蠍座', '射手座', '山羊座', '水瓶座', '魚座', 'A型', 'B型', 'O型', 'AB型'
			, '寂しがりや', '独りぼっち', '社畜', '営業職', '接客業', '自営業', '世界最強', '石油王', '海賊王', '次期総理', '駆け出しエンジニア', '神絵師', 'ノス廃'
			, 'マナー講師', 'インフルエンサー', '一般の主婦', 'ビットコイナー', 'ブロッコリー農家', 'スーパーハカー', 'ふぁぼ魔', '歩くNIP', 'きのこ派', 'たけのこ派'
		);
		$stars = array('★★★★★', '★★★★☆', '★★★☆☆', '★★☆☆☆', '★☆☆☆☆', '大吉', '中吉', '小吉', '吉', '末吉', '凶', '大凶'
			, '🍆🍆🍆🍆🍆', '🥦🥦🥦🥦🥦', '🍅🍅🍅🍅🍅', '🚀🚀🚀🚀🚀', '📃📃📃📃📃', '🐧🐧🐧🐧🐧', '👍👍👍👍👍', '💪💪💪💪💪'
		);
		$url = 'http://buynowforsale.shillest.net/ghosts/ghosts/index.rss';
		$rss = simplexml_load_file($url);
		$index = mt_rand(0, $rss->channel->item->count() - 1);
		$res = $types[rand(0, count($types) - 1)]. 'のあなたの今日の運勢は『'. $stars[rand(0, count($stars) - 1)]. "』\n";
		$res .= 'ラッキーゴーストは『'. $rss->channel->item[$index]->title. '』やで'. "\n";
		$res .= $rss->channel->item[$index]->link;
	}
	else if (preg_match('/おすすめ|オススメ|お勧め|お薦め/', $content)) {
		$url = 'http://buynowforsale.shillest.net/ghosts/ghosts/index.rss';
		$rss = simplexml_load_file($url);
		$index = mt_rand(0, $rss->channel->item->count() - 1);
		$res = $rss->channel->item[$index]->title. "\n".$rss->channel->item[$index]->link;
	}
	else if (preg_match('/doc/i', $content)) {
		$res = 'http://ssp.shillest.net/ukadoc/manual/';
	}
	else if (preg_match('/yaya/i', $content)) {
		$res = 'https://emily.shillest.net/ayaya/';
	}
	else if (preg_match('/里々|satori/i', $content)) {
		$res = 'https://soliton.sub.jp/satori/';
	}
	else if (preg_match('/ソース|コード|GitHub|リポジトリ|中身/i', $content)) {
		$res = 'https://github.com/nikolat/nostr-webhook-php-sample';
	}
	else if (preg_match('/時刻|時報/', $content)) {
		$date = new DateTime('now');
		$week = array('日', '月', '火', '水', '木', '金', '土');
		$weekday = $date->format('w');
		$res = $date->format('Y年m月d日 H時i分s秒 ').$week[$weekday].'曜日やで';
	}
	else if (preg_match('/ログボ|ログインボーナス/', $content)) {
		$mesary = array('ログボとかあらへん', '継続は力やな', '今日もログインしてえらいやで');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/いいの?か?(？|\?)$/u', $content)) {
		$mesary = array('ええで', 'ええんやで', 'あかんに決まっとるやろ');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/(^|\s+)(\S+)の(週間)?天気/u', $content, $match)) {
		//$url = 'http://www.jma.go.jp/bosai/common/const/area.json';
		$json = file_get_contents(__DIR__. '/area.json');//そうそう変わらんやろ
		$jsonar = json_decode($json, true);
		$code = false;
		foreach ($jsonar['offices'] as $key => $value) {
			if (strpos($value['name'], $match[2]) !== false) {
				$code = $key;
				break;
			}
		}
		if (!$code) {
			foreach (array_merge($jsonar['class20s'], $jsonar['class15s'], $jsonar['class10s']) as $key => $value) {
				if (strpos($value['name'], $match[2]) !== false) {
					$code = substr($value['parent'], 0, -3). '000';//3桁目がある都市もあるのでもっと真面目にやるべき
					break;
				}
			}
		}
		if (!$code) {
			$mesary = array('どこやねん', '知らんがな');
			return $mesary[rand(0, count($mesary) - 1)];
		}
		if (array_key_exists(3, $match)) {
			$baseurl = 'https://www.jma.go.jp/bosai/forecast/data/overview_week/';
		}
		else {
			$baseurl = 'https://www.jma.go.jp/bosai/forecast/data/overview_forecast/';
		}
		$url = $baseurl. $code. '.json';
		$context = stream_context_create();
		stream_context_set_option($context, 'http', 'ignore_errors', true);
		$json = file_get_contents($url, false, $context);
		$jsonar = json_decode($json, true);
		if (!$jsonar) {
			if ($match[3] == '週間') {
				return 'そんな先のこと気にせんでええ';
			}
			else {
				return 'そんな田舎の天気なんか知らんで';
			}
		}
		$res = $jsonar['text'];
		$res = str_replace('\n', "\n", $res);
		$res .= "\n\n（※出典：気象庁ホームページ）";
	}
	else if (preg_match('/(？|\?)$/', $content)) {
		$mesary = array('ワイに聞かれても', '知らんて', 'せやな');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/えんいー/', $content)) {
		$mesary = array('ほい、えんいー', 'ほな、またな', 'おつかれ');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	return $res;
}
function airrep($content) {
	$res = 'えんいー';
	if (preg_match('/いいの?か?(？|\?)$/u', $content)) {
		$mesary = array('ええで', 'ええんやで', 'あかんに決まっとるやろ');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/(ほめて|褒めて).?$/u', $content)) {
		$mesary = array('えらいやで', '偉業やで', 'すごいやん');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/[行い]っ?てきます.?$/u', $content)) {
		$mesary = array('気いつけてな', 'いてら', 'お土産よろしゅう');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/^みんな(.*)(て|で)は?る$/u', $content, $match)) {
		$res = $match[1]. $match[2].'へんのお前だけや';
	}
	else if (preg_match('/^みんな(.*)(て|で)へん$/u', $content, $match)) {
		$res = $match[1]. $match[2]. 'んのお前だけや';
	}
	return $res;
}
