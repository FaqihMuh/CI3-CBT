function loadJenjang(id) {
    $('#jenjang option').remove();
    $.getJSON(base_url+'jenjangmapel/getJenjangId/' + id, function (data) {
        console.log(data);
        let opsi;
        $.each(data, function (key, val) {
            opsi = `
                    <option value="${val.id_jenjang}">${val.nama_jenjang}</option>
                `;
            $('#jenjang').append(opsi);
        });
    });
}
function loadKelas(id) {
    $('#kelas option').remove();
    $.getJSON(base_url+'jenjangmapel/getKelasId/' + id, function (data) {
        console.log(data);
        let opsi;
        $.each(data, function (key, val) {
            opsi = `
                    <option value="${val.id_kelas}">${val.nama_kelas}</option>
                `;
            $('#kelas').append(opsi);
        });
    });
}

$(document).ready(function () {
    $('[name="mapel_id"]').on('change', function () {
        alert('yaa');
        loadJenjang($(this).val());
    });
    $('[id="jenjang"]').on('change', function () {
        alert('okok');
        loadKelas($('[id="jenjang"]').val());
    });

    $('form#jenjangmapel select').on('change', function () {
        $(this).closest('.form-group').removeClass('has-error');
        $(this).nextAll('.help-block').eq(0).text('');
    });

    $('form#jenjangmapel').on('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var btn = $('#submit');
        btn.attr('disabled', 'disabled').text('Wait...');

        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            method: 'POST',
            success: function (data) {
                btn.removeAttr('disabled').text('Simpan');
                console.log(data);
                if (data.status) {
                    Swal({
                        "title": "Sukses",
                        "text": "Data Berhasil disimpan",
                        "type": "success"
                    }).then((result) => {
                        if (result.value) {
                            window.location.href = base_url+'jenjangmapel';
                        }
                    });
                } else {
                    if (data.errors) {
                        let j;
                        $.each(data.errors, function (key, val) {
                            j = $('[name="' + key + '"]');
                            j.closest('.form-group').addClass('has-error');
                            j.nextAll('.help-block').eq(0).text(val);
                            if (val == '') {
                                j.parent().addClass('has-error');
                                j.nextAll('.help-block').eq(0).text('');
                            }
                        });
                    }
                }
            }
        });
    });
});