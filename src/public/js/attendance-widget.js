async function loadAttendanceWidget() {
    /*
    |--------------------------------------------------------------------------
    | widget element
    |--------------------------------------------------------------------------
    */
    const widget = document.getElementById("attendance-widget");

    if (!widget) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | loading
    |--------------------------------------------------------------------------
    */
    widget.innerHTML = `
        <div style="padding:20px;">
            読み込み中...
        </div>
    `;

    try {
        /*
        |--------------------------------------------------------------------------
        | fetch API
        |--------------------------------------------------------------------------
        */
        const response = await fetch(
            "https://cf444722.cloudfree.jp/api/widget/attendance",
        );

        const data = await response.json();

        /*
        |--------------------------------------------------------------------------
        | worker rows
        |--------------------------------------------------------------------------
        */
        let workerRows = "";

        data.workers.forEach((worker) => {
            // status label
            let statusLabel = "";
            let statusBadge = "";

            switch (worker.status) {
                case "working":
                    statusLabel = "出勤中";
                    statusBadge = `
                <span class="badge badge-warning">
                    ${statusLabel}
                </span>
            `;
                    break;

                case "missing":
                    statusLabel = "未打刻";
                    statusBadge = `
                <span class="badge badge-danger">
                    ${statusLabel}
                </span>
            `;
                    break;

                case "completed":
                    statusLabel = "退勤済";
                    statusBadge = `
                <span class="badge badge-success">
                    ${statusLabel}
                </span>
            `;
                    break;

                default:
                    statusLabel = "-";
                    statusBadge = statusLabel;
            }

            // row
            workerRows += `
        <tr>
            <td class="pl-4">${worker.name}</td>
            <td style="text-align:center;">
                ${statusBadge}
            </td>
        </tr>
    `;
        });

        /*
        |--------------------------------------------------------------------------
        | render
        |--------------------------------------------------------------------------
        */
        widget.innerHTML = `

<div class="card bg-info text-white">
        <div class="card-header d-flex align-items-center">
            <span>作業員出勤状況</span>
            <a href="https://cf444722.cloudfree.jp/v2/attendances" class="ml-auto text-white">
                <i class="fas fa-external-link-square-alt"></i>
            </a>
        </div>

        <div class="card-body position-relative">
            <div class="position-absolute" style="right:20px; top:10px; opacity:0.15; font-size:70px;">
                <i class="fas fa-users"></i>
            </div>

            <div class="d-flex border-bottom py-1">
                <span>総人数</span>
                <h4 class="ml-auto mb-0">${data.totalCount}</h4>
            </div>

            <div class="d-flex border-bottom py-1">
                <span>出勤中</span>
                <h4 class="ml-auto mb-0">${data.workingCount}</h4>
            </div>
            <div class="d-flex py-1">
                <span>未打刻</span>
                <h4 class="ml-auto mb-0">${data.missingCount}</h4>
            </div>
        </div>

        <div class="card-footer p-0">
            <details>
                <summary class="px-4 py-3">勤務状況</summary>
                <div style="max-height:250px; overflow:auto;">
                    <table class="table table-sm bg-white mb-0">
                        <thead>
                            <tr>
                                <th class="pl-4">氏名</th>
                                <th class="text-center">状況</th>
                            </tr>
                        </thead>
                        <tbody>
                                ${workerRows}
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    </div>
            `;
    } catch (error) {
        widget.innerHTML = `
            <div style="padding:20px; color:red;">
                widget load error
            </div>
        `;

        console.error(error);
    }
}

/*
|--------------------------------------------------------------------------
| 初回
|--------------------------------------------------------------------------
*/
loadAttendanceWidget();

/*
|--------------------------------------------------------------------------
| 30秒更新
|--------------------------------------------------------------------------
*/
//setInterval(loadAttendanceWidget, 30000);
