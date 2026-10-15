import Sortable from "sortablejs";

document.addEventListener("DOMContentLoaded", () => {
    const el = document.getElementById("sortable-items");

    if (el) {
        Sortable.create(el, {
            handle: ".fa-bars",
            animation: 150,
            ghostClass: "ui-state-highlight", // jQuery UIのクラス名を継承
            onEnd: function () {
                // window経由でjQuery側の関数を呼び出す
                if (typeof window.updateRowNumbers === "function") {
                    window.updateRowNumbers();
                }
                if (typeof window.calculate === "function") {
                    window.calculate();
                }
            },
        });
    }
});
