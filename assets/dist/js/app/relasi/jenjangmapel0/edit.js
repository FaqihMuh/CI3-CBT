function load_kelas(id) {
    $('#kelas').find('option').not(':first').remove();

    $.ajax({
        url: base_url+'jenjangmapel/kelas_by_jenjang/' + id,
        type: 'GET',
        success: function (data) {
            var option = [];
            for (let i = 0; i < data.length; i++) {
                option.push({
                    id: data[i].id_kelas,
                    text: data[i].nama_kelas
                });
            }
            $('#kelas').select2({
                data: option
            })
        }
    });
}

$(document).ready(function () {
    ajaxcsrf();

    // Load Kelas By jenjang
    $('[id="jenjang"]').on('change', function () {
        alert($('[id="jenjang"]').val());
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