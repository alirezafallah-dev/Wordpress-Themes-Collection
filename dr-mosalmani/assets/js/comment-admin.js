
jQuery(function ($) {

    const phoneBox = $('#comment-phone').closest('.postbox');

    if (!phoneBox.length) {
        return;
    }

    const phone = $('#comment-phone').val();

    const row = $(`
        <tr>
            <th>
                <label for="comment-phone-custom">
                    شماره تماس
                </label>
            </th>

            <td>
                <input
                    type="text"
                    id="comment-phone-custom"
                    class="regular-text"
                    value="${phone}">
            </td>
        </tr>
    `);

    $('#namediv table tbody').append(row);

    $('#comment-phone-custom').on('input', function () {

        $('#comment-phone').val($(this).val());

    });

    phoneBox.hide();

});