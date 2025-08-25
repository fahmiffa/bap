export default function pdfUploader({ maxSizeMB = 5 } = {}) {
    return {
        file: null,
        error: null,
        dragging: false,
        maxSizeMB,

        init() {},

        openPicker() {
            this.$refs.input.click();
        },

        handleChange(e) {
            const f = e.target.files[0];
            this.validateAndSet(f);
        },

        handleDrop(e) {
            this.dragging = false;
            const f = e.dataTransfer.files[0];
            this.validateAndSet(f);
        },

        validateAndSet(f) {
            this.error = null;
            this.file = null;

            if (!f) return;

            // Tipe & ukuran
            const isPdf =
                f.type === "application/pdf" ||
                (f.name || "").toLowerCase().endsWith(".pdf");
            const maxBytes = this.maxSizeMB * 1024 * 1024;

            if (!isPdf) {
                this.error = "File harus berupa PDF (.pdf).";
                return;
            }
            if (f.size > maxBytes) {
                this.error = `Ukuran melebihi ${this.maxSizeMB} MB.`;
                return;
            }

            this.file = f;
            // sinkronkan ke <input type="file"> form agar Laravel bisa membaca
            const dt = new DataTransfer();
            dt.items.add(f);
            this.$refs.input.files = dt.files;
        },

        prettySize(bytes) {
            if (bytes < 1024) return bytes + " B";
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
            return (bytes / (1024 * 1024)).toFixed(2) + " MB";
        },

        remove() {
            this.file = null;
            this.error = null;
            this.$refs.input.value = "";
        },
    };
}
export function userForm(initialRows = [{ note: "", name: "" }]) {
    console.log(initialRows)
    return {
         rows: initialRows,

        addRow() {
            this.rows.push({ note: "", name: "" });
        },

        removeRow(index) {
            this.rows.splice(index, 1);
        },
    };
}

import SignaturePad from "signature_pad";

export function ttd(da) {
    return {
        user: null,
        open: false,
        signaturePad: null,
        isEmpty: true,
        savedUrl: null,

        init() {
            this.$nextTick(() => {
                const canvas = this.$refs.canvas;
                canvas.style.touchAction = "none";

                this.signaturePad = new SignaturePad(canvas, {
                    backgroundColor: "rgba(255,255,255,0)",
                    penColor: "black",
                });

                // resize awal kalau modal sudah kelihatan
                if (this.open) {
                    this.$nextTick(() => this.resizeCanvas(canvas));
                }

                // ketika ukuran layar berubah
                window.addEventListener("resize", () =>
                    this.resizeCanvas(canvas)
                );

                // update status kosong
                canvas.addEventListener("pointerup", () => {
                    this.isEmpty = this.signaturePad.isEmpty();
                });

                // pantau perubahan "open" untuk resize saat modal dibuka
                this.$watch("open", (val) => {
                    if (val) {
                        this.$nextTick(() => {
                            this.resizeCanvas(canvas);
                        });
                    }
                });
            });
        },

        resizeCanvas(canvas) {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const rect = canvas.getBoundingClientRect();

            // jika modal belum terbuka, jangan resize
            if (rect.width === 0 || rect.height === 0) return;

            canvas.width = rect.width * ratio;
            canvas.height = rect.height * ratio;
            canvas.getContext("2d").scale(ratio, ratio);

            if (this.signaturePad) {
                const data = this.signaturePad.toData();
                this.signaturePad.clear();
                this.signaturePad.fromData(data || []);
            }
        },

        clear() {
            this.signaturePad?.clear();
            this.isEmpty = true;
        },

        undo() {
            if (!this.signaturePad) return;
            const data = this.signaturePad.toData();
            if (data.length) {
                data.pop();
                this.signaturePad.fromData(data);
                this.isEmpty = this.signaturePad.isEmpty();
            }
        },

        close() {
            this.open = false;
        },

        async save() {
            if (!this.signaturePad || this.signaturePad.isEmpty()) {
                alert("Tanda tangan kosong!");
                return;
            }
            const dataUrl = this.signaturePad.toDataURL("image/png");

            try {
                const token = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");

                const res = await fetch("/sign/" + da, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token,
                        Accept: "application/json",
                    },
                    body: JSON.stringify({
                        data_url: dataUrl,
                        user: this.user,
                        id: this.id,
                    }),
                });

                const json = await res.json();
                if (!res.ok) throw json;

                this.savedUrl = json.url;
                this.open = false;
                alert("Tanda tangan tersimpan!");
                window.reload();
            } catch (err) {
                console.error(err);
                alert("Gagal menyimpan tanda tangan.");
            }
        },
    };
}
