function getFormDataJson(form) {
    let jsonData = {};
    let formElements = $(form).find(':input[name]');

    formElements.each(function () {
        let name = $(this).attr('name');
        let value = $(this).val();

        if (Array.isArray(value) && $(this).data('type') === 'number') {
            let valuesFormatted = [];
            $.each(value, function (key, val) {
                valuesFormatted.push(Number(val));
            });

            jsonData[name] = valuesFormatted;

            return;
        } else if ($(this).data('type') === 'number') {
            value = Number(value);
        } else if ($(this).data('type') === 'float') {
            value = formatMoneyToFloat(value);
        }

        jsonData[name] = value;
    });


    return jsonData;
}

function formatMoneyToFloat(money)
{
    if (typeof money !== "string") return 0;

    return parseFloat(
        money.replace(/[^0-9,-]/g, '')
            .replace(',', '.')
    );
}

function formatToBR(value)
{
    return value.toFixed(2).replace('.', ',');
}
