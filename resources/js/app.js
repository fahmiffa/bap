import Alpine from "alpinejs";
import { userForm, ttd, docViewer } from "./custom";

window.Alpine = Alpine;
Alpine.data("userForm", userForm);
Alpine.data("ttd", ttd);
Alpine.data("docViewer", docViewer);
Alpine.start();
