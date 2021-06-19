<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Vue Laravel SPA') }}</title>
        <!-- Styles -->
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    </head>

 <body>

<?php


    $bearer_token = 'AAAAAAAAAAAAAAAAAAAAAEj3QgEAAAAA8EjFgM%2BQPXoyWINj1bjllzVc43E%3Dk6g9Gy0DFtOxOu6R06ApGyDVNRMEI7QBD38f1vNfdRKo4TUBkF' ;	// ベアラートークン
	$request_url = 'https://api.twitter.com/1.1/trends/place.json' ;

    $params = array(
		"id" => "2345896",
//		"exclude" => "hashtags",
	) ;
    if( $params ) {
		$request_url .= '?' . http_build_query( $params ) ;
	}

    $context = array(
		'http' => array(
			'method' => 'GET' , // リクエストメソッド
			'header' => array(			  // ヘッダー
				'Authorization: Bearer ' . $bearer_token ,
			) ,
		) ,
	) ;
    $curl = curl_init() ;
	curl_setopt( $curl , CURLOPT_URL , $request_url ) ;
	curl_setopt( $curl , CURLOPT_HEADER, 1 ) ;
	curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $context['http']['method'] ) ;			// メソッド
	curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ;								// 証明書の検証を行わない
	curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;								// curl_execの結果を文字列で返す
	curl_setopt( $curl , CURLOPT_HTTPHEADER , $context['http']['header'] ) ;			// ヘッダー
	curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;										// タイムアウトの秒数
	$res1 = curl_exec( $curl ) ;
	$res2 = curl_getinfo( $curl ) ;
	curl_close( $curl ) ;

    $json = substr( $res1, $res2['header_size']) ;
    $json_obj = json_decode( $json ) ; //配列にしている
    $json_obj = json_encode($json_obj, JSON_UNESCAPED_UNICODE);

    $path = "../js/components";
    $path = realpath($path);
    $pathOfFile = $path . "/trends.json";
    echo $path;
    //file_put_contents($pathOfFile, $json_obj);
    // echo $json_obj;
    // for($i =0;$i<10;$i++)
    // {
    //     var_dump ($json_obj[0]->trends[$i]->name);
    // }


    //取得したデータ(JSONなど)




?>
<div id="app">
    <router-view></router-view>
</div>
<!-- Scripts -->
<script src="{{ mix('/js/app.js') }}" defer>
    // Vue.createApp({
    //     data () {
    //         return {
    //             array: {!! json_encode($json) !!}
    //         }
    //     }
    // }).mount('App')
</script>
</body>
</html>
