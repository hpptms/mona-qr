var url = location.href ;
var excludes = ["u_id"];
history.replaceState(trimToURL(excludes, url);

function trimToURL(excludes, url){
  var builder = { search: '', hash: '' }, baseUrl = url; /* builderオブジェクトを用意 */

  if ( !url ) return url;

  if ( typeof(URL) === "function" ) {
    var builder = new URL(url);
    baseUrl = builder.origin + builder.port + builder.pathname;
  }
  /* URLインスタンス非対応ブラウザ用 */
  else {
    var tmpURL = url.split('?'), search = '', num = '', hash = '';
    // パラメーターを含んでいる場合
    if ( tmpURL[1] !== undefined ) {
      baseUrl = tmpURL[0]; // ? 以降を変数に格納
      search = tmpURL[1]; // ? 以降を変数に格納
      /* ハッシュの為の処理 */
      num = url.indexOf('#'); // # の位置を確認
      hash = (num > -1) ? url.substr(num) : ""; // # があった場合は、それ以降の文字列を変数に格納【更新(1)】
      search = '?' + search.replace(hash, ""); // # 以降の文字列をもとのURLから取り除き、変数に格納
      builder = { // エセURLインターフェースの戻り値作成
        search: search,
        hash: hash,
      };
    }
  }

  if (builder.search) {
    builder.search = "?" + builder.search.substr(1).split('&').filter(function (item) {
        return !excludes.hasOwnProperty(item.split('=', 2)[0]);
    }).join('&');
    builder.search = ( builder.search.search(/\?$/) < 0 ) ? builder.search : ""; // IE対策【更新(2)】
    // ↓現在はレガシーブラウザにも対応させる必要があったので悲しい書き方となっております
    return baseUrl + builder.search + builder.hash;
  }
  else {
    return url;
  }
}
