import flatpickr from "flatpickr";
import { Japanese } from "flatpickr/dist/l10n/ja.js";
import "../../css/components/datepicker.css";

flatpickr(".datepicker", {
    locale: Japanese,
    dateFormat: "Y-m-d",

    allowInput: true,
    disableMobile: true,

    onReady: function (selectedDates, dateStr, instance) {
        // ボタン作成
        const clearBtn = document.createElement("div");

        clearBtn.className = "flatpickr-clear";

        clearBtn.innerHTML = "<i class='fas fa-eraser mr-2'></i>クリア";

        // クリック処理
        clearBtn.addEventListener("click", function () {
            instance.clear();
            instance.close();
        });

        // カレンダー下へ追加
        instance.calendarContainer.appendChild(clearBtn);
    },
});

flatpickr(".datepicker-modal", {
    locale: Japanese,
    dateFormat: "Y-m-d",

    allowInput: true,
    disableMobile: true,
    appendTo: document.querySelector("#modal-create"),
    position: "above",

    onReady: function (selectedDates, dateStr, instance) {
        // ボタン作成
        const clearBtn = document.createElement("div");

        clearBtn.className = "flatpickr-clear";

        clearBtn.innerHTML = "<i class='fas fa-eraser mr-2'></i>クリア";

        // クリック処理
        clearBtn.addEventListener("click", function () {
            instance.clear();
            instance.close();
        });

        // カレンダー下へ追加
        instance.calendarContainer.appendChild(clearBtn);
    },
});
