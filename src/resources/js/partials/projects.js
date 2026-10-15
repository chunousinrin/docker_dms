/**
 * Projects 登録・編集画面用共通スクリプト
 */
function initProjectScript() {
    if (window.$ || window.jQuery) {
        $(function () {
            // HTMLのメタ情報やフォームから各種URL・トークンを取得
            const $form = $("#project-form");
            const routes = {
                workerGroupsActive: $form.data("route-worker-groups-active"),
                orderNoCandidates: $form.data("route-order-no-candidates"),
                pdfPreview: $form.data("route-pdf-preview"),
            };

            /* ================================
             * 指示日 → 年度自動計算
             * ================================ */
            $("#ordered_on").on("change", function () {
                const dateStr = $(this).val();
                if (!dateStr) return;

                const date = new Date(dateStr);
                const year = date.getFullYear();
                const month = date.getMonth() + 1;

                // 会計年度（4〜12月 → その年、1〜3月 → 前年）
                const fiscalYear = month >= 4 ? year : year - 1;

                // hidden にセット
                $("#fiscal_year").val(fiscalYear);

                // モーダルの年度セレクトも更新
                $("#fiscalYearSelect").val(fiscalYear);

                console.log("年度更新:", fiscalYear);

                // チーム選択済みなら候補番号を再取得
                const groupId = $("#teamSelect").val();
                if (groupId && routes.orderNoCandidates) {
                    fetchOrderNoCandidates(groupId, fiscalYear);
                }
            });

            /* ================================
             * モーダルイベント (Bootstrap 4対応)
             * ================================ */
            $("#orderNoModal").on("shown.bs.modal", function () {
                let fy = $("#fiscal_year").val();

                if (!fy) {
                    fy = $("#fiscalYearSelect").val();
                    $("#fiscal_year").val(fy);
                }

                $("#fiscalYearSelect").val(fy);
                console.log("モーダルが開いたのでloadTeamsを実行します");
                loadTeams();
            });

            /* ================================
             * WorkerGroups（稼働中の班）を取得
             * ================================ */
            function loadTeams() {
                if (!routes.workerGroupsActive) return;

                $.ajax({
                    url: routes.workerGroupsActive,
                    type: "GET",
                    success: function (data) {
                        const select = $("#teamSelect");
                        select.empty();

                        data.forEach((group) => {
                            select.append(
                                `<option value="${group.id}">${group.name}</option>`,
                            );
                        });

                        const groupId = select.find("option:first").val();
                        const fy = $("#fiscalYearSelect").val();

                        if (groupId) {
                            fetchOrderNoCandidates(groupId, fy);
                        }
                    },
                    error: function (xhr) {
                        console.error("班一覧取得エラー:", xhr.responseText);
                    },
                });
            }

            /* ================================
             * 年度 or チーム変更 → 候補番号取得
             * ================================ */
            $("#teamSelect, #fiscalYearSelect").on("change", function () {
                const groupId = $("#teamSelect").val();
                const fy = $("#fiscalYearSelect").val();

                // hidden にも年度を反映
                $("#fiscal_year").val(fy);

                if (groupId && fy) {
                    fetchOrderNoCandidates(groupId, fy);
                }
            });

            /* ================================
             * Ajax: 指示書番号候補取得
             * ================================ */
            function fetchOrderNoCandidates(groupId, fiscalYear) {
                if (!routes.orderNoCandidates) return;

                $.ajax({
                    url: routes.orderNoCandidates,
                    type: "GET",
                    data: {
                        group_id: groupId,
                        fiscal_year: fiscalYear,
                    },
                    success: function (data) {
                        console.log("候補番号API結果(fetch):", data);
                        $("#orderNoList").text(
                            "既存番号: " + (data.last_order_no ?? "なし"),
                        );
                        $("#nextOrderNo").text(
                            "次候補: " + data.candidates.join(", "),
                        );
                    },
                    error: function (xhr) {
                        $("#orderNoList").text("取得に失敗しました");
                        console.error("候補番号取得エラー:", xhr.responseText);
                    },
                });
            }

            /* ================================
             * モーダル「決定」 → order_no に反映
             * ================================ */
            $("#closeModal").on("click", function () {
                const nextOrder = $("#nextOrderNo")
                    .text()
                    .replace("次候補: ", "");
                $("#order_no").val(nextOrder.split(", ")[0] ?? nextOrder);
            });

            /* ================================
             * ratio / destination の追加削除
             * ================================ */
            const orderTypeSelect = document.getElementById("order_type");
            const labels = document.querySelectorAll(".area-label");

            // === order_type による表示/非表示 & ラベル切替 ===
            function toggleFields() {
                if (!orderTypeSelect) return;
                const val = orderTypeSelect.value;

                // 一旦すべて非表示
                document
                    .querySelectorAll(
                        ".forest-nursery-common, .general-only, .nursery-only, .forest-only",
                    )
                    .forEach((el) => (el.style.display = "none"));

                if (val === "一般") {
                    document
                        .querySelectorAll(".general-only")
                        .forEach((el) => (el.style.display = ""));
                } else if (val === "保育") {
                    document
                        .querySelectorAll(
                            ".forest-nursery-common, .nursery-only",
                        )
                        .forEach((el) => (el.style.display = ""));
                    const texts = ["除伐", "枝打ち", "測量"];
                    labels.forEach(
                        (el, i) => (el.textContent = texts[i] || ""),
                    );
                } else if (val === "林産") {
                    document
                        .querySelectorAll(
                            ".forest-nursery-common, .forest-only",
                        )
                        .forEach((el) => (el.style.display = ""));
                    const texts = ["総面積", "切捨", "搬出"];
                    labels.forEach(
                        (el, i) => (el.textContent = texts[i] || ""),
                    );
                } else {
                    // 未選択時は全部表示
                    document
                        .querySelectorAll(
                            ".forest-nursery-common, .general-only, .nursery-only, .forest-only",
                        )
                        .forEach((el) => (el.style.display = ""));
                }
            }

            if (orderTypeSelect) {
                orderTypeSelect.addEventListener("change", toggleFields);
                toggleFields(); // 初期化実行
            }

            // イベントデリゲーションを利用した動的要素のハンドリング
            document.addEventListener("click", function (e) {
                // ratio追加
                if (e.target.closest(".ratio_add")) {
                    const container =
                        document.querySelector(".ratio-container");
                    if (!container) return;
                    const wrappers =
                        container.querySelectorAll(".ratio-wraper");

                    if (wrappers.length >= 3) {
                        alert("割合（ratios）は最大3つまでです。");
                        return;
                    }

                    const index = wrappers.length;
                    const row = document.createElement("div");
                    row.classList.add("input-group", "ratio-wraper", "mt-2");
                    row.innerHTML = `
                        <input list="ratio_list" type="text" name="ratios[${index}][ratio_type]" class="form-control form-control-sm" placeholder="材種類">
                        <input type="number" step="0.01" name="ratios[${index}][ratio]" class="form-control form-control-sm" placeholder="割合">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-outline-danger remove-ratio" type="button">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                    container.appendChild(row);
                }

                // ratio削除
                if (e.target.closest(".remove-ratio")) {
                    e.target.closest(".ratio-wraper")?.remove();

                    // インデックス再振り分け
                    document
                        .querySelectorAll(".ratio-wraper")
                        .forEach((wrapper, i) => {
                            wrapper
                                .querySelectorAll("input")
                                .forEach((input) => {
                                    if (input.name.includes("[ratio_type]"))
                                        input.name = `ratios[${i}][ratio_type]`;
                                    if (input.name.includes("[ratio]"))
                                        input.name = `ratios[${i}][ratio]`;
                                });
                        });
                }

                // destination追加
                if (e.target.closest(".destination_add")) {
                    const container = document.querySelector(
                        ".destinations-container",
                    );
                    if (!container) return;
                    const wrappers = container.querySelectorAll(
                        ".destination-wraper",
                    );
                    const index = wrappers.length;

                    const row = document.createElement("div");
                    row.classList.add(
                        "input-group",
                        "destination-wraper",
                        "mt-2",
                    );
                    row.innerHTML = `
                        <input list="destination_type_list" type="text" name="destinations[${index}][destination_type]" class="form-control form-control-sm" placeholder="材種類">
                        <input list="destination_list" type="text" name="destinations[${index}][destination]" class="form-control form-control-sm" placeholder="出材先">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-outline-danger remove-destination" type="button">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                    container.appendChild(row);
                }

                // destination削除
                if (e.target.closest(".remove-destination")) {
                    e.target.closest(".destination-wraper")?.remove();

                    // インデックス再振り分け
                    document
                        .querySelectorAll(".destination-wraper")
                        .forEach((wrapper, i) => {
                            wrapper
                                .querySelectorAll("input")
                                .forEach((input) => {
                                    if (
                                        input.name.includes(
                                            "[destination_type]",
                                        )
                                    )
                                        input.name = `destinations[${i}][destination_type]`;
                                    if (input.name.includes("[destination]"))
                                        input.name = `destinations[${i}][destination]`;
                                });
                        });
                }
            });

            // モーダル閉じる時の処理
            $("#closeModal").on("click", function () {
                const selectedGroupId = $("#teamSelect").val();
                $("#group_id").val(selectedGroupId);
            });

            /* ================================
             * PDFプレビュー処理
             * ================================ */
            let previewLoading = false;
            async function previewPdf() {
                if (previewLoading) return;
                previewLoading = true;

                const $btn = $("#preview-btn");
                const originalHtml = $btn.html();
                $btn.html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i> 生成中...',
                ).prop("disabled", true);

                const formEl = document.getElementById("project-form");
                if (!formEl || !routes.pdfPreview) {
                    console.error(
                        "フォームまたはプレビューURLが見つかりません",
                    );
                    previewLoading = false;
                    $btn.html(originalHtml).prop("disabled", false);
                    return;
                }

                const formData = new FormData(formEl);
                const csrfToken = $('meta[name="csrf-token"]').attr("content");

                try {
                    const response = await fetch(routes.pdfPreview, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                        },
                    });

                    if (!response.ok) throw new Error("PDF生成に失敗しました");

                    const blob = await response.blob();
                    const pdfUrl = URL.createObjectURL(blob);

                    if (typeof Fancybox !== "undefined") {
                        Fancybox.show([{ src: pdfUrl, type: "iframe" }], {
                            on: {
                                destroy: () => {
                                    URL.revokeObjectURL(pdfUrl);
                                },
                            },
                        });
                    } else {
                        // Fancybox が利用できない場合のフォールバック（別窓開く）
                        window.open(pdfUrl, "_blank");
                    }
                } catch (e) {
                    console.error(e);
                    alert(
                        "PDFプレビューの生成に失敗しました。入力内容を確認してください。",
                    );
                } finally {
                    previewLoading = false;
                    $btn.html(originalHtml).prop("disabled", false);
                }
            }

            // PDFプレビューボタンのイベント紐付け
            $("#preview-btn").on("click", previewPdf);
        });
    } else {
        setTimeout(initProjectScript, 50);
    }
}

// 実行
initProjectScript();
