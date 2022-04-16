function renderScannedSitesList(sites) {
    $('.sites').show(300)
    let iterator = 1;
    let tbody = $('#scanned-sites-tbody')
    $.each(sites, function (key, value) {
        let btnGroup =
            "<div class='btn-group'>" +
            "        <button type='button' data-toggle='dropdown' aria-expanded='false' class='text-dark btn btn-tool dropdown-toggle'>" +
            "            <i class='fas fa-external-link-alt'></i>" +
            "        </button> " +
            "       <div role='menu' class='dropdown-menu dropdown-menu-left'>" +
            "            <a target='_blank' class='dropdown-item' href='" + value['site'] + "'>" +
            "                <i class='fas fa-external-link-alt'></i> Перейти на посадочную страницу</a>" +
            "            <span class='dropdown-item add-in-ignored-domains' style='cursor: pointer'" +
            "                  data-target='" + value['site'] + "'>" +
            "                <i class='fas fa-external-link-alt'></i>" +
            "                Добавить в игнорируемые домены" +
            "            </span>" +
            "        </div>" +
            "</div>";
        let noTop = ''
        let span = ''
        let background
        let warning = value['danger']
            ? "<td class='bg-warning'> Не удалось получить данные со страницы </td>"
            : "<td> Страница успешно проанализирована </td>"
        if (value['mainPage']) {
            if (!value['inRelevance']) {
                noTop = "<span class='text-muted'>(сайт не попал в топ)</span>"
            }
            background = 'background: #4eb767c4;'
        } else {
            background = ''
        }
        if (value['ignored']) {
            span = "<span class='text-muted'>(игнорируемый домен)</span>"
        }
        tbody.append(
            "<tr class='render'>" +
            "<td data-order='" + iterator + "'>" + iterator + "</td>" +
            "<td style='" + background + "max-width: 450px;'>" + value['site'] + noTop + span + btnGroup + "</td>" +
            "<td>" + value['coverage'] + "% </td>" +
            "<td data-order='" + value['coverageTf'] + "'>" + value['coverageTf'] + "% </td>" +
            "<td data-order='" + value['width'] + "'>" + value['width'] + "</td>" +
            "<td data-order='" + value['density'] + "'>" + value['density'] + "<span class='text-muted'>(" + value['densityPoints'] + ")</span></td>" +
            "<td data-order='" + value['density100'] + "'>" + value['density100'] + "<span class='text-muted'>(" + value['density100Points'] + ")</span></td>" +
            "<td data-order='" + value['density200'] + "'>" + value['density200'] + "<span class='text-muted'>(" + value['density200Points'] + ")</span></td>" +
            warning +
            "</tr>"
        )
        iterator++
    });

    $(document).ready(function () {
        $('#scaned-sites').DataTable({
            "order": [[0, "asc"]],
            "pageLength": 50,
            "searching": true,
        });
    });
    setTimeout(() => {
        $('#scaned-sites').wrap("<div style='width: 100%; overflow-x: scroll; max-height:90vh;'></div>")
    }, 2000)

    $('.add-in-ignored-domains').click(function () {
        let url = new URL($(this).attr('data-target'))
        let textarea = $('.form-control.ignoredDomains')
        let string = textarea.val()
        if (!string.includes(url.hostname)) {
            if (textarea.val().slice(-1) === "\n") {
                textarea.val(textarea.val() + url.hostname + "\n")
            } else {
                textarea.val(textarea.val() + "\n" + url.hostname + "\n")
            }
        }
    });
}

