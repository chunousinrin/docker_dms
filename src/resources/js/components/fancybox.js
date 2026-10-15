import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import "../../css/components/fancybox.css";

window.Fancybox = Fancybox;

Fancybox.bind("[data-fancybox]", {
    mainClass: "pdf-fancybox",
    dragToClose: false,
    Navigation: false,
    Html: {
        iframeAttr: {
            allow: "autoplay; fullscreen",
        },
    },
});
