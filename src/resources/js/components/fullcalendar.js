import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import googleCalendarPlugin from "@fullcalendar/google-calendar";
import jaLocale from "@fullcalendar/core/locales/ja";
import "../../css/components/fullcalendar.css";

document.addEventListener("DOMContentLoaded", function () {
    var calendarEl = document.getElementById("calendar");

    if (!calendarEl) return;

    var calendar = new Calendar(calendarEl, {
        plugins: [
            dayGridPlugin,
            timeGridPlugin,
            interactionPlugin,
            googleCalendarPlugin,
        ],

        initialView: "dayGridMonth",
        locale: jaLocale,
        height: "auto",
        contentHeight: "auto",
        handleWindowResize: true,

        dayCellContent: function (e) {
            return e.dayNumberText.replace("日", "");
        },

        headerToolbar: {
            left: "prev",
            center: "title",
            right: "next",
        },

        googleCalendarApiKey: "AIzaSyD-YqAbC9BkMld6nmrerxp61g4dbPTqb6c",
        eventSources: [
            {
                googleCalendarId:
                    "7659558c60480afed1dcd82e0271bab2db25d85f67e11caba8126289ef0879aa@group.calendar.google.com",
                color: "#3c97ff99",
            },
            {
                googleCalendarId:
                    "b1a34a28897861a9eb421e2883803167e0c07117e984e3bfbf10e2301e54b949@group.calendar.google.com",
                color: "#70BD29",
            },
        ],

        eventClick: function (info) {
            info.jsEvent.preventDefault();
            alert("イベント: " + info.event.title);
        },
    });

    calendar.render();
});
