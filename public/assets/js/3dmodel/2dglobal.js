var selectedData = [];

// configList datang dari master table (m_odontogram_diagnosis, kategori=2d)
// lewat fetch di 2D_global.blade.php — tidak lagi hardcoded di sini. Setiap
// baris master dipetakan ke bentuk yang dipakai fungsi di bawah ini:
// {id, clear, nama, color, text}.
function mapDiagnosisRowToConfig(row) {
    return {
        id: row.id,
        clear: row.is_reset ? "1" : "0",
        nama: row.nama,
        color: row.warna,
        text: row.simbol || "",
    };
}

function load2DGlobal(curData = [], configList = []) {
    let dataJson = {
        Default_dent: [],
        config: configList.map(mapDiagnosisRowToConfig),
    };

    // Jalankan load
    loadGigi(dataJson);

    let selectedGigi = null;
    let selectedInput = null;
    let selectedIdnya = null;

    function loadGigi(obj) {
        obj.Default_dent = [];
        $(".gigi").each(function () {
            obj.Default_dent.push($(this));
        });

        loadDiv(obj);
        seleksiObjek(obj);
        loadGigiFromData(curData);
    }

    function seleksiObjek(obj) {
        $(".gigi")
            .off("click")
            .on("click", function (e) {
                e.preventDefault();
                selectedGigi = $(this);
                selectedIdnya = $(this).attr("accesskey");
                selectedInput = $("#gigi-" + selectedIdnya);

                $("#m-gigi-selected").find("span strong").text(selectedIdnya);
                $("#m-gigi-selected").modal("show");
            });

        // Event handler tombol pilihan
        $("#m-gigi-selected")
            .off("click", ".btn-c")
            .on("click", ".btn-c", function (e) {
                e.preventDefault();

                if (!selectedGigi || !selectedIdnya) return;

                const index = $(this).attr("accesskey");
                const cfg = obj.config[index];
                const idGigi = selectedIdnya.substring(0, 2);
                const bagian = selectedIdnya;

                if (cfg.clear === "1") {
                    // Hapus warna dan data
                    ["T", "B", "R", "L", "C"].forEach((p) => {
                        $("#v-" + idGigi + p).attr({ fill: "white" });
                        $("#gigi-" + idGigi + p).val(0);
                    });
                    $("#text-" + idGigi).html("");

                    selectedData = selectedData.filter(
                        (item) => item.idGigi !== idGigi,
                    );
                } else {
                    // Hapus dulu data lama dengan idGigi sama
                    selectedData = selectedData.filter(
                        (item) => item.bagian !== bagian,
                    );

                    selectedData.push({
                        idGigi,
                        bagian: selectedIdnya,
                        id: cfg.id,
                        warna: cfg.color,
                        label: cfg.nama,
                        simbol: cfg.text,
                    });

                    if (selectedIdnya.endsWith("C") && cfg.text !== "") {
                        $("#text-" + idGigi).html(cfg.text);
                    } else {
                        selectedGigi.attr({ fill: cfg.color });
                    }

                    selectedInput.val(index);
                }

                $("#m-gigi-selected").modal("hide");
                selectedGigi = selectedInput = selectedIdnya = null;
            });
    }

    function loadDiv(obj) {
        var total = obj.config.length;
        var div1 = $("#div-slot1").html("");

        for (let i = 0; i < total; i++) {
            var kol = div1;

            var btn = $("<span>", {
                class: "btn-c m-1",
                accesskey: i, // Tambahkan accesskey untuk indeks konfigurasi
                style: "border: 1px solid #ccc; padding: 4px 8px; cursor: pointer; display: inline-block; margin-bottom: 5px;",
            });

            $("<span>", { text: obj.config[i].nama }).appendTo(btn);
            if (obj.config[i].text != "") {
                // Teks untuk kondisi gigi (UE, PE, dll.)
                $("<span>", {
                    html: obj.config[i].text,
                    style: "margin-left: 5px;",
                }).appendTo(btn);
                btn.css(
                    "background-color",
                    ["white", "#ffffff"].includes(
                        String(obj.config[i].color).toLowerCase(),
                    )
                        ? "#f8f9fa"
                        : obj.config[i].color,
                );
            } else {
                $("<span>", {
                    style:
                        "background-color: " +
                        obj.config[i].color +
                        "; width: 20px; height: 20px; display: inline-block; margin-left: 5px; border: 1px solid #000;",
                }).appendTo(btn);
            }

            btn.appendTo(kol);
        }
    }

    function loadGigiFromData(savedData) {
        if (!Array.isArray(savedData) || savedData.length === 0) {
            console.warn("⚠️ Tidak ada data gigi untuk dimuat.");
            return;
        }

        // Kosongkan data sebelumnya agar sinkron
        selectedData = [];

        savedData.forEach((item) => {
            const idGigi = item.idGigi; // Contoh: "18"

            const inputId = `#gigi-${item.bagian}`;
            const vectorId = `#v-${item.bagian}`;

            // Ambil index config yang disimpan (misal 0-14)
            const index = item[`val`]; // value yang tersimpan per bagian

            // Set warna bagian

            if (item.bagian.endsWith("C") && item.simbol) {
                $("#text-" + idGigi).html(item.simbol);
            } else {
                $(vectorId).attr({ fill: item.warna });
            }

            // Set value input hidden-nya
            $(inputId).val(index);

            selectedData.push({
                idGigi: idGigi,
                bagian: item.bagian,
                id: item.id,
                warna: item.warna,
                label: item.label,
                simbol: item.simbol,
            });
        });
    }
}

function resetAllGigi() {
    // Hapus semua data terpilih
    selectedData = [];

    // Reset warna tiap bagian gigi
    $(".gigi").each(function () {
        $(this).attr({ fill: "white" });
    });

    // Kosongkan semua input hidden terkait gigi
    $("input[id^='gigi-']").each(function () {
        $(this).val(0);
    });

    // Hapus semua simbol teks (UE, PE, A, dsb)
    $("[id^='text-']").each(function () {
        $(this).html("");
    });
}
