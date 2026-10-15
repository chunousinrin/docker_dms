// 1. jQuery のインポートとグローバル展開
/*import jQuery from "jquery";
window.$ = window.jQuery = jQuery;

// 2. Bootstrap / AdminLTE のインポート
import "bootstrap";
import "admin-lte";
*/
// ここまで初期化されてからBladeへ通知
//window.dispatchEvent(new Event("app-ready"));

// 3. Select2 本体のインポートと【明示的な紐付け】
import select2 from "select2";
//select2(window, jQuery); // 💡ここが超重要！jQueryに「お前はSelect2を使えるんだぞ」と直接教え込みます
select2(window, $);

import "./bootstrap";
import initSelect2 from "./components/select2"; // パスは適宜合わせてください（select.js）

// 4. CSS やその他のコンポーネント
import "@fortawesome/fontawesome-free/css/all.min.css";
import "select2/dist/css/select2.min.css";
import "select2-bootstrap4-theme/dist/select2-bootstrap4.min.css";
import "./components/datepicker";
import "./components/fancybox";
import "./components/sortable";

// 5. 初期化処理
$(function () {
    initSelect2();
});
/*import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();*/
