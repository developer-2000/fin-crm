function getDataSocket(data) {
    var data = jQuery.parseJSON(data);
    switch (data.key) {
        case ('getOrderByWeight') :
            renderTable(data);
            break;
    }
}

function renderTable(data) {
    var table = $('.table_weights');
    if (data.data) {
        renderCampaignTitle(data.data, table);

        renderWeightTitle(data.data, table);

        renderValue(data.data);

        deleteWeightTitle(data.data, table);

        deleteCampaign();
    }
}

function renderCampaignTitle(data, table) {
    $.each(data, function (id, value) {
        for (var id_campaign in value) {
            var obj = value[id_campaign];
            var procCampaign = $('#proc_campaign_' + obj.id_campaign);
            if (!procCampaign.length) {
                var campaign = $('#campaign_' + obj.id_campaign);
                var html = '<tr class="campaigns" id="proc_campaign_' + obj.id_campaign + '" data-id="' + obj.id_campaign + '">' +
                    '<td>' + campaign.text() + '</td>' +
                    '</tr>'
                table.find('tbody').append(html);
            }
        }
    });
}

function renderWeightTitle(data, table) {
    $.each(data, function(id, value) {
        if (!table.find('#weight_' + id).length) {
            var html = '<th class="text-center" id="weight_' + id + '" data-id="' + id + '">' + id + '</th>';
            var lastWeight = $('#weight_' + (parseInt(id) + 1));
            if (lastWeight.length) {
                lastWeight.before(html);
            } else {
                table.find('thead tr').append(html);
            }
        }
    })
}

function renderValue(data) {
    $.each($('.campaigns'), function (id, campaign) {
        var id_campaign = $(campaign).attr('data-id');
        var countEmptyValues = [];
        $.each(data, function(id, value) {
            var html = '<td id="' + id_campaign + '_' + id + '" class="text-center weight_' + id + '">';
            var count = 0;
            if (value[id_campaign]) {
                count = value[id_campaign].quantity;
            } else {
                countEmptyValues.push(1);
            }
            html += count + "</td>";
            if ($('#' + id_campaign + '_' + id ).length) {
                if ($('#' + id_campaign + '_' + id ).text() != count) {
                    var color = "#56FF7E";
                    if ($('#' + id_campaign + '_' + id ).text() < count) {
                        color = "#FF7A62";
                    }
                    $('#' + id_campaign + '_' + id ).text(count);
                    $('#' + id_campaign + '_' + id ).css('background-color', color);
                    setTimeout(function () {
                        $('#' + id_campaign + '_' + id ).css('background-color', 'transparent');
                    },500)
                }

            } else {
                var lastWeight = $('#' + id_campaign + '_' + (parseInt(id) + 1));
                if (lastWeight.length) {
                    lastWeight.before(html);
                } else {
                    $(campaign).append(html);
                }
                $('#' + id_campaign + '_' + id ).css('background-color', '#FF7A62');
                setTimeout(function () {
                    $('#' + id_campaign + '_' + id ).css('background-color', 'transparent');
                },500)
            }
        });

        if (countEmptyValues.length == data.length) {
            $(campaign).remove();
        }

    })
}


function deleteWeightTitle(data, table) {
    var weightInTable = table.find('th');
    if (weightInTable.length - 1 != Object.keys(data).length) {
        $.each(weightInTable, function (id, value) {
            var weight = $(value).text();
            if (!data[weight]) {
                $('#weight_' + weight).remove();
                $('.weight_' + weight).remove();
            }
        })
    }
}

function deleteCampaign() {
    var tr = $('tr');
    for (var j = 0; j > tr.length; j++){
        var td = tr.find('td');
        var emptyValue = [];
        for (var i = 1; i < td.length; i++) {
            if (!td.eq(i).text()) {
                emptyValue.push(i);
            }
        }

        if (emptyValue.length == td.length - 1) {
            tr.eq(j).remove;
        }

    }
}
