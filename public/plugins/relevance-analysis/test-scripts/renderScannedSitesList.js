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
            "           <span class='dropdown-item remove-from-ignored-domains' style='cursor: pointer'" +
            "                 data-target='" + value['site'] + "'>" +
            "               <i class='fas fa-external-link-alt'></i>" +
            "               Исключить из игнорируемых доменов" +
            "           </span>" +
            "        </div>" +
            "</div>";

        let noTop = ''
        let ignorBlock = ''
        let background
        let warning = value['danger']
            ? "<td class='bg-warning'>" +
            "<u data-scroll='#ignoredDomains' class='scroll-to-ignored-list pointer'> Не удалось получить данные со страницы</u> " +
            "</td>"
            : "<td>" +
            "<u data-scroll='#ignoredDomains' class='scroll-to-ignored-list pointer'> Страница успешно проанализирована </u>" +
            "</td>"

        if (value['mainPage']) {
            if (!value['inRelevance']) {
                noTop = "<span class='text-muted'>(сайт не попал в топ)</span>"
            }
            background = 'background: #4eb767c4;'
        } else {
            background = ''
        }

        if (value['ignored']) {
            ignorBlock = "<div class='text-muted'>(игнор)</div>"
        }

        tbody.append(
            "<tr class='render'>" +
            "<td data-order='" + iterator + "'>" + iterator + ignorBlock + "</td>" +
            "<td style='" + background + "max-width: 450px;'>" + value['site'] + noTop + btnGroup + "</td>" +
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
        $('#scaned-sites_length').before(
            "    <div class='d-flex' onclick='showOrHideIgnoreList()'>" +
            "        <div class='__helper-link ui_tooltip_w'>" +
            "            <div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>" +
            "                <input type='checkbox'" +
            "                       class='custom-control-input'" +
            "                       id='showOrHideIgnoredList'" +
            "                       name='noIndex'>" +
            "                <label class='custom-control-label' for='showOrHideIgnoredList'></label>" +
            "            </div>" +
            "        </div>" +
            "        <p>скрыть игнорируемые домены</p>" +
            "    </div>"
        )
    }, 2000)

    $('.add-in-ignored-domains').click(function () {
        let url = new URL($(this).attr('data-target'))
        let textarea = $('.form-control.ignoredDomains')
        let string = textarea.val()
        if (!string.includes(url.hostname)) {
            let domain = (url.hostname).replace('www.', '')

            if (textarea.val().slice(-1) === "\n") {
                textarea.val(textarea.val() + domain + "\n")
            } else {
                textarea.val(textarea.val() + "\n" + domain + "\n")
            }

            let toastr = $('.toast-top-right.success-message.lock-word');
            toastr.show(300)
            $('#lock-word').html('Домен "' + domain + '" добавлен в игнорируемые')
            setTimeout(() => {
                toastr.hide(300)
            }, 3000)
        }
    });


    $('.remove-from-ignored-domains').click(function () {
        let url = new URL($(this).attr('data-target'))
        let textarea = $('.form-control.ignoredDomains')
        let string = textarea.val()
        if (string.includes(url.hostname)) {
            let domain = (url.hostname).replace('www.', '')
            textarea.val(textarea.val().replace(domain, ""))

            let toastr = $('.toast-top-right.success-message.lock-word');
            toastr.show(300)
            $('#lock-word').html('Домен "' + domain + '" удалён из игнорируемых')
            setTimeout(() => {
                toastr.hide(300)
            }, 3000)
        }
    });


    $('.scroll-to-ignored-list').on('click', function () {
        var el = $(this);
        var dest = el.attr('data-scroll');
        $('html').animate({
                scrollTop: $(dest).offset().top
            }, 500
        );
        return false;
    });
}

function showOrHideIgnoreList() {
    $('#showOrHideIgnoredList').click(function () {
        if ($('#ignoredDomainsBlock').is(':visible')) {
            $('#ignoredDomainsBlock').hide(300)
        } else {
            $('#ignoredDomainsBlock').show(300)
        }
    });
}
