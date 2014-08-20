<?php
/*
Plugin Name: Y!ショッピング商品検索
Plugin URI: http://mar11th.com/
Description: Yahooショッピングの商品検索APIを利用して、キーワード検索を行います
Author: hyugavirus
Version: 0.1
Author URI: https://github.com/hyugavirus
*/

class YahooShoppingItemsearchClient {
    // オプションのKEY名
    const PLUGIN_OPTION_KEY = 'ysic_options';
    // フォームで利用するアクション名
    const ACTION_NAME = 'register_appId';
    
    /**
     * コンストラクタ
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_pages'));
    }
    
    // ワードプレスの管理画面へのメニュー追加
    function add_pages() {
        add_menu_page(
                'Y!ショッピング商品検索', //メニューが有効になった時に表示されるHTMLのページタイトル用テキスト。
                'Y!商品検索', //管理画面のメニュー上での表示名。
                'level_8', // このメニューページを閲覧・使用するために最低限必要なユーザーレベルまたはユーザーの種類と権限 
                __FILE__, // メニューページのコンテンツを表示するPHPファイル
                array($this, 'registerAppId'), //メニューページにコンテンツを表示する関数
                '' // icon_url WordPress 2.7においてのみ有効
                );
    }
    
    /**
     * appIDを登録するフォーム
     */
    function registerAppId() {
        $post = filter_input_array(INPUT_POST);
        if(isset($post[self::PLUGIN_OPTION_KEY])) {
            $opt = $post[self::PLUGIN_OPTION_KEY];
            check_admin_referer(self::ACTION_NAME);
            update_option(self::PLUGIN_OPTION_KEY, $opt);
            echo <<<EOD
<div class="updated fade"><p><strong>Options saved.</strong></p></div>
EOD;
        }
        
        
        echo <<<EOD
<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2>Yahoo!デベロッパーネットワークで取得したアプリケーションIDを入力してください</h2>
    <form action="" method="post">
EOD;
            wp_nonce_field(self::ACTION_NAME);
            $optionValue = $this->getOptionValue();
            $pluginOptionKey = self::PLUGIN_OPTION_KEY;
            echo <<<EOD
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="inputtext">アプリケーションID</label></th>
            <td><input name="{$pluginOptionKey}[text]" type="text" id="inputtext" value="{$optionValue}" class="regular-text" /></td>
        </tr>
    </table>
    <p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
    </form>
</div>
EOD;
    }
    
    /**
     * オプション値を取得する
     * @return type
     */
    private function getOptionValue() {
        $opt = get_option(self::PLUGIN_OPTION_KEY);
        $optvalue = isset($opt['text']) ? $opt['text']: null;
        return $optvalue;
    }
    
    
    // APIのURL
    const YAHOO_SHOPPING_API_URL = 'http://shopping.yahooapis.jp/ShoppingWebService/V1/php/itemSearch';
    // APIのパラメータ
    private $_params = array();
    
    /**
     * APIのパラメーターの設定
     * @see http://developer.yahoo.co.jp/webapi/shopping/shopping/v1/itemsearch.html
     * @param array $params
     */
    public function setParameters(array $params) {
        $this->_params = array_merge($this->_params, $params);
    }
    
    /**
     * キーワード検索
     * @param type $keyword
     * @return ResultSet
     */
    public function keywordSearch($keyword) {
        $appId = $this->getOptionValue();
        if(empty($appId)) {
            return null;
        }
        $this->_params['appid'] = $appId;
        $this->_params['query'] = htmlspecialchars($keyword);
        
        $ch = curl_init(self::YAHOO_SHOPPING_API_URL.'?'.http_build_query($this->_params));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);
        return unserialize($result);
    }
    
}
$ysiClient = new YahooShoppingItemsearchClient();