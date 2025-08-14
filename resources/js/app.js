import Alpine from "alpinejs";
import { userForm, ttd } from "./custom";

window.Alpine = Alpine;
Alpine.data("userForm", userForm);
Alpine.data("ttd", ttd);
Alpine.start();
