<?php


define('IP_PHP_VERSION', 'PHP バージョン 5.3 以上');
define('IP_MOD_REWRITE', 'Apache モジュール "mod_rewrite"');
define('IP_HTACCESS', '.htaccess file');
define('IP_MOD_PDO', 'PHP module "PDO"');
define('IP_GD_LIB', 'GD グラフィックライブラリ');
define('IP_CURL', 'PHP module "Curl" (optional)');
define('IP_SESSION', 'PHP sessions');
define('IP_MAGIC_QUOTES', 'magic quotes (off 推奨)');
define('IP_INDEX_HTML', 'index.html removed');

define('IP_OK', 'はい');
define('IP_ERROR', 'いいえ');
define('IP_WRITABLE', '書込可能');
define('IP_CHECK_AGAIN', '再度確認');
define('IP_BACK', '戻る');
define('IP_NEXT', '次へ');
define('IP_ACCEPT', '承諾');
define('IP_INSTALLATION', 'ImpressPages CMS インストールウィザード');
define('IP_VERSION', 'Version '.TARGET_VERSION);
define('IP_SUBDIRECTORIES', '(サブフォルダ・ファイルを含む');
define('IP_OPTIONAL', '(任意)');



define('IP_STEP_LANGUAGE', '言語を選択');
define('IP_STEP_CHECK', 'システム確認');
define('IP_STEP_LICENSE', 'ライセンス');
define('IP_STEP_DB', 'データベース');
define('IP_STEP_CONFIGURATION', '設定');
define('IP_STEP_COMPLETED', '完了');
define('IP_STEP_LANGUAGE_LONG', 'インターフェイス言語の選択');
define('IP_STEP_CHECK_LONG', 'システム確認');
define('IP_STEP_LICENSE_LONG', 'ImpressPages 法的通知');
define('IP_STEP_DB_LONG', 'データベースへインストール');
define('IP_STEP_CONFIGURATION_LONG', 'システム設定');
define('IP_STEP_COMPLETED_LONG', 'ImpressPages CMS をインストールしました');



define('IP_DB_SERVER', 'データベースホスト (例 localhost または 127.0.0.1)');
define('IP_DB_USER', 'ユーザ名');
define('IP_DB_PASS', 'ユーザパスワード');
define('IP_DB_DB', 'データベース');
define('IP_DB_PREFIX', 'テーブル prefix (下線は prefix の区切り)');
define('IP_DB_DATA_WARNING', '注意!!! prefix が含まれる古い全てのテーブル削除されます!');
define('IP_DB_ERROR_ALL_FIELDS', '全項目を記入して下さい');
define('IP_DB_ERROR_CONNECT', 'データベースに接続できません');
define('IP_DB_ERROR_DB', '指定されたデータベースは存在しません');
define('IP_DB_ERROR_QUERY', '未知の SQL エラー');
define('IP_DB_ERROR_LONG_PREFIX', 'prefix は7文字以上でなければいけません');
define('IP_DB_ERROR_INCORRECT_PREFIX', 'prefix に記号を含める事はできず、半角英文小文字からはじめなければいけません');
define('IP_DB_ERROR_EMAIL', 'E-mail アドレスに誤りがあります');
define('IP_CONFIG_ERROR_CONFIG', '設定ファイル "/ip_config.php" へ書き込みできません');
define('IP_CONFIG_ERROR_ROBOTS', '設定ファイル "/robots.txt" へ書き込みできません');
define('IP_CONFIG_ERROR_EMAIL', '管理者 E-mail アドレスへご連絡下さい');
define('IP_CONFIG_ERROR_SITE_NAME', 'Web サイト名を入力して下さい');
define('IP_CONFIG_ERROR_SITE_EMAIL', 'Web サイト E-mail アドレスを入力して下さい');
define('IP_CONFIG_ERROR_LOGIN', '管理者名・パスワードを入力して下さい');
define('IP_CONFIG_ERROR_TIME_ZONE', 'タイムゾーンを選択して下さい');


define('IP_CONFIG_SITE_NAME', 'サイト名');
define('IP_CONFIG_SITE_EMAIL', 'サイト E-mail アドレス');
define('IP_CONFIG_EMAIL', 'エラーレポート送信先 E-mail (任意)');
define('IP_CONFIG_LOGIN', '管理者ログイン名');
define('IP_CONFIG_PASS', '管理者パスワード');
define('IP_CONFIG_TIMEZONE', 'サイトタイムゾーン');
define('IP_CONFIG_SELECT_TIMEZONE', 'サイトタイムゾーンを選択して下さい');

define('IP_FINISH_MESSAGE', '
<p>
<a href="../">トップページ</a>
</p>
<p>
<a href="../admin.php">管理者ページ</a><br /><br />
</p>
<p>
もしインストールを再度行いたい場合は設定ファイル "ip_config.php" を削除して下さい。
</p>
');
