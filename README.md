wordpress-yahoo-shopping-itemsearch-plugin
==========================================

Yahooショッピングの商品検索APIを利用して、キーワード検索を行うワードプレスプラグインです

# 使い方

## プラグインの有効化
pluginsの中に入っている"yahoo-shopping-itemsearch-client"フォルダをwordpressのプラグインディレクトリへ

## アプリケーションIDの取得
Yahoo!デベロッパーネットワークで取得したアプリケーションIDを管理画面で登録

## テンプレートで検索結果を出力
検索結果を行事したい場所で、以下のように呼び出し
  `$result = $ysiClient->keywordSearch('妖怪ウォッチ');`
  あとは$resultの内容をご自由に出力してください
  アフィリエイトID等設定する場合は、検索する前に
  `$ysiClient->setParameters(array('affiliate_id' => YOUR_AFFLIATE_ID,))`
  などしてください
