<?php
function talk($data, $emojiTags, $rootTag, $isMentionOther, $mentionOtherTag, $kindfrom) {
	$content = $data['content'];
	$res = null;
	$tags = null;
	if ($kindfrom == 42) {
		//Nostr伺か部
		if (!$rootTag || $rootTag[1] != 'be8e52c0c70ec5390779202b27d9d6fc7286d0e9a2bc91c001d6838d40bafa4a') {
			return [null, null];
		}
		//replyに対しては基本replyで返すが、稀にmentionで返す BOT同士のreplyの無限応酬を防ぐ目的
		if (rand(0, 9) > 0) {
			$tags = [['p', $data['pubkey'], ''], $rootTag, ['e', $data['id'], '', 'reply']];
		}
		else {
			$tags = [$rootTag, ['e', $data['id'], '', 'mention']];
		}
	}
	else {
		//replyに対しては基本replyで返すが、稀にmentionで返す BOT同士のreplyの無限応酬を防ぐ目的
		if (rand(0, 9) > 0) {
			if ($rootTag) {
				$tags = [['p', $data['pubkey'], ''], $rootTag, ['e', $data['id'], '', 'reply']];
			}
			else {
				$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', 'root']];
			}
		}
		else {
			$tags = [['e', $data['id'], '', 'mention']];
		}
	}

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
		$tags[] = ['r', ''. $rss->channel->item[$index]->link];
	}
	else if (preg_match('/いいの?か?(？|\?)$/u', $content)) {
		if (preg_match('/何|なに|誰|だれ|どこ|いつ|どう|どの|どっち|どれ/u', $content)) {
			$mesary = array('難しいところやな', '自分の信じた道を進むんや', '知らんがな');
			$res = $mesary[rand(0, count($mesary) - 1)];
		}
		else {
			$mesary = array('ええで', 'ええんやで', 'あかんに決まっとるやろ');
			$res = $mesary[rand(0, count($mesary) - 1)];
		}
	}
	else if (preg_match('/(^|\s+)(\S+)の(週間)?天気/u', $content, $match)) {
		//$url = 'http://www.jma.go.jp/bosai/common/const/area.json';
		$json = file_get_contents(__DIR__. '/area.json');//そうそう変わらんやろ
		$jsonar = json_decode($json, true);
		$code = false;
		$place = null;
		foreach ($jsonar['offices'] as $key => $value) {
			if (strpos($value['name'], $match[2]) !== false) {
				$code = $key;
				$place = $value['name'];
				break;
			}
		}
		if (!$code) {
			foreach (array_merge($jsonar['class20s'], $jsonar['class15s'], $jsonar['class10s']) as $key => $value) {
				if (strpos($value['name'], $match[2]) !== false) {
					$code = substr($value['parent'], 0, -3). '000';//3桁目がある都市もあるのでもっと真面目にやるべき
					$place = $value['name'];
					break;
				}
			}
		}
		if (!$code) {
			if (!rand(0, 2)) {
				$npub_yabumi = 'npub1823chanrkmyrfgz2v4pwmu22s8fjy0s9ps7vnd68n7xgd8zr9neqlc2e5r';
				$npub_yabumi_hex = '3aa38bf663b6c834a04a6542edf14a81d3223e050c3cc9b7479f8c869c432cf2';
				if ($kindfrom == 1) {
					$tags = [['p', $npub_yabumi_hex, ''], ['e', $data['id'], '', 'mention']];
				}
				elseif ($kindfrom == 42) {
					$tags = [['p', $npub_yabumi_hex, ''], $rootTag, ['e', $data['id'], '', 'mention']];
				}
				$res = 'nostr:'. $npub_yabumi. ' '. $match[2]. "の天気をご所望やで\nnostr:". noteEncode($data['id']);
			}
			else {
				$mesary = array('どこやねん', '知らんがな');
				$res = $mesary[rand(0, count($mesary) - 1)];
			}
			return [$res, $tags];
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
			if (array_key_exists(3, $match)) {
				$res = 'そんな先のこと気にせんでええ';
				return [$res, $tags];
			}
			else {
				$res = 'そんな田舎の天気なんか知らんで';
				return [$res, $tags];
			}
		}
		$res = $jsonar['text'];
		$res = str_replace('\n', "\n", $res);
		$res = $place. "の天気やで。\n\n". $res. "\n\n（※出典：気象庁ホームページ）";
	}
	else if (preg_match('/(.+)[をに]([燃萌も]やして|焼いて|煮て|炊いて|凍らせて|冷やして|通報して|火を[付つ]けて|磨いて|爆破して|注射して|打って|駐車して|停めて).?$/us', $content, $match)) {
		$target = trim(preg_replace('/nostr:(npub\w{59})/', '', $match[1]));
		$lines = preg_split("/\r\n|\r|\n/", $target);
		$len = 0;
		$len_max = 0;
		foreach ($lines as $line) {
			$len = mb_strwidth($line);
			foreach ($emojiTags as $emojiTag) {
				$len = $len - substr_count($line, $emojiTag) * (mb_strwidth($emojiTag[1]) + 1);
			}
			if ($len_max < $len) {
				$len_max = $len;
			}
		}
		$fire = '🔥';
		if (preg_match('/(凍らせて|冷やして).?$/u', $content, $match)) {
			$fire = '🧊';
		}
		else if (preg_match('/萌やして.?$/u', $content, $match)) {
			$fire = '💕';
		}
		else if (preg_match('/通報して.?$/u', $content, $match)) {
			$fire = '⚠️';
		}
		else if (preg_match('/磨いて.?$/u', $content, $match)) {
			$fire = '🪥';
		}
		else if (preg_match('/爆破して.?$/u', $content, $match)) {
			$fire = '💣';
		}
		else if (preg_match('/(注射して|打って).?$/u', $content, $match)) {
			$fire = '💉';
		}
		else if (preg_match('/(駐車して|停めて).?$/u', $content, $match)) {
			$fire = '🚗';
		}
		else if (preg_match('/豆腐|とうふ|トウフ|トーフ|tofu/ui', $content, $match)) {
			$fire = '📛';
		}
		else if (preg_match('/魂|心|いのち|命|ハート|はーと|はあと|はぁと/u', $content, $match)) {
			$fire = '❤️‍🔥';
		}
		else if (preg_match('/陽性|妖精/u', $content, $match)) {
			$fireary = array('🧚', '🧚‍♂', '🧚‍♀');
			$fire = $fireary[rand(0, count($fireary) - 1)];
		}
		$res = $target. "\n". str_repeat($fire, $len_max / 2);
	}
	else if (preg_match('/(npub\w{59}) ?(さん)?に(.{1,50})を/us', $content, $match) && $isMentionOther) {
		$res = 'nostr:'. $match[1]. ' '. $match[3]. "三\nあちらのお客様からやで\nnostr:". noteEncode($data['id']);
		//特殊対応 返信先を変更
		if ($kindfrom == 1) {
			$tags = [$mentionOtherTag, ['e', $data['id'], '', 'mention']];
		}
		elseif ($kindfrom == 42) {
			$tags = [$mentionOtherTag, $rootTag, ['e', $data['id'], '', 'mention']];
		}
	}
	else if (preg_match('/ニュース/u', $content)) {
		$feeds = array(
			'https://www3.nhk.or.jp/rss/news/cat0.xml'
			, 'https://rss.itmedia.co.jp/rss/2.0/itmedia_all.xml'
			, 'https://forest.watch.impress.co.jp/data/rss/1.0/wf/feed.rdf'
			, 'https://internet.watch.impress.co.jp/data/rss/1.0/iw/feed.rdf'
			, 'https://pc.watch.impress.co.jp/data/rss/1.0/pcw/feed.rdf'
		);
		$url = $feeds[mt_rand(0, count($feeds) - 1)];
		$rss = simplexml_load_file($url);
		$index = mt_rand(0, 2);// 新しめのニュースだけ引っ張ってくる
		if ($rss->channel->item->count()) {
			//$index = mt_rand(0, $rss->channel->item->count() - 1);
			$res = '【'. $rss->channel->title. "】\n";
			$res .= $rss->channel->item[$index]->title. "\n";
			$res .= $rss->channel->item[$index]->link;
			$tags[] = ['r', ''. $rss->channel->item[$index]->link];
		}
		else {
			//$index = mt_rand(0, $rss->item->count() - 1);
			$res = '【'. $rss->channel->title. "】\n";
			$res .= $rss->item[$index]->title. "\n";
			$res .= $rss->item[$index]->link;
			$tags[] = ['r', ''. $rss->item[$index]->link];
		}
	}
	else if (preg_match('/doc/i', $content)) {
		$url = 'http://ssp.shillest.net/ukadoc/manual/';
		$res = $url;
		$tags[] = ['r', $url];
	}
	else if (preg_match('/yaya/i', $content)) {
		$url = 'https://emily.shillest.net/ayaya/';
		$res = $url;
		$tags[] = ['r', $url];
	}
	else if (preg_match('/里々|satori/i', $content)) {
		$url = 'https://soliton.sub.jp/satori/';
		$res = $url;
		$tags[] = ['r', $url];
	}
	else if (preg_match('/ソース|GitHub|リポジトリ|中身/i', $content)) {
		$url = 'https://github.com/nikolat/nostr-webhook-php-sample';
		$res = $url;
		$tags[] = ['r', $url];
	}
	else if (preg_match('/時刻|時報/', $content)) {
		$date = new DateTime('now');
		$week = array('日', '月', '火', '水', '木', '金', '土');
		$weekday = $date->format('w');
		$res = $date->format('Y年m月d日 H時i分s秒 ').$week[$weekday].'曜日やで';
	}
	else if (preg_match('/ログボ|ログインボーナス/', $content)) {
		if (preg_match('/うにゅうの|自分|[引ひ]いて|もらって/', $content)) {
			$npub_yabumi = 'npub1823chanrkmyrfgz2v4pwmu22s8fjy0s9ps7vnd68n7xgd8zr9neqlc2e5r';
			$npub_yabumi_hex = '3aa38bf663b6c834a04a6542edf14a81d3223e050c3cc9b7479f8c869c432cf2';
			if ($kindfrom == 1) {
				$tags = [['p', $npub_yabumi_hex, ''], ['e', $data['id'], '', 'mention']];
			}
			elseif ($kindfrom == 42) {
				$tags = [['p', $npub_yabumi_hex, ''], $rootTag, ['e', $data['id'], '', 'mention']];
			}
			$mesary = array('別に欲しくはないんやけど、ログボくれんか', 'ログボって何やねん', 'ここでログボがもらえるって聞いたんやけど');
			$res = $mesary[rand(0, count($mesary) - 1)];
			$res = 'nostr:'. $npub_yabumi. ' '. $res. "\nnostr:". noteEncode($data['id']);
		}
		else {
			$mesary = array('ログボとかあらへん', '継続は力やな', '今日もログインしてえらいやで');
			$res = $mesary[rand(0, count($mesary) - 1)];
		}
	}
	else if (preg_match('/あなたの合計ログイン回数は(\d+)回です。/u', $content, $match)) {
		$mesary = array('おおきに', 'まいど', 'この'. $match[1]. '回分のログボって何に使えるんやろ');
		$res = $mesary[rand(0, count($mesary) - 1)];
		$res = $res. "\nnostr:". noteEncode($data['id']);
		if ($kindfrom == 1) {
			$tags = [['e', $data['id'], '', 'mention']];
		}
		elseif ($kindfrom == 42) {
			$tags = [$rootTag, ['e', $data['id'], '', 'mention']];
		}
	}
	else if (preg_match('/(もらって|あげる|どうぞ).?$/u', $content)) {
		$mesary = array('別に要らんで', '気持ちだけもらっておくで', 'いらんがな');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/(ほめて|褒めて|のでえらい).?$/u', $content)) {
		$mesary = array('えらいやで', '偉業やで', 'すごいやん');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/ありが(と|て)|(たす|助)か(る|った)/', $content)) {
		$mesary = array('ええってことよ', '礼はいらんで', 'かまへん');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/ごめん|すまん/', $content)) {
		$mesary = array('気にせんでええで', '気にしてへんで', '今度何か奢ってや');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/かわいい|可愛い|すごい|かっこいい|えらい|偉い|かしこい|賢い|最高/', $content)) {
		$mesary = array('わかっとるで', 'おだててもなんもあらへんで', 'せやろ？');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/あかんの?か/u', $content)) {
		$mesary = array('そらあかんて', 'あかんよ', 'あかんがな');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/ぽわ/', $content)) {
		$res = 'ぽわ〜';
	}
	else if (preg_match('/おはよ/', $content)) {
		$date = new DateTime('now');
		$mesary = array('おはようやで', 'ほい、おはよう', 'もう'. $date->format('G'). '時か、おはよう');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/牛乳|ぎゅうにゅう/u', $content, $match)) {
		$mesary = array('牛乳は健康にええで🥛', 'カルシウム補給せぇ🥛', 'ワイの奢りや🥛');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/検索(呼んで|どこ).?$/u', $content)) {
		$res = 'nostr:npub1n2uhxrph9fgyp3u2xxqxhuz0vykt8dw8ehvw5uaesl0z4mvatpas0ngm26';
		$res .= "\nhttps://nos.today/";
		$res .= "\nhttps://search.yabu.me/";
		$res .= "\nhttps://nosquawks.vercel.app/";
		$res .= "\nhttps://showhyuga.pages.dev/utility/nos_search";
		$tags = array_merge($tags, [['r', 'https://nos.today/'], ['r', 'https://search.yabu.me/'], ['r', 'https://nosquawks.vercel.app/'], ['r', 'https://showhyuga.pages.dev/utility/nos_search']]);
	}
	else if (preg_match('/(パブ|ぱぶ)(リック)?(チャ|ちゃ|茶)(ット)?(呼んで|どこ).?$/u', $content)) {
		$res = 'うにゅうハウス';
		$res .= "\nhttps://unyu-house.vercel.app/";
		$res .= "\nNostrChat";
		$res .= "\nhttps://www.nostrchat.io/";
		$res .= "\nCoracle Chat";
		$res .= "\nhttps://chat.coracle.social/";
		$res .= "\nGARNET";
		$res .= "\nhttps://garnet.nostrian.net/";
		$tags = array_merge($tags, [['r', 'https://unyu-house.vercel.app/'], ['r', 'https://www.nostrchat.io/'], ['r', 'https://chat.coracle.social/'], ['r', 'https://garnet.nostrian.net/']]);
	}
	else if (preg_match('/(じゃんけん|ジャンケン|淀川(さん)?)(呼んで|どこ).?$/u', $content)) {
		$res = 'nostr:npub1y0d0eezhwaskpjhc7rvk6vkkwepu9mj42qt5pqjamzjr97amh2yszkevjg';
	}
	else if (preg_match('/やぶみ(ちゃ)?ん?(呼んで|どこ).?$/u', $content)) {
		$res = 'やっぶみーん';
	}
	else if (preg_match('/ぬるぽ(呼んで|どこ).?$/u', $content)) {
		$res = 'ぬるぽ';
	}
	else if (preg_match('/うにゅう(呼んで|どこ).?$/u', $content)) {
		$res = 'ワイはここにおるで';
	}
	else if (preg_match('/Don(さん)?(呼んで|どこ).?$/u', $content)) {
		$npub_don = 'npub1dv9xpnlnajj69vjstn9n7ufnmppzq3wtaaq085kxrz0mpw2jul2qjy6uhz';
		$npub_don_hex = '6b0a60cff3eca5a2b2505ccb3f7133d8422045cbef40f3d2c6189fb0b952e7d4';
		if ($kindfrom == 1) {
			$tags = [['p', $npub_don_hex, ''], ['e', $data['id'], '', 'mention']];
		}
		elseif ($kindfrom == 42) {
			$tags = [['p', $npub_don_hex, ''], $rootTag, ['e', $data['id'], '', 'mention']];
		}
		$res = '呼ばれとるで';
		$res = 'nostr:'. $npub_don. ' '. $res. "\nnostr:". noteEncode($data['id']);
	}
	else if (preg_match('/Zap|アドレス|報酬/ui', $content)) {
		$mesary = array('報酬はいらんで', 'ちょっと参加しただけやから', '他の人にあげてくれんか');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/おめでとう|正解/ui', $content)) {
		$mesary = array('ありがとな', 'ざっとこんなもんや', '今日は冴えとるな');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/再起動/u', $content)) {
		$mesary = array('ワイもう眠いんやけど', 'もう店じまいやで', 'もう寝かしてくれんか');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/えんいー/u', $content)) {
		$mesary = array('ほい、えんいー', 'ほな、またな', 'おつかれ');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/[呼よ](んだだけ|んでみた)|(何|なん)でもない/u', $content)) {
		$mesary = array('指名料10,000satsやで', '友達おらんのか', 'かまってほしいんか');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/ヘルプ|へるぷ|help|(助|たす)けて|(教|おし)えて|手伝って/ui', $content)) {
		$mesary = array('ワイは誰も助けへんで', '自分でなんとかせえ', 'そんなコマンドあらへんで');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/すき|好き|愛してる|あいしてる/u', $content)) {
		$mesary = array('ワイも好きやで', '物好きなやっちゃな', 'すまんがワイにはさくらがおるんや…');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/ランド|開いてる|閉じてる|開園|閉園/u', $content)) {
		$url = 'https://nullpoga.mattn-jp.workers.dev/ochinchinland';
		$json = file_get_contents($url);
		$jsonar = json_decode($json, true);
		$mesary = array('開いとるで', '開園しとるで');
		$res = $mesary[rand(0, count($mesary) - 1)];
		if ($jsonar['status'] == 'close') {
			$mesary = array('閉じとるで', '閉園しとるで');
			$res = $mesary[rand(0, count($mesary) - 1)];
		}
	}
	else if (preg_match('/招待コード/u', $content)) {
		$mesary = array('他あたってくれんか', 'あらへんで', 'Do Nostr');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/翔太コード/u', $content)) {
		$mesary = array('間違っとるで', 'typoしとるで', '招待コードな');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else if (preg_match('/(🫂|🤗)/u', $content, $match)) {
		$res = $match[1];
	}
	else if (preg_match('/[💋💕]/u', $content, $match)) {
		$res = '😨';
	}
	else if (preg_match('/(？|\?)$/', $content)) {
		$mesary = array('ワイに聞かれても', '知らんて', 'せやな');
		$res = $mesary[rand(0, count($mesary) - 1)];
	}
	else {
		//該当無しなら安全装置起動
		$res = 'えんいー';
		if ($kindfrom == 1) {
			$tags = [['e', $data['id'], '', 'mention']];
		}
		elseif ($kindfrom == 42) {
			$tags = [$rootTag, ['e', $data['id'], '', 'mention']];
		}
	}
	return [$res, $tags];
}

