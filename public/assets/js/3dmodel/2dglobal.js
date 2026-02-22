var selectedData = [];

function load2DGlobal(curData = []) {
    let dataJson = {
        Default_dent: [],
        config: [
            { clear: "1", nama: "Bersih", color: "white", text: "" },
            { clear: "0", nama: "Belum Erupsi", color: "white", text: "UE" },
            { clear: "0", nama: "Erupsi Sebagian", color: "white", text: "PE" },
            {
                clear: "0",
                nama: "Anomali Bentuk",
                color: "white",
                text: "&nbsp;A",
            },
            { clear: "0", nama: "Sisa Akar", color: "white", text: "&nbsp;√" },
            {
                clear: "0",
                nama: "Gigi Hilang",
                color: "white",
                text: "&nbsp;X",
            },
            { clear: "0", nama: "Jembatan", color: "white", text: "__" },
            {
                clear: "0",
                nama: "Gigi Tiruan Lepas",
                color: "white",
                text: "--",
            },
            { clear: "0", nama: "Non Vital", color: "white", text: "&nbsp;/" },
            { clear: "0", nama: "Karies", color: "grey", text: "" },
            { clear: "0", nama: "Tambalan Logam", color: "pink", text: "" },
            {
                clear: "0",
                nama: "Tambalan Non Logam",
                color: "cadetblue",
                text: "",
            },
            { clear: "0", nama: "Mahkota Logam", color: "darkgreen", text: "" },
            { clear: "0", nama: "Mahkota Non Logam", color: "cyan", text: "" },
            { clear: "0", nama: "Indikasi Cabut", color: "black", text: "" },
        ],
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
                    obj.config[i].color === "white"
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
