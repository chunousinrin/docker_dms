export default function initSelect2() {
    $(".select2").select2({
        theme: "bootstrap4",
        width: "100%", // AdminLTEだとこれ入れておくと安定
        tags: true, // 選択肢にない値を入力しても追加できるようにする

        containerCss: function () {
            return { class: isSm ? "select2-form-control-sm" : "" };
        },
        dropdownCss: function () {
            return { class: isSm ? "select2-form-control-sm" : "" };
        },
    });
}