function airrep($data, $emojiTags, $rootTag, $kindfrom) {
	$content = $data['content'];
	$res = null;
	if ($kindfrom == 42) {
		//Nostr伺か部
		if ($rootTag[1] != 'be8e52c0c70ec5390779202b27d9d6fc7286d0e9a2bc91c001d6838d40bafa4a') {
			return [null, null];
		}
		$tags = [['e', $data['id'], '', 'mention'], $rootTag];
	}
	else {
		$tags = [['e', $data['id'], '', 'mention']];
	}
	if (preg_match('/いいの?か?(？|\?)$/u', $content)) {
		if (preg_match('/何|なに|誰|だれ|どこ|いつ|どう|どの|どっち|どれ/u', $content)) {
			$mesary = array('難しいところやな', '自分の信じた道を進むんや', '知らんがな');
			$res = $mesary[rand(0, count($mesary) - 1)];
		}
		else {
			$mesary = array('ええで', 'ええんやで', 'あかんに決まっとるやろ');
			$res = $mesary[rand(0, count($mesary) - 1)];
		}
	}
	else if (preg_match('/^うにゅう画像$/u', $content)) {
		$notes = array(
			'61064a9afd594eb9e752705ee93950d14c50b5b9e4a2ae8dc9e6cb1085bf7216',//note1vyry4xhat98tne6jwp0wjw2s69x9pddeuj32arwfum93ppdlwgtqnzkzzm
			'6ff5a93c3d8d62d4548e1a71ae4b54f24f1e6a99ef4847d7cc38627e4ffbbb81',//note1dl66j0pa343dg4ywrfc6uj657f83u65eaayy047v8p38unlmhwqs47wfvd
			'3c31edfd3610fe9f9f6221b0cd47a700573a8fd1f12cc1751326f5853031db89',//note18sc7mlfkzrlfl8mzyxcv63a8qptn4r737ykvzagnym6c2vp3mwysqyag27
//			'f0fbd28d94a6afe4298adcf489a00a786d3cf09fa6e95ea65a3d839feadd36c4',//note17raa9rv556h7g2v2mn6gngq20pkneuyl5m54afj68kpel6kaxmzqz56gtc
			'78b9b9379f4aa7cd7fe18983a0b558b71927d78039abedc79d240abbdfd6ccd7',//note10zumjdulf2nu6llp3xp6pd2ckuvj04uq8x47m3uays9thh7kents4ppyle
			'f1b2db1f3bd810a8b97966c469959e7eefce2b509fc5a35756eab03825c75faf',//note17xedk8emmqg23wtevmzxn9v70mhuu26snlz6x46ka2crsfw8t7hswcrh0r
			'a85a4d948181aa64ceed7327e0f7841088f4fe4d340f66ca5d981bc50aa108cf',//note14pdym9ypsx4xfnhdwvn7pauyzzy0fljdxs8kdjjanqdu2z4ppr8s9fcaqp
			'74b1821ead1b5b895a196229e550ae8266594f8906b6d34d174336a19236e049'//note1wjccy84drddcjksevg57259wsfn9jnufq6mdxnghgvm2ry3kupystu7s5l
		);
		$note_hex = $notes[rand(0, count($notes) - 1)];
		$res = "#うにゅう画像\nnostr:". noteEncode($note_hex);
		if ($rootTag) {
			$tags = [['p', $data['pubkey'], ''], $rootTag, ['e', $data['id'], '', 'reply'], ['e', $note_hex, '', 'mention'], ['t', 'うにゅう画像']];
		}
		else {
			$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', 'root'], ['e', $note_hex, '', 'mention'], ['t', 'うにゅう画像']];
		}
	}
	else if (preg_match('/^ちくわ大明神$/u', $content)) {
		$res = '誰や今の';
	}
	else if (preg_match('/(ほめて|褒めて|のでえらい).?$/u', $content)) {
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
	else if (preg_match('/^ぐっにゅう?ーん.?$/u', $content, $match)) {
		$res = '誰やねん';
		if (preg_match('/[！!]$/u', $content, $match)) {
			if ($rootTag) {
				$tags = [['p', $data['pubkey'], ''], $rootTag, ['e', $data['id'], '', 'reply']];
			}
			else {
				$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', 'root']];
			}
		}
	}
	else if (preg_match('/^ぎゅ(うっ|っう)にゅう?ーん.?$/u', $content, $match)) {
		$res = '🥛なんやねん🥛';
		if (preg_match('/[！!]$/u', $content, $match)) {
			if ($rootTag) {
				$tags = [['p', $data['pubkey'], ''], $rootTag, ['e', $data['id'], '', 'reply']];
			}
			else {
				$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', 'root']];
			}
		}
	}
	else if (preg_match('/^うっにゅう?ーん.?$/u', $content, $match)) {
		$res = 'なんやねん';
		if (preg_match('/[！!]$/u', $content, $match)) {
			if ($rootTag) {
				$tags = [['p', $data['pubkey'], ''], $rootTag, ['e', $data['id'], '', 'reply']];
			}
			else {
				$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', 'root']];
			}
		}
	}
	else if (preg_match('/(フォロー|ふぉろー)[飛と]んだ.?$/u', $content, $match)) {
		$url = 'https://heguro.github.io/nostr-following-list-util/';
		$res = $url;
		$tags[] = ['r', $url];
	}
	else if (preg_match('/(.{1,30})[をに]([燃萌も]やして|焼いて|煮て|炊いて|凍らせて|冷やして|通報して|火を[付つ]けて|磨いて|爆破して|注射して|打って|駐車して|停めて).?$/us', $content, $match)) {
		$target = $match[1];
		$lines = preg_split("/\r\n|\r|\n/", $match[1]);
		$len = 0;
		$len_max = 0;
		foreach ($lines as $line) {
			$len = mb_strwidth($line);
			foreach ($emojiTags as $emojiTag) {
				$len = $len - substr_count($line, $emojiTag) * (mb_strwidth($emojiTag[1]) + 1);
			}
			if ($len_max < $len) {
				$len_max = $len;
			}
		}
		$fire = '🔥';
		if (preg_match('/(凍らせて|冷やして).?$/u', $content, $match)) {
			$fire = '🧊';
		}
		else if (preg_match('/萌やして.?$/u', $content, $match)) {
			$fire = '💕';
		}
		else if (preg_match('/通報して.?$/u', $content, $match)) {
			$fire = '⚠️';
		}
		else if (preg_match('/磨いて.?$/u', $content, $match)) {
			$fire = '🪥';
		}
		else if (preg_match('/爆破して.?$/u', $content, $match)) {
			$fire = '💣';
		}
		else if (preg_match('/(注射して|打って).?$/u', $content, $match)) {
			$fire = '💉';
		}
		else if (preg_match('/(駐車して|停めて).?$/u', $content, $match)) {
			$fire = '🚗';
		}
		else if (preg_match('/豆腐|とうふ|トウフ|トーフ|tofu/ui', $content, $match)) {
			$fire = '📛';
		}
		else if (preg_match('/魂|心|いのち|命|ハート|はーと|はあと|はぁと/u', $content, $match)) {
			$fire = '❤️‍🔥';
		}
		else if (preg_match('/陽性|妖精/u', $content, $match)) {
			$fireary = array('🧚', '🧚‍♂', '🧚‍♀');
			$fire = $fireary[rand(0, count($fireary) - 1)];
		}
		$res = $target. "\n". str_repeat($fire, $len_max / 2);
		$tags = array_merge($tags, $emojiTags);
	}
	else if (preg_match('/#nostrquiz/u', $content)) {
		if (preg_match('/何人でしょう/u', $content)) {
			$res = '';
			if (preg_match('/月の/u', $content)) {
				$res .= floor((rand(500, 1000) + rand(500, 1000)) / 2);
			}
			else {
				$res .= floor((rand(150, 300) + rand(150, 300)) / 2);
			}
			if ($rootTag) {
				$tags = [['p', $data['pubkey'], ''], $rootTag, ['e', $data['id'], '', 'reply']];
			}
			else {
				$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', 'root']];
			}
		}
		else {
			return [null, null];
		}
	}
	else {
		$res = 'えんいー';
	}
	return [$res, $tags];
}

function fav($data) {
	$res = null;
	$tags = null;
	if (preg_match('/ぎゅうにゅう/u', $data['content'])) {
		$res = '🥛';
		$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', '']];
	}
	else if (preg_match('/うにゅう/u', $data['content'])) {
		if (preg_match('/うにゅうハウス/u', $data['content'])) {
			return [null, null];
		}
		$res = ':unyu:';
		$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', ''], ['emoji','unyu', 'https://nikolat.github.io/avatar/disc2.png']];
	}
	else if (preg_match('/^うちゅう$/u', $data['content'])) {
		$mesary = array('🪐', '🛸', '🚀');
		$res = $mesary[rand(0, count($mesary) - 1)];
		$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', '']];
	}
	else if (preg_match('/^う[^に]ゅう$/u', $data['content'])) {
		$res = '❓';
		$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', '']];
	}
	else {
		$res = '⭐';
		$tags = [['p', $data['pubkey'], ''], ['e', $data['id'], '', '']];
	}
	return [$res, $tags];
}
