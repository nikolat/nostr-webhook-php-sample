<?php
function talk($content) {
	$res = 'えんいー';
	if (preg_match('/おすすめ|オススメ|お勧め|お薦め/', $content)) {
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
	else if (preg_match('/時刻|時報/', $content)) {
		$date = new DateTime('now');
		$week = array('日', '月', '火', '水', '木', '金', '土');
		$weekday = $date->format('w');
		$res = $date->format('Y年m月d日 H時i分s秒 ').$week[$weekday].'曜日やで';
	}
	return $res;
}
