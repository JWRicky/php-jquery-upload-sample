<form id="form" method="post" enctype="multipart/form-data">
    <div class="photo-content">

        <?php for ($i = 1; $i <= 8; $i++) { ?>
            <div class="wrap-content">
                <div class="wrap-photo">
                    <p class="photo-title">画像<?php echo $i; ?></p>
                    <div class="button-items">
                        <div class="button-items--inner">
                            <div>
                                <label for="img-upload<?php echo $i; ?>">
                                    <img class="button-items--inner_edit"
                                        src="http://localhost/img/edit.png"
                                        alt="" />
                                    <input name="file[]" accept="image/*" type="file" id="img-upload<?php echo $i; ?>">
                                </label>
                            </div>
                            <div>
                                <img id="delete<?php echo $i; ?>" class="button-items--inner_delete"
                                    src="http://localhost/img/delete.png"
                                    alt="" />
                            </div>
                        </div>
                    </div>
                    <img id="img<?php echo $i; ?>" class="photo"
                        src="http://localhost/img/no_image.png" alt="" />
                </div>
                <div class="wrap-content--title">
                    <p class="photo-title">タイトル</p>
                    <div>
                        <input type="text" id="title-input<?php echo $i; ?>" name="title[]" value="" />
                    </div>
                </div>
            </div>
        <?php } ?>


    </div>

    <div class="form-button-photo">
        <input class="form-send-button" type="button" id="send-button" name="upload" value="変更する">
    </div>

</form>


<script>

    const fileList = [];
    var noImagePath = "http://localhost/img/no_image.png";

    $('.photo').hover(
        function () {
            $(this).css('background', 'rgba(0, 0, 0, 0.5)');
            $('.button-items').css('display', 'block');
        },
        function () {
            $(this).css('background', 'none');
            $('.button-items').css('display', 'none');
        },
    );

    $('.button-items').hover(
        function () {
            $('.photo').css('background', 'rgba(0, 0, 0, 0.5)');
            $(this).css('display', 'block');
        },
        function () {
            $('.photo').css('background', 'none');
            $(this).css('display', 'none');
        },
    );

    for (let i = 1; i <= 8; i++) {

        $('#img-upload' + i).on('change', function (e) {
            var reader = new FileReader();
            reader.onload = function (e) {
                console.log();
                $('#img' + i).attr('src', e.target.result);
                $('#img' + i).css('border', '3px dashed gray');
            }
            reader.readAsDataURL(e.target.files[0]);
        });

        $('#delete' + i).on('click', function (e) {
            $('#img' + i).attr('src', noImagePath);
            $('#img' + i).css('border', '');
            $('#img-upload' + i).val('');
        });
    }




    $(function () {

        const dir = "./logic/update.php";

        $.ajax({
            type: "POST",
            url: dir,
            dataType: 'json',
        }).done(function (data) {

            var formattedData = JSON.stringify(data);
            var values = JSON.parse(formattedData);

            var ids = [];
            var paths = [];
            var titles = [];

            for (let i = 0; i < values.length; i++) {

                ids.push(values[i]['id']);
                paths.push("http://localhost" + values[i]['path']);
                titles.push(values[i]['titles']);

                $('#img' + (i + 1)).attr('src', values[i]['path']);
                $('#title-input' + (i + 1)).val(values[i]['title']);
            }

            console.log(values);



        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log('failed');
        }).always(function (jqXHR, textStatus) {
            console.log('Request completed: ' + textStatus);
        });

        $('#send-button').on('click', function () {


            var form = $('form').get()[0];
            var formData = new FormData(form);
            console.log(formData);

            const targetUrl = "./logic/update.php";

            $.ajax({
                url: targetUrl,
                type: 'POST',
                dataType: 'text',
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            }).done(function (data) {

                var formattedData = JSON.stringify(data);
                var values = JSON.parse(formattedData);



                console.log(values);
            }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("failed");
            });


        });
    });

</script>